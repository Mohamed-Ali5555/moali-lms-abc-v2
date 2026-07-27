<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;
use App\Services\WaPilot\WhatsAppNotifier;

class QuizController extends Controller
{
    /**
     * Check if exam mode (strict mode) is enabled
     */
    private function isExamMode()
    {
        return get_settings('quiz_submission_mode') === 'secure';
    }

    /**
     * Update course progress helper
     */
    private function updateCourseProgress($quiz_id, $user_id, $courseId)
    {
        $watchHistory = DB::table('watch_histories')
            ->where([
                'course_id' => $courseId,
                'student_id' => $user_id,
            ])
            ->first();

        if ($watchHistory) {
            $lessonIds = json_decode($watchHistory->completed_lesson, true);
            $courseProgress = $watchHistory->course_progress;

            if (!is_array($lessonIds)) $lessonIds = [];

            if (!in_array($quiz_id, $lessonIds)) {
                array_push($lessonIds, $quiz_id);
                $totalLesson = DB::table('lessons')->where('course_id', $courseId)->count();
                $courseProgress = (100 / $totalLesson) * count($lessonIds);

                $completedDate = ($courseProgress >= 100 && !$watchHistory->completed_date)
                    ? time()
                    : $watchHistory->completed_date;

                DB::table('watch_histories')
                    ->where('id', $watchHistory->id)
                    ->update([
                        'course_progress' => $courseProgress,
                        'completed_lesson' => json_encode($lessonIds),
                        'completed_date' => $completedDate,
                    ]);
            }
        } else {
            DB::table('watch_histories')->insert([
                'course_id' => $courseId,
                'student_id' => $user_id,
                'completed_lesson' => json_encode([$quiz_id]),
                'course_progress' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Start quiz in Exam Mode - creates a COMPLETED submission immediately
     * Every answer will be saved and graded instantly
     */
    public function start_quiz(Request $request)
    {
        $quiz_id = $request->quiz_id;
        $user_id = auth()->user()->id;

        // Check retake limit
        $retake = Lesson::where('id', $quiz_id)->value('retake');
        $completedSubmits = QuizSubmission::where('quiz_id', $quiz_id)
            ->where('user_id', $user_id)
            ->where('status', 'completed')
            ->count();

        if ($completedSubmits >= $retake) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Attempt has been over.')
            ]);
        }

        // In Exam Mode: Create submission with status = completed immediately
        // This means if student leaves, the exam is already "submitted"
        $submission = new QuizSubmission();
        $submission->quiz_id = $quiz_id;
        $submission->user_id = $user_id;
        $submission->status = 'completed'; // Immediately completed!
        $submission->submits = json_encode([]);
        $submission->correct_answer = null;
        $submission->wrong_answer = null;
        $submission->save();

        // Update course progress
        $lesson = Lesson::where('id', $quiz_id)->first();
        if ($lesson) {
            $this->updateCourseProgress($quiz_id, $user_id, $lesson->course_id);
        }

        return response()->json([
            'success' => true,
            'submission_id' => $submission->id,
            'message' => get_phrase('Quiz started.')
        ]);
    }

    /**
     * Save and grade a single answer immediately (Exam Mode)
     */
    public function save_answer(Request $request)
    {
        $submission_id = $request->submission_id;
        $question_id = $request->question_id;
        $answer = $request->answer;

        $submission = QuizSubmission::where('id', $submission_id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Submission not found.')
            ]);
        }

        // Get the question
        $question = Question::find($question_id);
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Question not found.')
            ]);
        }

        // Get current data
        $submits = json_decode($submission->submits, true) ?? [];
        $correct_answers = json_decode($submission->correct_answer, true) ?? [];
        $wrong_answers = json_decode($submission->wrong_answer, true) ?? [];

        // Remove this question from previous correct/wrong if it was answered before
        $correct_answers = array_values(array_diff($correct_answers, [$question_id]));
        $wrong_answers = array_values(array_diff($wrong_answers, [$question_id]));

        // Save the answer
        $submits[$question_id] = $answer;

        // Grade this question immediately
        $correct_answer = json_decode($question->answer, true);
        $isCorrect = false;

        if ($question->type == 'mcq') {
            if (is_array($answer) && is_array($correct_answer)) {
                $isCorrect = empty(array_diff($correct_answer, $answer)) && empty(array_diff($answer, $correct_answer));
            }
        } elseif ($question->type == 'fill_blanks') {
            if (is_array($answer) && is_array($correct_answer)) {
                $isCorrect = count($correct_answer) === count($answer);
                if ($isCorrect) {
                    for ($i = 0; $i < count($correct_answer); $i++) {
                        if (strtolower($correct_answer[$i]) != strtolower($answer[$i] ?? '')) {
                            $isCorrect = false;
                            break;
                        }
                    }
                }
            }
        } elseif ($question->type == 'true_false') {
            $isCorrect = strtolower(json_encode($correct_answer)) == strtolower($answer);
        }

        // Add to correct or wrong
        if ($isCorrect) {
            $correct_answers[] = $question_id;
        } else {
            $wrong_answers[] = $question_id;
        }

        // Update submission
        $submission->submits = json_encode($submits);
        $submission->correct_answer = !empty($correct_answers) ? json_encode($correct_answers) : null;
        $submission->wrong_answer = !empty($wrong_answers) ? json_encode($wrong_answers) : null;
        $submission->save();

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'message' => get_phrase('Answer saved and graded.')
        ]);
    }

    public function quiz_submit(Request $request)
    {
        $quiz_id = $request->quiz_id;
        $user_id = auth()->user()->id;

        $retake = Lesson::where('id', $quiz_id)->value('retake');
        $completedSubmits = QuizSubmission::where('quiz_id', $quiz_id)
            ->where('user_id', $user_id)
            ->where('status', 'completed')
            ->count();

        if ($completedSubmits >= $retake) {
            Session::flash('warning', get_phrase('Attempt has been over.'));
            return redirect()->back();
        }

        // Check if secure mode and there's an in_progress submission
        $existingSubmission = null;
        if ($this->isExamMode()) {
            $existingSubmission = QuizSubmission::where('quiz_id', $quiz_id)
                ->where('user_id', $user_id)
                ->where('status', 'in_progress')
                ->first();
        }

        $inputs  = collect($request->all());
        $inputs->pull('quiz_id');
        $inputs->forget(['_token', 'quiz_id', 'submission_id']);

        $submits = $inputs->whereNotNull();
        foreach ($submits as $key => $submit) {
            if (is_string($submit) && ($submit != 'true' && $submit != 'false')) {
                $submits[$key] = array_column(json_decode($submit), 'value');
            }
        }

        // If secure mode, merge with saved answers
        if ($existingSubmission) {
            $savedSubmits = json_decode($existingSubmission->submits, true) ?? [];
            // Merge: form submits override saved submits
            $submits = collect(array_merge($savedSubmits, $submits->toArray()));
        }

        $question_ids      = $submits->keys();
        $questions         = Question::whereIn('id', $question_ids)->get();

        $right_answers = $wrong_answers = [];
        foreach ($questions as $question) {

            $correct_answer = json_decode($question->answer, true);
            $submitted      = $submits[$question->id] ?? null;

            if ($submitted === null) continue;

            if ($question->type == 'mcq') {
                $isCorrect = empty(array_diff($correct_answer, $submitted)) && empty(array_diff($submitted, $correct_answer));
            } elseif ($question->type == 'fill_blanks') {
                $isCorrect = count($correct_answer) === count($submitted);

                if ($isCorrect) {
                    for ($i = 0; $i < count($correct_answer); $i++) {
                        if (strtolower($correct_answer[$i]) != strtolower($submitted[$i])) {
                            $isCorrect = false;
                            break;
                        }
                    }
                } else {
                    $isCorrect = false;
                }
            } elseif ($question->type == 'true_false') {
                $isCorrect = strtolower(json_encode($correct_answer)) == strtolower($submitted);
            }

            $isCorrect ? $right_answers[] = $question->id : $wrong_answers[] = $question->id;
        }

        // Update existing or create new submission
        $submissionId = null;
        if ($existingSubmission) {
            $existingSubmission->correct_answer = $right_answers ? json_encode($right_answers) : null;
            $existingSubmission->wrong_answer = $wrong_answers ? json_encode($wrong_answers) : null;
            $existingSubmission->submits = $submits->count() > 0 ? json_encode($submits->toArray()) : null;
            $existingSubmission->status = 'completed';
            $existingSubmission->save();
            $submissionId = $existingSubmission->id;
        } else {
            $submission = QuizSubmission::create([
                'quiz_id'        => $quiz_id,
                'user_id'        => $user_id,
                'correct_answer' => $right_answers ? json_encode($right_answers) : null,
                'wrong_answer'   => $wrong_answers ? json_encode($wrong_answers) : null,
                'submits'        => $submits->count() > 0 ? json_encode($submits->toArray()) : null,
                'status'         => 'completed',
            ]);
            $submissionId = $submission->id;
        }

        $lessons = Lesson::where('id', $quiz_id)->first();
        $courseId = $lessons->course_id;

        $submission = QuizSubmission::find($submissionId);
        if ($submission && $lessons) {
            app(WhatsAppNotifier::class)->notifyQuizResult($submission, $lessons, auth()->user());
        }

        // Update course progress if the lesson is completed
        $watchHistory = DB::table('watch_histories')
            ->where([
                'course_id' => $courseId,
                'student_id' => $user_id,
            ])
            ->first();

        if ($watchHistory) {
            $lessonIds = json_decode($watchHistory->completed_lesson, true);
            $courseProgress = $watchHistory->course_progress;

            if (!is_array($lessonIds)) $lessonIds = [];

            if (!in_array($quiz_id, $lessonIds)) {
                array_push($lessonIds, $quiz_id);
                $totalLesson = DB::table('lessons')->where('course_id', $courseId)->count();
                $courseProgress = (100 / $totalLesson) * count($lessonIds);

                $completedDate = ($courseProgress >= 100 && !$watchHistory->completed_date)
                    ? time()
                    : $watchHistory->completed_date;

                DB::table('watch_histories')
                    ->where('id', $watchHistory->id)
                    ->update([
                        'course_progress' => $courseProgress,
                        'completed_lesson' => json_encode($lessonIds),
                        'completed_date' => $completedDate,
                    ]);
            }
        } else {
            DB::table('watch_histories')->insert([
                'course_id' => $courseId,
                'student_id' => $user_id,
                'completed_lesson' => json_encode([$quiz_id]),
                'course_progress' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Session::flash('success', get_phrase('Your answers have been submitted.'));
        Session::flash('quiz_submitted_id', $submissionId);
        return redirect()->back();
    }

    public function load_result(Request $request)
    {
        $quiz = Lesson::where('id', $request->quiz_id)->first();
        $questions = Question::where('quiz_id', $request->quiz_id)->get();
        $result = QuizSubmission::where('id', $request->submit_id)
            ->where('quiz_id', $request->quiz_id)
            ->where('user_id', auth()->user()->id)
            ->first();

        $page_data['quiz'] = $quiz;
        $page_data['questions'] = $questions;
        $page_data['result'] = $result;
        $page_data['ranking'] = $this->buildQuizRanking($quiz, $questions, $result);

        return view('course_player.quiz.result', $page_data);
    }

    /**
     * Rank students by their best completed attempt score on this quiz.
     */
    private function buildQuizRanking($quiz, $questions, $currentResult): array
    {
        $totalQuestions = max($questions->count(), 1);
        $totalMark = (float) ($quiz->total_mark ?? 0);
        $markPerQuestion = $totalMark > 0 ? ($totalMark / $totalQuestions) : 1;

        $currentCorrect = count(json_decode($currentResult->correct_answer ?? '[]', true) ?: []);
        $currentScore = round($currentCorrect * $markPerQuestion, 1);

        $submissions = QuizSubmission::query()
            ->where('quiz_id', $quiz->id)
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn('quiz_submissions', 'status'),
                fn ($q) => $q->where('status', 'completed')
            )
            ->get(['id', 'user_id', 'correct_answer']);

        $bestByUser = [];
        foreach ($submissions as $submission) {
            $correctCount = count(json_decode($submission->correct_answer ?? '[]', true) ?: []);
            $score = round($correctCount * $markPerQuestion, 1);
            $userId = (int) $submission->user_id;

            if (! isset($bestByUser[$userId]) || $score > $bestByUser[$userId]['score']) {
                $bestByUser[$userId] = [
                    'user_id' => $userId,
                    'score' => $score,
                    'correct' => $correctCount,
                ];
            }
        }

        uasort($bestByUser, function ($a, $b) {
            if ($a['score'] === $b['score']) {
                return $a['user_id'] <=> $b['user_id'];
            }
            return $b['score'] <=> $a['score'];
        });

        $ranked = array_values($bestByUser);
        $participants = count($ranked);
        $rank = null;

        foreach ($ranked as $index => $row) {
            if ((int) $row['user_id'] === (int) auth()->id()) {
                $rank = $index + 1;
                break;
            }
        }

        if ($rank === null && $currentResult) {
            $rank = $participants + 1;
            $participants++;
        }

        $userIds = collect($ranked)->take(5)->pluck('user_id')->all();
        if (! in_array(auth()->id(), $userIds, true)) {
            $userIds[] = auth()->id();
        }

        $users = \App\Models\User::whereIn('id', $userIds)
            ->get(['id', 'name', 'photo'])
            ->keyBy('id');

        $leaderboard = [];
        foreach (array_slice($ranked, 0, 5) as $index => $row) {
            $user = $users->get($row['user_id']);
            $leaderboard[] = [
                'rank' => $index + 1,
                'user_id' => $row['user_id'],
                'name' => $user->name ?? get_phrase('طالب'),
                'photo' => $user->photo ?? null,
                'score' => $row['score'],
                'is_me' => (int) $row['user_id'] === (int) auth()->id(),
            ];
        }

        $percentile = ($participants > 1 && $rank)
            ? max(0, round((($participants - $rank) / ($participants - 1)) * 100))
            : ($rank === 1 ? 100 : 0);

        return [
            'rank' => $rank,
            'participants' => $participants,
            'score' => $currentScore,
            'percentile' => $percentile,
            'leaderboard' => $leaderboard,
            'mark_per_question' => $markPerQuestion,
        ];
    }

    public function load_questions(Request $request)
    {
        $page_data['quiz']      = Lesson::where('id', $request->quiz_id)->first();
        $page_data['questions'] = Question::where('quiz_id', $request->quiz_id)->get();
        $page_data['submits']   = QuizSubmission::where('quiz_id', $request->quiz_id)
            ->where('user_id', auth()->user()->id)
            ->where('status', 'completed')
            ->get();
        
        return view('course_player.quiz.questions', $page_data);
    }
}

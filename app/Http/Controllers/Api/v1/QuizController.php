<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WaPilot\WhatsAppNotifier;

class QuizController extends Controller
{
    private function isExamMode(): bool
    {
        return get_settings('quiz_submission_mode') === 'secure';
    }

    private function updateCourseProgress($quiz_id, $user_id, $courseId): void
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

            if (!is_array($lessonIds)) {
                $lessonIds = [];
            }

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
     * POST /api/v1/quiz/{quiz_id}/start
     */
    public function startQuiz(Request $request, $quizId)
    {
        $quiz_id = $quizId;
        $user_id = auth('sanctum')->user()->id;

        $retake = Lesson::where('id', $quiz_id)->value('retake');
        $completedSubmits = QuizSubmission::where('quiz_id', $quiz_id)
            ->where('user_id', $user_id)
            ->where('status', 'completed')
            ->count();

        if ($completedSubmits >= $retake) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Attempt has been over.'),
            ], 400);
        }

        $submission = new QuizSubmission();
        $submission->quiz_id = $quiz_id;
        $submission->user_id = $user_id;
        $submission->status = 'completed';
        $submission->submits = json_encode([]);
        $submission->correct_answer = null;
        $submission->wrong_answer = null;
        $submission->save();

        $lesson = Lesson::where('id', $quiz_id)->first();
        if ($lesson) {
            $this->updateCourseProgress($quiz_id, $user_id, $lesson->course_id);
        }

        return response()->json([
            'success' => true,
            'submission_id' => $submission->id,
            'message' => get_phrase('Quiz started.'),
        ]);
    }

    /**
     * Save and grade a single answer immediately (Exam Mode)
     * POST /api/v1/quiz/save-answer
     */
    public function saveAnswer(Request $request)
    {
        $submission_id = $request->submission_id;
        $question_id = $request->question_id;
        $answer = $request->answer;

        $submission = QuizSubmission::where('id', $submission_id)
            ->where('user_id', auth('sanctum')->user()->id)
            ->first();

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Submission not found.'),
            ], 404);
        }

        $question = Question::find($question_id);
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Question not found.'),
            ], 404);
        }

        $submits = json_decode($submission->submits, true) ?? [];
        $correct_answers = json_decode($submission->correct_answer, true) ?? [];
        $wrong_answers = json_decode($submission->wrong_answer, true) ?? [];

        $correct_answers = array_values(array_diff($correct_answers, [$question_id]));
        $wrong_answers = array_values(array_diff($wrong_answers, [$question_id]));

        $submits[$question_id] = $answer;

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

        if ($isCorrect) {
            $correct_answers[] = $question_id;
        } else {
            $wrong_answers[] = $question_id;
        }

        $submission->submits = json_encode($submits);
        $submission->correct_answer = !empty($correct_answers) ? json_encode($correct_answers) : null;
        $submission->wrong_answer = !empty($wrong_answers) ? json_encode($wrong_answers) : null;
        $submission->save();

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'message' => get_phrase('Answer saved and graded.'),
        ]);
    }

    /**
     * Submit quiz answers
     * POST /api/v1/quiz/{quiz_id}/submit
     */
    public function submitQuiz(Request $request, $quizId)
    {
        $quiz_id = $quizId;
        $user_id = auth('sanctum')->user()->id;

        $retake = Lesson::where('id', $quiz_id)->value('retake');
        $completedSubmits = QuizSubmission::where('quiz_id', $quiz_id)
            ->where('user_id', $user_id)
            ->where('status', 'completed')
            ->count();

        if ($completedSubmits >= $retake) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Attempt has been over.'),
            ], 400);
        }

        $existingSubmission = null;
        if ($this->isExamMode()) {
            $existingSubmission = QuizSubmission::where('quiz_id', $quiz_id)
                ->where('user_id', $user_id)
                ->where('status', 'in_progress')
                ->first();
        }

        $inputs = collect($request->all());
        $inputs->pull('quiz_id');
        $inputs->forget(['_token', 'quiz_id', 'submission_id', 'answers']);

        if ($request->has('answers') && is_array($request->answers)) {
            $inputs = $inputs->merge($request->answers);
        }

        $submits = $inputs->whereNotNull();
        foreach ($submits as $key => $submit) {
            if (is_string($submit) && ($submit != 'true' && $submit != 'false')) {
                $submits[$key] = array_column(json_decode($submit), 'value');
            }
        }

        if ($existingSubmission) {
            $savedSubmits = json_decode($existingSubmission->submits, true) ?? [];
            $submits = collect(array_merge($savedSubmits, $submits->toArray()));
        }

        $question_ids = $submits->keys();
        $questions = Question::whereIn('id', $question_ids)->get();

        $right_answers = $wrong_answers = [];
        foreach ($questions as $question) {
            $correct_answer = json_decode($question->answer, true);
            $submitted = $submits[$question->id] ?? null;

            if ($submitted === null) {
                continue;
            }

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
            } else {
                $isCorrect = false;
            }

            $isCorrect ? $right_answers[] = $question->id : $wrong_answers[] = $question->id;
        }

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
                'quiz_id' => $quiz_id,
                'user_id' => $user_id,
                'correct_answer' => $right_answers ? json_encode($right_answers) : null,
                'wrong_answer' => $wrong_answers ? json_encode($wrong_answers) : null,
                'submits' => $submits->count() > 0 ? json_encode($submits->toArray()) : null,
                'status' => 'completed',
            ]);
            $submissionId = $submission->id;
        }

        $lessons = Lesson::where('id', $quiz_id)->first();
        if ($lessons) {
            $this->updateCourseProgress($quiz_id, $user_id, $lessons->course_id);
            $submission = QuizSubmission::find($submissionId);
            if ($submission) {
                app(WhatsAppNotifier::class)->notifyQuizResult($submission, $lessons, auth('sanctum')->user());
            }
        }

        return response()->json([
            'success' => true,
            'submission_id' => $submissionId,
            'message' => get_phrase('Your answers have been submitted.'),
        ]);
    }

    /**
     * Load quiz result (same as load_result)
     * GET /api/v1/quiz/{quiz_id}/result/{submission_id}
     */
    public function getQuizResult(Request $request, $quizId, $submissionId)
    {
        $quiz = Lesson::where('id', $quizId)->first();
        $questions = Question::where('quiz_id', $quizId)->get();
        $result = QuizSubmission::where('id', $submissionId)
            ->where('quiz_id', $quizId)
            ->where('user_id', auth('sanctum')->user()->id)
            ->first();

        if (!$quiz || !$result) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Result not found.'),
            ], 404);
        }

        $showAnswer = (bool) $quiz->show_answer;
        $correctAnswers = $result->correct_answer ? json_decode($result->correct_answer, true) : [];
        $wrongAnswers = $result->wrong_answer ? json_decode($result->wrong_answer, true) : [];
        $totalQuestions = max($questions->count(), 1);
        $markPerQuestion = $quiz->total_mark / $totalQuestions;
        $score = count($correctAnswers) * $markPerQuestion;

        $payload = [
            'quiz' => $quiz,
            'result' => $result,
            'show_answer' => $showAnswer,
            'score' => round($score, 1),
            'passed' => $score >= $quiz->pass_mark,
            'correct_count' => count($correctAnswers),
            'wrong_count' => count($wrongAnswers),
        ];

        if ($showAnswer) {
            $payload['questions'] = $questions;
        } else {
            // Hide question details and submitted answers when review is disabled
            $result->makeHidden(['submits', 'correct_answer', 'wrong_answer']);
            $payload['questions'] = [];
            $payload['message'] = get_phrase('Answers review is not available yet. You can only see your score until the instructor enables answer review.');
        }

        return response()->json([
            'success' => true,
            'data' => $payload,
        ]);
    }

    /**
     * Load quiz questions and previous submissions (same as load_questions)
     * GET /api/v1/quiz/{quiz_id}/questions
     */
    public function loadQuestions(Request $request, $quizId)
    {
        $quiz = Lesson::where('id', $quizId)->first();
        $questions = Question::where('quiz_id', $quizId)->get();
        $submits = QuizSubmission::where('quiz_id', $quizId)
            ->where('user_id', auth('sanctum')->user()->id)
            ->where('status', 'completed')
            ->get();

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Quiz not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => $quiz,
                'questions' => $questions,
                'submits' => $submits,
                'exam_mode' => $this->isExamMode(),
            ],
        ]);
    }

    /**
     * Get quiz details with questions (without correct answers)
     * GET /api/v1/quiz/{quiz_id}
     */
    public function getQuiz(Request $request, $quizId)
    {
        $quiz = Lesson::where('id', $quizId)
            ->where('lesson_type', 'quiz')
            ->with(['questions' => function ($query) {
                $query->orderBy('sort', 'asc');
            }])
            ->first();

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Quiz not found.'),
            ], 404);
        }

        $submissions = QuizSubmission::where('quiz_id', $quizId)
            ->where('user_id', auth('sanctum')->user()->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $questions = $quiz->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'title' => $question->title,
                'type' => $question->type,
                'options' => $question->options ? json_decode($question->options, true) : null,
                'sort' => $question->sort,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'description' => $quiz->description,
                    'total_mark' => $quiz->total_mark,
                    'pass_mark' => $quiz->pass_mark,
                    'retake' => $quiz->retake,
                    'duration' => $quiz->duration,
                    'start_time' => $quiz->start_time,
                    'end_time' => $quiz->end_time,
                    'exam_mode' => $this->isExamMode(),
                    'can_retake' => $submissions->count() < $quiz->retake,
                    'attempts_used' => $submissions->count(),
                    'attempts_remaining' => max(0, $quiz->retake - $submissions->count()),
                ],
                'questions' => $questions,
                'previous_submissions' => $submissions,
            ],
        ]);
    }

    /**
     * Get user's quiz submissions
     * GET /api/v1/quiz/{quiz_id}/submissions
     */
    public function getQuizSubmissions(Request $request, $quizId)
    {
        $quiz = Lesson::where('id', $quizId)->first();

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Quiz not found.'),
            ], 404);
        }

        $submissions = QuizSubmission::where('quiz_id', $quizId)
            ->where('user_id', auth('sanctum')->user()->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => $quiz,
                'submissions' => $submissions,
                'total_submissions' => $submissions->count(),
                'remaining_attempts' => max(0, $quiz->retake - $submissions->count()),
            ],
        ]);
    }
}

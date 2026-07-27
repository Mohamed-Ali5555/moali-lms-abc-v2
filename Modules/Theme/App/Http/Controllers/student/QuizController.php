<?php

namespace Modules\Theme\App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class QuizController extends Controller
{
    public function quiz_submit(Request $request)
    {
        $retake = Lesson::where('id', $request->quiz_id)->value('retake');
        $submit = QuizSubmission::where('quiz_id', $request->quiz_id)->where('user_id', auth()->user()->id)->count();
        if ($submit > $retake) {
            Session::flash('warning', get_phrase('Attempt has been over.'));
            return redirect()->back();
        }

        $inputs  = collect($request->all());
        $quiz_id = $inputs->pull('quiz_id');
        $inputs->forget(['_token', 'quiz_id']);

        $submits = $inputs->whereNotNull();
        foreach ($submits as $key => $submit) {
            if (is_string($submit) && ($submit != 'true' && $submit != 'false')) {
                $submits[$key] = array_column(json_decode($submit), 'value');
            }
        }

        $question_ids      = $submits->keys();
        $submitted_answers = $submits->values();
        $questions         = Question::whereIn('id', $question_ids)->get();

        $right_answers = $wrong_answers = [];
        foreach ($questions as $key => $question) {

            $correct_answer = json_decode($question->answer, true);
            $submitted      = $submitted_answers[$key];

            if ($question->type == 'mcq') {
                $isCorrect = empty(array_diff($correct_answer, $submitted));
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

        $data['quiz_id']        = $quiz_id;
        $data['user_id']        = auth()->user()->id;
        $data['correct_answer'] = $right_answers ? json_encode($right_answers) : null;
        $data['wrong_answer']   = $wrong_answers ? json_encode($wrong_answers) : null;
        $data['submits']        = $submits->count() > 0 ? json_encode($submits->toArray()) : null;

        QuizSubmission::insert($data);
        Session::flash('success', get_phrase('Your answers have been submitted.'));
        return redirect()->back();
    }

    public function load_result(Request $request)
    {
        $quiz = Lesson::where('id', $request->quiz_id)->where('status', 1)->first();
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

    private function buildQuizRanking($quiz, $questions, $currentResult): array
    {
        $totalQuestions = max($questions->count(), 1);
        $totalMark = (float) ($quiz->total_mark ?? 0);
        $markPerQuestion = $totalMark > 0 ? ($totalMark / $totalQuestions) : 1;

        $currentCorrect = count(json_decode($currentResult->correct_answer ?? '[]', true) ?: []);
        $currentScore = round($currentCorrect * $markPerQuestion, 1);

        $submissionsQuery = QuizSubmission::query()->where('quiz_id', $quiz->id);
        if (\Illuminate\Support\Facades\Schema::hasColumn('quiz_submissions', 'status')) {
            $submissionsQuery->where('status', 'completed');
        }
        $submissions = $submissionsQuery->get(['id', 'user_id', 'correct_answer']);

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
            ->get();
        return view('course_player.quiz.questions', $page_data);
    }
}

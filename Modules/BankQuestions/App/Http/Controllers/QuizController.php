<?php

namespace Modules\BankQuestions\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Modules\BankQuestions\App\Models\BankQuestionsCategory;
use Modules\BankQuestions\App\Models\BankQuizs;
use Modules\BankQuestions\App\Models\BankQuestions;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = BankQuizs::with(['category.category', 'questions']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('retake', 'LIKE', '%' . $search . '%')
                    ->orWhere('total_mark', 'LIKE', '%' . $search . '%')
                    ->orWhere('pass_mark', 'LIKE', '%' . $search . '%');
            });
        }

        $page_data = [];

        if ($request->filled('category') && $request->category !== 'all') {
            if (str_starts_with($request->category, 'main_')) {
                $mainCategoryId = (int) str_replace('main_', '', $request->category);
                $ids = BankQuestionsCategory::where('category_id', $mainCategoryId)->pluck('id')->toArray();
                $query->whereIn('category_id', $ids);
                $page_data['parent_cat'] = $mainCategoryId;
            } elseif (str_starts_with($request->category, 'sub_')) {
                $subCategoryId = (int) str_replace('sub_', '', $request->category);
                $query->where('category_id', $subCategoryId);
                $page_data['child_cat'] = $subCategoryId;
            }
        }

        $page_data['quizs'] = $query->orderByDesc('id')->paginate(20)->appends($request->query());

        return view('bankquestions::quiz.index', $page_data);
    }

    public function show($id)
    {
        $questions = BankQuestions::whereHas('quizs', function ($query) use ($id) {
            $query->where('quiz_id', $id);
        })->orderBy('sort')->get();

        $quiz = BankQuizs::findOrFail($id);

        return view('bankquestions::quiz.show_questions', compact('questions', 'quiz'));
    }

    public function store(Request $request)
    {
        $validator = $this->quizValidator($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        BankQuizs::create($this->quizPayload($request));

        Session::flash('success', get_phrase('Quiz has been created.'));

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $quiz = BankQuizs::find($id);

        if (! $quiz) {
            Session::flash('error', get_phrase('Data not found.'));

            return redirect()->back();
        }

        $validator = $this->quizValidator($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $quiz->update($this->quizPayload($request));

        Session::flash('success', get_phrase('Quiz has been updated.'));

        return redirect()->back();
    }

    public function result(Request $request)
    {
        $submissions = QuizSubmission::where('quiz_id', $request->quizId)
            ->where('user_id', $request->participant)
            ->get();

        $result[] = '<option>' . get_phrase('Select an option') . '</option>';
        foreach ($submissions as $key => $submission) {
            $result[] = '<option value=' . $submission->id . '>Attempt ' . ++$key . '</option>';
        }

        return $result;
    }

    public function result_preview(Request $request)
    {
        $page_data['quiz'] = Lesson::where('id', $request->quizId)->first();
        $page_data['results'] = QuizSubmission::where('quiz_id', $request->quizId)
            ->where('user_id', $request->participantId)
            ->get();
        $page_data['questions'] = Question::where('quiz_id', $request->quizId)->get();

        return view('admin.quiz_result.preview', $page_data);
    }

    public function destroy($id)
    {
        $quiz = BankQuizs::find($id);

        if (! $quiz) {
            Session::flash('error', get_phrase('quiz not found.'));

            return redirect()->back();
        }

        // Detach bank questions from this quiz only (do not delete shared bank questions)
        $quiz->questions()->detach();
        $quiz->delete();

        Session::flash('success', get_phrase('Quiz has been deleted.'));

        return redirect()->back();
    }

    private function quizValidator(Request $request)
    {
        return Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'required|numeric|exists:bank_quizs_categories,id',
            'hour' => 'nullable|integer|min:0|max:23',
            'minute' => 'nullable|integer|min:0|max:59',
            'second' => 'nullable|integer|min:0|max:59',
            'total_mark' => 'required|numeric|min:1',
            'pass_mark' => 'required|numeric|min:1',
            'retake' => 'required|numeric|min:1',
            'status' => 'nullable|in:0,1',
            'description' => 'nullable|string',
        ])->after(function ($validator) use ($request) {
            $hour = (int) ($request->hour ?? 0);
            $minute = (int) ($request->minute ?? 0);
            $second = (int) ($request->second ?? 0);

            if ($hour === 0 && $minute === 0 && $second === 0) {
                $validator->errors()->add('second', get_phrase('Duration must be greater than 0.'));
            }

            if ((float) $request->pass_mark > (float) $request->total_mark) {
                $validator->errors()->add('pass_mark', get_phrase('The pass mark must be less than or equal to the total mark.'));
            }
        });
    }

    private function quizPayload(Request $request): array
    {
        $hour = (int) ($request->hour ?? 0);
        $minute = (int) ($request->minute ?? 0);
        $second = (int) ($request->second ?? 0);

        return [
            'title' => $request->title,
            'category_id' => $request->category,
            'duration' => sprintf('%02d:%02d:%02d', $hour, $minute, $second),
            'total_mark' => $request->total_mark,
            'pass_mark' => $request->pass_mark,
            'retake' => $request->retake,
            'description' => $request->description,
            'status' => (int) ($request->status ?? 1),
        ];
    }
}

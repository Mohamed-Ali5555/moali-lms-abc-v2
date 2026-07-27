<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Section;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Modules\BankQuestions\App\Models\BankQuizs;
use App\Models\Category;
use App\Services\WaPilot\WhatsAppNotifier;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function store(Request $request)
    {

       $maximum_sort_value = Lesson::where('course_id', $request->course_id)->orderBy('sort', 'desc')->firstOrNew()->sort;

        $validator = Validator::make($request->all(), [
            'title'      => 'required',
            // 'level'      => 'required',
            'section'    => 'required|numeric',
            'second'     => 'max:59',
            'minute'     => 'max:59',
            'hour'       => 'max:23',
            'total_mark' => 'required|numeric',
            'pass_mark'  => 'required|numeric',
            'retake'     => 'required|numeric|min:1',
        ])->after(function ($validator) use ($request) {
            $hour   = $request->hour;
            $minute = $request->minute;
            $second = $request->second;

            if ($hour == 0 && $minute == 0 && $second == 0) {
                $validator->errors()->add('second', 'If hour and minute are 0, second must be greater than 0.');
            } elseif ($hour == 0 && $minute == 0 && $second < 1) {
                $validator->errors()->add('minute', 'If hour is 0, minute must be greater than 0.');
            } elseif ($minute == 0 && $second == 0 && $hour < 1) {
                $validator->errors()->add('hour', 'If minute and second are 0, hour must be greater than 0.');
            }

            if ($request->pass_mark > $request->total_mark) {
                $validator->errors()->add('pass_mark', 'The pass mark must be less than the total mark.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $title = Lesson::join('sections', 'lessons.section_id', 'sections.id')
            ->join('courses', 'sections.course_id', 'courses.id')
            ->where('courses.user_id', auth()->user()->id)
            ->where('lessons.title', $request->title)
            ->first();
        if ($title) {
            Session::flash('error', get_phrase('Title has been taken.'));
            return redirect()->back();
        }

        $data['user_id']     = auth()->user()->id;
        $data['title']         = $request->title;
        $data['course_id']     = $request->course_id;
        $data['section_id']    = $request->section;
        $data['sort']          = $maximum_sort_value + 1;
        $data['total_mark']    = $request->total_mark;
        $data['pass_mark']     = $request->pass_mark;
        $data['retake']        = $request->retake;
        $data['show_answer']   = $request->boolean('show_answer') ? 1 : 0;
        $data['description']   = $request->description;
        $data['start_time']    = $request->filled('start_time') ? $request->start_time : null;
        $data['end_time']      = $request->filled('end_time') ? $request->end_time : null;
        $data['lesson_type']   = 'quiz';
        $data['status']        = 1;
        $data['type']          = $request->type;


        $hour             = $request->hour ?? 0;
        $minute           = $request->minute ?? 0;
        $second           = $request->second ?? 0;
        $data['duration'] = $hour . ':' . $minute . ':' . $second;


        Lesson::insert($data);
        $quiz = Lesson::where('course_id', $data['course_id'])
            ->where('title', $data['title'])
            ->where('lesson_type', 'quiz')
            ->orderByDesc('id')
            ->first();
        if ($quiz) {
            app(WhatsAppNotifier::class)->notifyQuizActivated($quiz);
        }

        Session::flash('success', get_phrase('Quiz or assignment has been created.'));
        return redirect()->back();
    }

    public function choose(Request $request){
        $maximum_sort_value = Lesson::where('course_id', $request->course_id)->orderBy('sort', 'desc')->firstOrNew()->sort;

        $validator = Validator::make($request->all(), [
            'section'        => 'required|numeric',
            'quiz'           => 'required|numeric',
            'start_time'     => 'nullable|date',
            'end_time'       => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->filled('start_time') && $request->filled('end_time')) {
            $startTime = Carbon::parse($request->start_time);
            $endTime   = Carbon::parse($request->end_time);
            if ($endTime <= $startTime) {
                Session::flash('error', get_phrase('يجب تاريخ النهاية يكون بعد تاريخ البداية.'));
                return redirect()->back()->withInput();
            }
        }
        $quiz = BankQuizs::where('id',$request->quiz)->first();

        $type = $request->type === 'quiz' ? 1 : 2;


            // Check if the quiz already exists
            $check_quiz = Lesson::where('bank_id', $quiz->id)
                ->where('section_id', $request->section)
                ->where('type', $type)
                ->exists();

            if ($check_quiz) {
                $message = $type == 1 ? 'تم إضافة الكويز من قبل.' : 'تم إضافة الواجب من قبل.';
                Session::flash('error', get_phrase($message));
                return redirect()->back();
            }


        $lesson = Lesson::create([
            'user_id'      => auth()->user()->id,
            'title'        => $quiz->title,
            // 'category_id'  => $quiz->category_id,
            'bank_id'      => $quiz->id,
            'course_id'    => $request->course_id,

            'total_mark'   => $quiz->total_mark,
            'pass_mark'    => $quiz->pass_mark,
            'retake'       => $quiz->retake,
            'show_answer'  => $request->boolean('show_answer') ? 1 : 0,
            'duration'       => $quiz->duration,
            'description'  => $quiz->description,
            'lesson_type'  => 'quiz',
            'type'         => $type,
            'sort'         => $maximum_sort_value + 1,
            'status'       => 1,
            // 'type'         => 1, // quiz or (2=== assignment)
            'section_id'   => $request->section,
            'start_time'   => $request->filled('start_time') ? $request->start_time : null,
            'end_time'     => $request->filled('end_time') ? $request->end_time : null,
        ]);


        $questions = $quiz->questions()->get();
        foreach ($questions as $question) {


            $answer  = null;
            $options = null;
            if ($question->type == 'mcq') {
                $answer          = $question->answer;
                $options = $question->options;
            } elseif ($question->type == 'fill_blanks') {
                $answers = $question->answer;
                $answer  = $answers;
            } elseif ($request->type == 'true_false') {
                $answer = $question->answer;
            }

            Question::create([
                'title'      => $question->title,
                'quiz_id'    => $lesson->id,
                'type'       => $question->type,
                // 'sort'       => $question->sort,
                'answer'     => $answer,
                'options'  => $options,

            ]);
        }

        app(WhatsAppNotifier::class)->notifyQuizActivated($lesson);

        $message = $type == 1 ? 'تم إضافة الكويز  بنجاح.' : 'تم إضافة الواجب  بنجاح.';
        Session::flash('success', get_phrase($message));
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'      => 'required',
            // 'level'      => 'required',
            'section'    => 'required|numeric',
            'second'     => 'max:59',
            'minute'     => 'max:59',
            'hour'       => 'max:23',
            'total_mark' => 'required|numeric',
            'pass_mark'  => 'required|numeric',
            'retake'     => 'required|numeric|min:1',
        ])->after(function ($validator) use ($request) {
            $hour   = $request->hour;
            $minute = $request->minute;
            $second = $request->second;

            if ($hour == 0 && $minute == 0 && $second == 0) {
                $validator->errors()->add('second', 'If hour and minute are 0, second must be greater than 0.');
            } elseif ($hour == 0 && $minute == 0 && $second < 1) {
                $validator->errors()->add('minute', 'If hour is 0, minute must be greater than 0.');
            } elseif ($minute == 0 && $second == 0 && $hour < 1) {
                $validator->errors()->add('hour', 'If minute and second are 0, hour must be greater than 0.');
            }

            if ($request->pass_mark > $request->total_mark) {
                $validator->errors()->add('pass_mark', 'The pass mark must be less than the total mark.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lesson = Lesson::where('id', $id)->first();

        $title = Lesson::join('sections', 'lessons.section_id', 'sections.id')
            ->join('courses', 'sections.course_id', 'courses.id')
            ->where('lessons.id', '!=', $id)
            ->where('lessons.title', $request->title)->where('type', $request->type)
            ->where('courses.user_id', auth()->user()->id)
            ->first();
        if ($title) {
            Session::flash('error', get_phrase('Title has been taken.'));
            return redirect()->back();
        }

        //    $quiz =  Lesson::where('id', $id)->first();

        if ($request->filled('start_time') && $request->filled('end_time')) {
            $startTime = Carbon::parse($request->start_time);
            $endTime   = Carbon::parse($request->end_time);
            $now = now();

            if ($endTime <= $startTime) {
                Session::flash('error', get_phrase('يجب تاريخ النهاية يكون بعد تاريخ البداية.'));
                return redirect()->back()->withInput();
            }
            if ($endTime->lt($now)) {
                Session::flash('error', get_phrase('لا يجب ان يكون تاريخ النهاية في الماضي'));
                return redirect()->back()->withInput();
            }
        } elseif ($request->filled('end_time') && ! $request->filled('start_time')) {
            $endTime = Carbon::parse($request->end_time);
            if ($endTime->lt(now())) {
                Session::flash('error', get_phrase('لا يجب ان يكون تاريخ النهاية في الماضي'));
                return redirect()->back()->withInput();
            }
        }

        $data['title']         = $request->title;
        $data['section_id']    = $request->section;
        $data['total_mark']    = $request->total_mark;
        $data['pass_mark']     = $request->pass_mark;
        $data['retake']        = $request->retake;
        $data['show_answer']   = $request->boolean('show_answer') ? 1 : 0;
        $data['description']   = $request->description;
        $data['start_time']    = $request->filled('start_time') ? $request->start_time : null;
        $data['end_time']      = $request->filled('end_time') ? $request->end_time : null;
        $data['lesson_type']   = 'quiz';
        $data['status']        = 1;

        $hour             = $request->hour ?? 0;
        $minute           = $request->minute ?? 0;
        $second           = $request->second ?? 0;
        $data['duration'] = $hour . ':' . $minute . ':' . $second;

       $lesson->update($data);

        Session::flash('success', get_phrase('Quiz or assignment has been updated.'));
        return redirect()->back();
    }

    public function result(Request $request)
    {
        $submissions = QuizSubmission::where('quiz_id', $request->quizId)
            ->where('user_id', $request->participant)->get();

        $result[] = "<option>" . get_phrase('Select an option') . "</option>";
        foreach ($submissions as $key => $submission) {
            $result[] = "<option value=" . $submission->id . ">Attempt " . ++$key . "</option>";
        }
        return $result;
    }

    public function result_preview(Request $request)
    {
        $page_data['quiz']      = Lesson::where('id', $request->quizId)->first();
        $page_data['results']   = QuizSubmission::where('quiz_id', $request->quizId)->where('user_id', $request->participantId)->get();
        $page_data['questions'] = Question::where('quiz_id', $request->quizId)->get();
        return view('admin.quiz_result.preview', $page_data);
    }

    public function deleteSubmission($id)
    {
        $submission = QuizSubmission::where('id', $id)->first();
        if (!$submission) {
            return response()->json([
                'status' => false,
                'error' => get_phrase('Submission not found.')
            ], 404);
        }

        $submission->delete();
        
        return response()->json([
            'status' => true,
            'success' => get_phrase('Attempt has been deleted successfully.')
        ]);
    }

    public function examsList(Request $request){


        $category   = 'all';
        $status     = 'all';
        $instructor = 'all';

        $query = Lesson::query();


        if (isset($_GET['search']) && $_GET['search'] != '') {
            $query = $query->where(['lesson_type'=>'quiz','type'=>'1'])

            ->where('title', 'LIKE', '%' . $_GET['search'] . '%')
            ->orWhere('retake','LIKE','%' .request('search') .'%')
            ->orWhere('total_mark','LIKE','%' .request('search') .'%')
            ->orWhere('pass_mark','LIKE','%' .request('search') .'%');

        }


        if (isset($_GET['status']) && $_GET['status'] != '' && $_GET['status'] != 'all') {

            if ($_GET['status'] == '1') {
                $search_status = '1';
                $query         = $query->where('status', $search_status);
            }

            elseif ($_GET['status'] == '0') {
                $search_status = '0';
                $query         = $query->where('status', $search_status);
            }

            $status = $_GET['status'];
        }


        if (isset($request->category) && $request->category != '' && $request->category != 'all') {

            $category_details = Category::where('slug', $request->category)->first();
            if ($category_details->parent_id > 0) {
                $page_data['child_cat'] = $request->category;


                $query   = $query->whereHas('course.category',function($q) use ($category_details){
                    $q->where('id', $category_details->id);
                });

            } else {
                $sub_cat_id   = Category::where('parent_id', $category_details->id)->pluck('id')->toArray();
                $sub_cat_id[] = $category_details->id;

                $query   = $query->whereHas('course.category',function($q) use ($sub_cat_id){
                    $q->whereIn('id', $sub_cat_id);
                });
                $page_data['parent_cat'] = $request->category;
            }
        }



        // selected instructor
        // if (isset($_GET['instructor']) && $_GET['instructor'] != '' && $_GET['instructor'] != 'all') {
        //     $query      = $query->where('user_id', $_GET['instructor']);
        //     $instructor = $_GET['instructor'];
        // }

        // status filter



        // $query = Lesson::where(['lesson_type'=>'quiz'])
        // ->where('title','LIKE','%' .request('search') .'%')
        // ->orWhere('retake','LIKE','%' .request('search') .'%')
        // ->orWhere('total_mark','LIKE','%' .request('search') .'%')
        // ->orWhere('pass_mark','LIKE','%' .request('search') .'%');




        $page_data['quizs'] = $query->where(['lesson_type'=>'quiz','type'=>'1'])->paginate(20)->appends(request()->query());

        return view('admin.exams.index',$page_data);
    }
    public function assignmentsList(Request $request){


        $category   = 'all';
        $status     = 'all';
        $instructor = 'all';

        $query = Lesson::query();


        if (isset($_GET['search']) && $_GET['search'] != '') {
            $query = $query->where(['lesson_type'=>'quiz','type'=>'2'])
            ->where('title', 'LIKE', '%' . $_GET['search'] . '%')
            ->orWhere('retake','LIKE','%' .request('search') .'%')
            ->orWhere('total_mark','LIKE','%' .request('search') .'%')
            ->orWhere('pass_mark','LIKE','%' .request('search') .'%');

        }


        if (isset($_GET['status']) && $_GET['status'] != '' && $_GET['status'] != 'all') {

            if ($_GET['status'] == '1') {
                $search_status = '1';
                $query         = $query->where('status', $search_status);
            }

            elseif ($_GET['status'] == '0') {
                $search_status = '0';
                $query         = $query->where('status', $search_status);
            }

            $status = $_GET['status'];
        }


        if (isset($request->category) && $request->category != '' && $request->category != 'all') {

            $category_details = Category::where('slug', $request->category)->first();
            if ($category_details->parent_id > 0) {
                $page_data['child_cat'] = $request->category;


                $query   = $query->whereHas('course.category',function($q) use ($category_details){
                    $q->where('id', $category_details->id);
                });

            } else {
                $sub_cat_id   = Category::where('parent_id', $category_details->id)->pluck('id')->toArray();
                $sub_cat_id[] = $category_details->id;

                $query   = $query->whereHas('course.category',function($q) use ($sub_cat_id){
                    $q->whereIn('id', $sub_cat_id);
                });
                $page_data['parent_cat'] = $request->category;
            }
        }

        $page_data['quizs'] = $query->where(['lesson_type'=>'quiz','type'=>'2'])->paginate(20)->appends(request()->query());

        return view('admin.assignments.index',$page_data);
    }
    public function activation($id)
    {
        $lession = Lesson::where(['lesson_type'=>'quiz'])->findOrFail($id);
        $lession->status == '1' ? $lession->status = 0 : $lession->status = 1;
        $lession->save();
        if ((int) $lession->status === 1) {
            app(WhatsAppNotifier::class)->notifyQuizActivated($lession);
        }
        return redirect()->back()
        // ->route('admin.exams.list')
        ->with('success', 'exams or assignment activation successfully.');
    }

    /**
     * Load sections for a course (AJAX)
     */
    public function loadSections($courseId)
    {
        $sections = Section::where('course_id', $courseId)
            ->orderBy('sort')
            ->get(['id', 'title']);
        return response()->json(['sections' => $sections]);
    }

    /**
     * Copy or move exam to another course
     */
    public function copyOrMoveExam(Request $request)
    {
        if (!has_permission('admin.exams.copy_move')) {
            Session::flash('error', get_phrase('You do not have permission to perform this action.'));
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:lessons,id',
            'action' => 'required|in:copy,move',
            'target_course_id' => 'required|exists:courses,id',
            'target_section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            Session::flash('error', get_phrase('Invalid data. Please check your inputs.'));
            return redirect()->back();
        }

        $exam = Lesson::where(['lesson_type' => 'quiz', 'type' => '1'])->findOrFail($request->exam_id);
        $targetCourse = Course::findOrFail($request->target_course_id);
        $targetSection = Section::where('course_id', $request->target_course_id)
            ->findOrFail($request->target_section_id);

        if ($exam->course_id == $request->target_course_id) {
            Session::flash('error', get_phrase('Target course must be different from current course.'));
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            if ($request->action == 'copy') {
                $maxLesson = Lesson::where('course_id', $request->target_course_id)
                    ->orderBy('sort', 'desc')
                    ->first();
                $maximum_sort_value = $maxLesson ? $maxLesson->sort : 0;

                $newExam = $exam->replicate();
                $newExam->course_id = $request->target_course_id;
                $newExam->section_id = $request->target_section_id;
                $newExam->sort = $maximum_sort_value + 1;
                $newExam->save();

                foreach ($exam->questions as $question) {
                    $newQuestion = $question->replicate();
                    $newQuestion->quiz_id = $newExam->id;
                    $newQuestion->save();
                }

                Session::flash('success', get_phrase('Exam copied successfully.'));
            } else {
                $exam->course_id = $request->target_course_id;
                $exam->section_id = $request->target_section_id;
                $maxLesson = Lesson::where('course_id', $request->target_course_id)
                    ->orderBy('sort', 'desc')
                    ->first();
                $exam->sort = ($maxLesson ? $maxLesson->sort : 0) + 1;
                $exam->save();

                Session::flash('success', get_phrase('Exam moved successfully.'));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', get_phrase('An error occurred. Please try again.'));
        }

        return redirect()->route('admin.exams.list');
    }
}

<?php

namespace Modules\Theme\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use Modules\BookStore\App\Models\Book;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('theme::courses.index');
    }

    public function course_details($id){

            // Check if the course exists before applying the complex query
            $courseExists =Course::where('id', $id)->exists();
            if (!$courseExists) {
                Session::flash('error', get_phrase('Data not found.'));
                return redirect()->back();

            }

            // Get the current date and time in the format YYYY-MM-DD HH:MM:SS
            $data['title'] = ('frontend.Course Details'); // Title

            $data['course'] = Course::where('status', 'active')
                ->with([
                    'category.parent',
                    'sections.allLesson' => function($query){
                        $query->active()->whereStatus(1);
                    }
                    ])
                ->where('id', $id)
                ->first();

            // Initialize lesson, quiz, and question counts
            $data['lessonCount']   = 0;
            $data['documentCount'] = 0;

            $data['quizeCount'] = 0;
            $data['assinmentCount'] = 0;
            $data['question'] = 0;
            $data['question_number_count'] = 0;

            // Check if sections are available
            if (!empty($data['course']->sections)) {
                // Loop through sections and lessons to count them
                foreach ($data['course']->sections as $section) {
                    // return  $section->allLesson ;
                    foreach ($section->allLesson as $lesson) {


                        if (($lesson->lesson_type != 'text') && ($lesson->lesson_type != 'image') && ($lesson->lesson_type != 'document_type') && ($lesson->lesson_type != 'quiz')) {
                            $data['lessonCount']++;
                            // $data['question_number_count'] += $lesson->question_number;

                        }elseif(($lesson->lesson_type == 'document_type')){
                            $data['documentCount']++;
                        }
                        else {
                            if($lesson->lesson_type == "quiz"){

                                if($lesson->type ==1){
                                    $data['quizeCount']++;
                                    $data['question'] += count($lesson->questions);
                                    }else{
                                       $data['assinmentCount']++;
                                       $data['question'] += count($lesson->questions);

                                    }
                            }

                        }
                    }
                }
            } else {
                // Handle case where there are no sections
                return redirect('/')->with('danger','No_sections_found');
            }
            // return   $data['assinmentCount'];
            // Render partial views
            // $data['profile'] = view('frontend.partials.course.instructor_profile', compact('data'))->render();
            // $data['review'] = view('frontend.partials.course.reviews', compact('data'))->render();
            // $data['curriculum'] = view('frontend.partials.course.curriculum', ['sections' => $data['course']->sections])->render();

            // Return course details view if course is found
            return view('theme::courses.index', compact('data'));

    }


    public function book_details($id){
        $data['book'] = Book::with('category')->where('status',1)->where('id',$id)->first();
        if(!$data['book']){
            Session::flash('error', get_phrase('book not found.'));
                return redirect()->back();
        }
        return view('theme::books.index', compact('data'));

    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use Modules\BookStore\App\Models\Book;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CartItem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function allCourses()
    {
        $courses = Course::where('status', 'active')->get();
    
        return response()->json([
            'status' => true,
            'message' => 'Courses retrieved successfully',
            'courses' => $courses,
        ], 200);
    }
    // Fetch all the categories
    public function courseDetails($id)
    {
        $course = Course::where('status', 'active')
            ->with([
                'category.parent',
                'sections.allLesson' => function ($query) {
                    $query->active()->whereStatus(1);
                },
                'sections.allLesson.questions'
            ])
            ->find($id);
    
        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found',
                'data' => null
            ], 404);
        }
    
        $lessonCount = 0;
        $documentCount = 0;
        $quizCount = 0;
        $assignmentCount = 0;
        $questionCount = 0;
    
        if ($course->sections) {
    
            foreach ($course->sections as $section) {
    
                foreach ($section->allLesson as $lesson) {
    
                    if (
                        $lesson->lesson_type != 'text' &&
                        $lesson->lesson_type != 'image' &&
                        $lesson->lesson_type != 'document_type' &&
                        $lesson->lesson_type != 'quiz'
                    ) {
    
                        $lessonCount++;
    
                    } elseif ($lesson->lesson_type == 'document_type') {
    
                        $documentCount++;
    
                    } elseif ($lesson->lesson_type == 'quiz') {
    
                        if ($lesson->type == 1) {
    
                            $quizCount++;
    
                        } else {
    
                            $assignmentCount++;
    
                        }
    
                        $questionCount += $lesson->questions->count();
                    }
                }
            }
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Course details retrieved successfully',
            'data' => [
                'course' => $course,
                'lesson_count' => $lessonCount,
                'document_count' => $documentCount,
                'quiz_count' => $quizCount,
                'assignment_count' => $assignmentCount,
                'question_count' => $questionCount,
            ]
        ], 200);
    }


    public function sections(Request $request)
    {
        if (!auth('sanctum')->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => null,
            ], 401);
        }
    
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);
    
        $userId = auth('sanctum')->id();
        $courseId = $request->course_id;
    
        $sections = sections($courseId, $userId);
    
        return response()->json([
            'status' => true,
            'message' => 'Sections retrieved successfully',
            'data' => $sections,
        ], 200);
    }

 // Filter course
//  public function filter_course(Request $request)
//  {
//      $query = Course::query();
 
//      if (!empty($request->selected_search_string) && $request->selected_search_string != 'null') {
//          $query->where('title', 'LIKE', '%' . $request->selected_search_string . '%');
//      }
 
//      if ($request->selected_category != 'all') {
//          $query->where('category_id', $request->selected_category);
//      }
 
//      if ($request->selected_price != 'all') {
 
//          if ($request->selected_price == 'paid') {
//              $query->where('is_paid', 1);
//          }
 
//          if ($request->selected_price == 'free') {
//              $query->where(function ($q) {
//                  $q->where('is_paid', 0)
//                    ->orWhereNull('is_paid');
//              });
//          }
//      }
 
//      if ($request->selected_level != 'all') {
//          $query->where('level', $request->selected_level);
//      }
 
//      if ($request->selected_language != 'all') {
//          $query->where('language', $request->selected_language);
//      }
 
//      $courses = $query
//          ->where('status', 'active')
//          ->get();
 
//      return response()->json([
//          'status' => true,
//          'message' => 'Courses filtered successfully',
//          'courses' => course_data($courses),
//      ], 200);
//  }

//     // Filter course
//     public function courses_by_search_string(Request $request)
//     {
//         $courses = Course::where('title', 'LIKE', "%{$request->search_string}%")
//             ->where('status', 'active')
//             ->get();
    
//         return response()->json([
//             'status' => true,
//             'message' => 'Courses retrieved successfully',
//             'courses' => course_data($courses),
//         ], 200);
//     }


//     // Course Details
//     public function course_details_by_id(Request $request)
//     {
//         $courseId = $request->course_id;
    
//         $user = auth('sanctum')->user();
//         $userId = $user ? $user->id : 0;
    
//         $course = course_details_by_id($userId, $courseId);
    
//         if (!$course) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Course not found',
//                 'course' => null,
//             ], 404);
//         }
    
//         return response()->json([
//             'status' => true,
//             'message' => 'Course retrieved successfully',
//             'course' => $course,
//         ], 200);
//     }
}

<?php

namespace Modules\Theme\App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;

class MyCoursesController extends Controller
{
    public function index()
    {
        $page_data['my_courses'] = Enrollment::join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->join('users', 'courses.user_id', '=', 'users.id')
            ->where('enrollments.user_id', auth()->user()->id)    ->where('courses.status', 'active')

            ->whereRaw('enrollments.id = (SELECT MAX(e.id) FROM enrollments e WHERE e.user_id = enrollments.user_id AND e.course_id = enrollments.course_id)')
            ->select('enrollments.*', 'courses.slug', 'courses.title',
             'courses.thumbnail', 'courses.discount_flag','courses.price','courses.discount_price',
             'courses.created_at',
              'courses.is_paid', 'users.name as user_name', 'users.photo as user_photo')
            ->paginate(8);

            //  return $page_data['my_courses'];

            return view('theme::student.my_courses.index',$page_data);


    }
}

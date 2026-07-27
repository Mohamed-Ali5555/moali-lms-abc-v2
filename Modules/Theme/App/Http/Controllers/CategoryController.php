<?php

namespace Modules\Theme\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollments;
use App\Models\Lesson;
use App\Models\Watch_history;
use Modules\BookStore\App\Models\Book;

class CategoryController extends Controller
{
    public function index($id)
    {
        $category = Category::where('id', $id)->where('status', 1)->first();
        if (! $category) {
            return redirect()->back();
        }

        $categories = Category::where('parent_id', $category->id)
            ->where('status', 1)
            ->orderBy('sort')
            ->get();

        if ($categories->isEmpty()) {
            $categories = Category::where('id', $id)->where('status', 1)->get();
        }

        $mainCategory = $category;
        $categoryIds  = $categories->pluck('id')->all();

        $courses = Course::where('status', 'active')
            ->whereIn('category_id', $categoryIds)
            ->orderByDesc('id')
            ->get();

        $books = Book::where('status', 1)
            ->where('category_id', $category->id)
            ->orderBy('sort', 'asc')
            ->get();

        $enrolledCourseIds = [];
        $playerUrls        = [];

        if (auth()->check()) {
            $userId = auth()->id();

            $enrolledCourseIds = Enrollments::where('user_id', $userId)
                ->whereIn('course_id', $courses->pluck('id'))
                ->pluck('course_id')
                ->map(fn ($cid) => (int) $cid)
                ->all();

            $watchMap = Watch_history::where('student_id', $userId)
                ->whereIn('course_id', $enrolledCourseIds)
                ->pluck('watching_lesson_id', 'course_id');

            $firstLessons = Lesson::whereIn('course_id', $enrolledCourseIds)
                ->orderBy('sort', 'asc')
                ->get()
                ->groupBy('course_id')
                ->map(fn ($group) => optional($group->first())->id);

            foreach ($enrolledCourseIds as $courseId) {
                $course = $courses->firstWhere('id', $courseId);
                if (! $course) {
                    continue;
                }

                $lessonId = $watchMap[$courseId] ?? $firstLessons[$courseId] ?? null;
                $playerUrls[$courseId] = $lessonId
                    ? route('course.player', ['slug' => $course->slug, 'id' => $lessonId])
                    : route('course.player', ['slug' => $course->slug]);
            }

            $cartItems = CartItem::where('user_id', $userId)
                ->whereNotNull('book_id')
                ->pluck('qty', 'book_id')
                ->toArray();
        } else {
            $cartItems = [];
        }

        $coursesByCategory = $courses->groupBy('category_id');

        return view('theme::category.index', compact(
            'courses',
            'coursesByCategory',
            'categories',
            'books',
            'mainCategory',
            'cartItems',
            'enrolledCourseIds',
            'playerUrls'
        ));
    }
}

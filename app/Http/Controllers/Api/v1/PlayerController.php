<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\frontend\HomeController as FrontendHomeController;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Forum;
use App\Models\Lesson;
use App\Models\Watch_history;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function coursePlayer(Request $request, $slug, $lesson_id = '')
    {
        $course = Course::where('slug', $slug)->where('status', 'active')->first();

        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'الكورس غير موجود أو غير نشط',
            ], 404);
        }

        $user = $request->user();

        if ($course->is_paid && $user->role !== 'admin') {
            if ($user->role === 'student') {
                $enrollStatus = enroll_status($course->id, $user->id);
                if ($enrollStatus === 'expired') {
                    return response()->json([
                        'status' => false,
                        'message' => 'انتهت صلاحية وصولك إلى الدورة التدريبية. عليك شراؤها مرة أخرى.',
                    ], 403);
                }
                if ($enrollStatus === 'free_locked') {
                    return response()->json([
                        'status' => false,
                        'message' => 'هذا الكورس أصبح مدفوعاً. عليك شراؤه للوصول إلى المحتوى.',
                    ], 403);
                }
                if (!$enrollStatus) {
                    return response()->json([
                        'status' => false,
                        'message' => 'أنت غير مسجل في هذا الكورس',
                    ], 403);
                }
            } elseif ($user->role === 'instructor' && $course->user_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'لست المدرّس المسؤول عن هذا الكورس',
                ], 403);
            }
        }

        $checkLessonHistory = Watch_history::where('course_id', $course->id)
            ->where('student_id', $user->id)
            ->first();

        $firstLessonOfCourse = Lesson::where('course_id', $course->id)->Active()->orderBy('sort', 'asc')->value('id');

        if ($lesson_id === '' || $lesson_id === null) {
            $lesson_id = $checkLessonHistory->watching_lesson_id ?? $firstLessonOfCourse;
        }

        if (!$checkLessonHistory && $lesson_id > 0) {
            Watch_history::insert([
                'course_id' => $course->id,
                'student_id' => $user->id,
                'watching_lesson_id' => $lesson_id,
                'completed_lesson' => json_encode([]),
            ]);
        }

        if ($lesson_id > 0) {
            Watch_history::where('course_id', $course->id)
                ->where('student_id', $user->id)
                ->update(['watching_lesson_id' => $lesson_id]);
        }

        if (
            $course->enable_drip_content == 1
            && $lesson_id > 0
            && in_array($lesson_id, get_locked_lesson_ids($course->id, $user->id))
        ) {
            $dripSettings = json_decode($course->drip_content_settings, true);
            $message = strip_tags(htmlspecialchars_decode($dripSettings['locked_lesson_message'] ?? ''));
            if (trim($message) === '') {
                $message = 'This course supports drip content, so you must complete the previous lessons.';
            }

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 403);
        }

        $lessonDetails = Lesson::where('id', $lesson_id)->Active()->first();
        $history = Watch_history::where('course_id', $course->id)->where('student_id', $user->id)->first();

        $forumQuery = Forum::join('users', 'forums.user_id', 'users.id')
            ->select('forums.*', 'users.name as user_name', 'users.photo as user_photo')
            ->latest('forums.id')
            ->where('forums.parent_id', 0)
            ->where('forums.course_id', $course->id);

        if ($request->filled('search')) {
            $forumQuery->where(function ($query) use ($request) {
                $query->where('forums.title', 'like', '%' . $request->search . '%')
                    ->orWhere('forums.description', 'like', '%' . $request->search . '%');
            });
        }

        $sections = sections($course->id, $user->id);

        return response()->json([
            'status' => true,
            'course' => $course,
            'lesson' => $lessonDetails,
            'history' => $history,
            'questions' => $forumQuery->get(),
            'sections' => $sections,
            'progress' => round(course_progress($course->id, $user->id)),
        ], 200);
    }

    public function setWatchHistory(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'lesson_id' => 'required|integer',
        ]);

        $course = Course::where('id', $request->course_id)->first();
        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'الكورس غير موجود',
            ], 404);
        }

        $user = $request->user();
        $enrollment = Enrollment::where('course_id', $course->id)->where('user_id', $user->id)->first();

        if (!$enrollment && ($user->role !== 'admin' || !is_course_instructor($course->id))) {
            return response()->json([
                'status' => false,
                'message' => 'أنت غير مسجل في هذا الكورس',
            ], 403);
        }

        $totalLesson = Lesson::where('course_id', $request->course_id)->Active()->pluck('id')->toArray();

        $watchHistory = Watch_history::where('course_id', $request->course_id)
            ->where('student_id', $user->id)
            ->first();

        $data = [
            'course_id' => $request->course_id,
            'student_id' => $user->id,
            'watching_lesson_id' => $request->lesson_id,
        ];

        if ($watchHistory) {
            $lessons = (array) json_decode($watchHistory->completed_lesson);
            if (!in_array($request->lesson_id, $lessons)) {
                $lessons[] = $request->lesson_id;
            } else {
                while (($key = array_search($request->lesson_id, $lessons)) !== false) {
                    unset($lessons[$key]);
                }
            }
            $data['completed_lesson'] = json_encode(array_values($lessons));
            $data['completed_date'] = (count($totalLesson) === count($lessons)) ? time() : null;
            Watch_history::where('course_id', $request->course_id)->where('student_id', $user->id)->update($data);
        } else {
            $lessons = [$request->lesson_id];
            $data['completed_lesson'] = json_encode($lessons);
            $data['completed_date'] = (count($totalLesson) === count($lessons)) ? time() : null;
            Watch_history::insert($data);
        }

        if (progress_bar($request->course_id) >= 100) {
            $certificate = Certificate::where('user_id', $user->id)->where('course_id', $request->course_id);
            if ($certificate->count() === 0) {
                Certificate::insert([
                    'user_id' => $user->id,
                    'course_id' => $request->course_id,
                    'identifier' => random(12),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث سجل المشاهدة',
            'progress' => round(course_progress($request->course_id, $user->id)),
            'completion' => course_completion_data($request->course_id, $user->id),
        ], 200);
    }

    public function updateWatchDuration(Request $request)
    {
        return app(FrontendHomeController::class)->update_watch_history_with_duration($request);
    }
}

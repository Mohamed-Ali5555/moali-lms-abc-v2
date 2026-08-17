<?php
// import facade

use App\Models\Addon;
use App\Models\SeoField;
use function PHPUnit\Framework\fileExists;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

if (!function_exists('clear_lms_cache')) {
    /**
     * Invalidate shared LMS performance caches (settings, phrases, permissions).
     */
    function clear_lms_cache(?string $group = null): void
    {
        $groups = $group ? [$group] : ['settings', 'frontend_settings', 'theme_settings', 'phrases', 'permissions'];

        foreach ($groups as $g) {
            if ($g === 'settings') {
                Cache::forget('settings.all');
            } elseif ($g === 'frontend_settings') {
                Cache::forget('frontend_settings.all');
            } elseif ($g === 'theme_settings') {
                Cache::forget('theme_settings.all');
            } elseif ($g === 'phrases') {
                // Forget known language phrase maps via a version bump key
                $version = (int) Cache::get('phrases.version', 1);
                Cache::forever('phrases.version', $version + 1);
                Cache::forget('languages.name_to_id');
                Cache::forget('languages.name_to_direction');
                Cache::forget('languages.all');
            } elseif ($g === 'permissions') {
                $version = (int) Cache::get('permissions.version', 1);
                Cache::forever('permissions.version', $version + 1);
            }
        }
    }
}


if (!function_exists('seoField')) {
    function seoField()
    {
        $seo = \App\Models\SeoField::first();

        if (!$seo) {
            return null;
        }

        if (!empty($seo->meta_keywords)) {
            $decoded = json_decode($seo->meta_keywords, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $seo->meta_keywords = collect($decoded)
                    ->pluck('value')
                    ->filter()
                    ->implode(', ');
            }
        }

        return $seo;
    }
}


if (!function_exists('module_asset')) {
    function module_asset($module, $path)
    {
        return asset("modules/$module/$path");
    }
}

// encrypt id
if (!function_exists('encryptFunction')) {
    function encryptFunction($number = null)
    {

        return openssl_encrypt($number, "AES-128-CTR", "CodeSpeedyKeybj54HH", 0, '8565825542115032');
    }
}
;
// Global Settings
if (!function_exists('get_src')) {
    function get_src($url = null, $optimized = false)
    {
        return get_image($url, $optimized);
    }
}

if (!function_exists('lesson_count')) {
    function lesson_count($course_id = "")
    {
        if ($course_id == '') {
            return;
        }

        static $memo = [];
        $course_id = (string) $course_id;
        if (!array_key_exists($course_id, $memo)) {
            $memo[$course_id] = DB::table('lessons')->whereStatus(1)->where('course_id', $course_id)->count();
        }

        return $memo[$course_id];
    }
}
if (!function_exists('section_count')) {
    function section_count($course_id = "")
    {
        if ($course_id == '') {
            return;
        }

        static $memo = [];
        $course_id = (string) $course_id;
        if (!array_key_exists($course_id, $memo)) {
            $memo[$course_id] = DB::table('sections')->where('course_id', $course_id)->count();
        }

        return $memo[$course_id];
    }
}
if (!function_exists('count_blogs_by_category')) {
    function count_blogs_by_category($category_id = "")
    {
        if ($category_id != '') {
            $total_blog = DB::table('blogs')->where('status', 1)->where('category_id', $category_id)->count();
            return $total_blog;
        }
    }
}
if (!function_exists('get_blog_category_name')) {
    function get_blog_category_name($id = "")
    {
        if ($id != '') {
            $category_title = DB::table('blog_categories')->where('id', $id)->value('title');
            return $category_title;
        }
    }
}
if (!function_exists('get_user_info')) {
    function get_user_info($user_id = "")
    {
        static $memo = [];
        $key = (string) $user_id;
        if (!array_key_exists($key, $memo)) {
            $memo[$key] = App\Models\User::where('id', $user_id)->firstOrNew();
        }
        return $memo[$key];
    }
}
if (!function_exists('get_image_by_id')) {
    function get_image_by_id($user_id = "")
    {
        $image_path = DB::table('users')->where('id', $user_id)->value('photo');
        return get_image($image_path);
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($time_ago)
    {
        $time_ago     = strtotime($time_ago);
        $cur_time     = time();
        $time_elapsed = $cur_time - $time_ago;
        $seconds      = $time_elapsed;
        $minutes      = round($time_elapsed / 60);
        $hours        = round($time_elapsed / 3600);
        $days         = round($time_elapsed / 86400);
        $weeks        = round($time_elapsed / 604800);
        $months       = round($time_elapsed / 2600640);
        $years        = round($time_elapsed / 31207680);
        // Seconds
        if ($seconds <= 60) {
            return "just now";
        }
        //Minutes
        else if ($minutes <= 60) {
            if ($minutes == 1) {
                return "1 minute ago";
            } else {
                return "$minutes minutes ago";
            }
        }
        //Hours
        else if ($hours <= 24) {
            if ($hours == 1) {
                return "1 hour ago";
            } else {
                return "$hours hours ago";
            }
        }
        //Days
        else if ($days <= 7) {
            if ($days == 1) {
                return "Yesterday";
            } else {
                return "$days days ago";
            }
        }
        //Weeks
        else if ($weeks <= 4.3) {
            if ($weeks == 1) {
                return "1 week ago";
            } else {
                return "$weeks weeks ago";
            }
        }
        //Months
        else if ($months <= 12) {
            if ($months == 1) {
                return "1 month ago";
            } else {
                return "$months months ago";
            }
        }
        //Years
        else {
            if ($years == 1) {
                return "1 year ago";
            } else {
                return "$years years ago";
            }
        }
    }
}

if (!function_exists('course_enrolled')) {
    function course_enrolled($user_id = "")
    {
        if ($user_id != '') {
            $enrolled = DB::table('enrollments')->where('user_id', $user_id)->exists();
            return $enrolled;
        }
    }
}
if (!function_exists('course_enrollments')) {
    function course_enrollments($course_id = "")
    {
        if ($course_id != '') {
            $enrolled = DB::table('enrollments')->where('course_id', $course_id)->count();
            return $enrolled;
        }
    }
}
if (!function_exists('course_by_instructor')) {
    function course_by_instructor($course_id = "")
    {
        if ($course_id != '') {
            $user_id      = App\Models\Course::where('id', $course_id)->firstOrNew();
            $byInstructor = App\Models\User::where('id', $user_id->user_id)->firstOrNew();
            return $byInstructor;
        }
    }
}

if (!function_exists('course_instructor_image')) {
    function course_instructor_image($course_id = "")
    {
        if ($course_id != '') {
            $user_id    = DB::table('courses')->where('id', $course_id)->value('user_id');
            $user_image = DB::table('users')->where('id', $user_id)->value('photo');
        }
        $img_path = isset($user_image) ? $user_image : $course_id;
        return get_image($img_path);
    }
}

if (!function_exists('get_course_info')) {
    function get_course_info($course_id)
    {
        $course = App\Models\Course::where('id', $course_id)->firstOrNew();
        return $course;
    }
}
if (!function_exists('count_unread_message_of_thread')) {

    function count_unread_message_of_thread($message_thread_code = "")
    {
        $unread_message_counter = 0;
        $current_user           = auth()->user()->id;
        $messages               = DB::table('messages')->where('message_thread_code', $message_thread_code)->get();
        foreach ($messages as $row) {
            if ($row->sender != $current_user && $row->read_status == '0') {
                $unread_message_counter++;
            }
        }
        return $unread_message_counter;
    }
}
if (!function_exists('get_enroll_info')) {
    function get_enroll_info($course_id = "", $user_id = "")
    {
        if ($course_id != '' && $user_id != "") {
            $enrolled = App\Models\Enrollment::where('course_id', $course_id)->where('user_id', $user_id)->firstOrNew();
            return $enrolled;
        }
    }
}
if (!function_exists('total_enrolled')) {
    function total_enrolled()
    {
        $total_enrolled = DB::table('enrollments')->count();
        return $total_enrolled;
    }
}
if (!function_exists('total_enroll')) {
    function total_enroll($course_id = "")
    {
        if ($course_id != '') {
            $count = DB::table('enrollments')->where('course_id', $course_id)->count();
            return $count;
        }
    }
}
if (!function_exists('is_course_instructor')) {
    function is_course_instructor($course_id = "", $user_id = "")
    {
        if ($user_id == '') {
            $user_id = auth()->user()->id;
        }
        $course = App\Models\Course::where('id', $course_id)->firstOrNew();
        if ($course) {
            if ($course->instructors()->where('id', $user_id)->count() > 0 || $course->user_id == $user_id) {
                return true;
            }
        }
        return false;
    }
}

// Get Home page Settings Data
if (!function_exists('get_homepage_settings')) {
    function get_homepage_settings($type = "", $return_type = false)
    {
        $value = DB::table('home_page_settings')->where('key', $type);
        if ($value->count() > 0) {
            if ($return_type === true) {
                return json_decode($value->value('value'), true);
            } elseif ($return_type === "object") {
                return json_decode($value->value('value'));
            } else {
                return $value->value('value');
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('count_student_by_instructor')) {
    function count_student_by_instructor($user_id = "")
    {
        if ($user_id != '') {
            $course_ids        = App\Models\Course::where('user_id', $user_id)->pluck('id')->toArray();
            $total_student = 0;
            $total_student = App\Models\Enrollment::whereIn('course_id', $course_ids)->distinct('user_id')->count();
            return ($total_student > 1) ? "{$total_student} " . get_phrase('Students') : "{$total_student} " . get_phrase('Student');
        }
    }
}
if (!function_exists('count_course_by_instructor')) {
    function count_course_by_instructor($user_id = "")
    {
        if ($user_id != '') {
            $count_course = DB::table('courses')->where('status', 'active')->where('user_id', $user_id)->count();
            return ($count_course > 1) ? "{$count_course} " . get_phrase('Courses') : "{$count_course} " . get_phrase('Course');
        }
    }
}
if (!function_exists('progress_bar')) {
    function progress_bar($course_id = "")
    {
        if ($course_id != '') {
            $lesson_history = App\Models\Watch_history::where('course_id', $course_id)
                ->where('student_id', auth()->user()->id)
                ->firstOrNew();
            $total_lesson = lesson_count($course_id);

            $progress_result = 0;
            if ($lesson_history && $lesson_history->completed_lesson != '') {
                $complete_lesson_ids = json_decode($lesson_history->completed_lesson, true) ?? [0];
                $count_incomplete_lesson = App\Models\Lesson::where('course_id', $course_id)->whereNotIn('id', $complete_lesson_ids)->count();
                $count_complete_lesson = $total_lesson - $count_incomplete_lesson;

                if (count($complete_lesson_ids) != $count_complete_lesson) {
                    check_lesson_was_deleted($complete_lesson_ids, $lesson_history->id);
                }

                $progress_result       = $total_lesson ? (count($complete_lesson_ids) * 100) / $total_lesson : 0;
            }



            if ($progress_result <= 100) {
                return number_format($progress_result, 2);
            } else {
                return 100;
            }
        }
    }
}

if (!function_exists('progress_bar_admin')) {
    function progress_bar_admin($course_id = "" , $user_id = "")
    {
        if ($course_id != '') {
            $lesson_history = App\Models\Watch_history::where('course_id', $course_id)
                ->where('student_id', $user_id)
                ->firstOrNew();
            $total_lesson = lesson_count($course_id);

            $progress_result = 0;
            if ($lesson_history && $lesson_history->completed_lesson != '') {
                $complete_lesson_ids = json_decode($lesson_history->completed_lesson, true) ?? [0];
                $count_incomplete_lesson = App\Models\Lesson::where('course_id', $course_id)->whereNotIn('id', $complete_lesson_ids)->count();
                $count_complete_lesson = $total_lesson - $count_incomplete_lesson;

                if (count($complete_lesson_ids) != $count_complete_lesson) {
                    check_lesson_was_deleted($complete_lesson_ids, $lesson_history->id);
                }

                $progress_result       = $total_lesson ? (count($complete_lesson_ids) * 100) / $total_lesson : 0;
            }



            if ($progress_result <= 100) {
                return number_format($progress_result, 2);
            } else {
                return 100;
            }
        }
    }
}
if (!function_exists('check_lesson_was_deleted')) {
    function check_lesson_was_deleted($complete_lesson_ids = [], $lesson_history_id = "")
    {
        $updated_completed_lesson_ids = [];
        foreach ($complete_lesson_ids as $complete_lesson_id) {
            if (App\Models\Lesson::where('id', $complete_lesson_id)->count() > 0) {
                $updated_completed_lesson_ids[] = $complete_lesson_id;
            }
        }
        App\Models\Watch_history::where('id', $lesson_history_id)->update(['completed_lesson' => json_encode($updated_completed_lesson_ids)]);
    }
}
if (!function_exists('course_creator')) {
    function course_creator($user_id = "")
    {
        if ($user_id != '') {
            $creator = DB::table('courses')->where('user_id', $user_id)->exists();
            return $creator;
        }
    }
}
if (!function_exists('get_course_creator_id')) {
    function get_course_creator_id($course_id = "")
    {
        if ($course_id === '' || $course_id === null) {
            return new App\Models\User();
        }

        $course = DB::table('courses')->where('id', $course_id)->first();
        if (! $course || empty($course->user_id)) {
            return new App\Models\User();
        }

        return App\Models\User::where('id', $course->user_id)->firstOrNew();
    }
}

if (!function_exists('user_count')) {
    function user_count($role = "")
    {
        if ($role != '') {
            $user_count = DB::table('users')->where('role', $role)->count();
            return $user_count;
        }
    }
}
if (!function_exists('blog_user')) {
    function blog_user($user_id = "")
    {
        if ($user_id != '') {
            $user = App\Models\User::where('id', $user_id)->firstOrNew();
            return $user;
        }
    }
}
if (!function_exists('category_course_count')) {
    function category_course_count($slug = "")
    {
        if ($slug != '') {
            $slug_category = App\Models\Category::where('slug', $slug)->firstOrNew();
            $all_category  = DB::table('categories')->where('id', $slug_category->id)->get();
            foreach ($all_category as $row) {
                $sub_category = DB::table('categories')->where('parent_id', $row->id)->get();
            }
            foreach ($sub_category as $sub_categories) {

                $category_by_course = DB::table('courses')->where('category_id', $sub_categories->id)->count();
            }
            return $category_by_course;
        }
    }
}

if (!function_exists('category_by_course')) {
    function category_by_course($category_id = "")
    {
        static $memo = [];
        $key = (string) $category_id;
        if (!array_key_exists($key, $memo)) {
            $memo[$key] = App\Models\Category::where('id', $category_id)->firstOrNew();
        }
        return $memo[$key];
    }
}

if (!function_exists('check_course_admin')) {
    function check_course_admin($user_id = "")
    {
        if ($user_id != '') {
            $creator = DB::table('users')->where('id', $user_id)->value('role');
            return $creator;
        }
    }
}

if (!function_exists('duration_to_seconds')) {
    function duration_to_seconds($duration = "00:00:00:")
    {
        $time_array        = explode(':', $duration);
        $hour_to_seconds   = $time_array[0] * 60 * 60;
        $minute_to_seconds = $time_array[1] * 60;
        $seconds           = $time_array[2];
        $total_seconds     = $hour_to_seconds + $minute_to_seconds + $seconds;
        return $total_seconds;
    }
}

if (!function_exists('total_durations')) {
    function total_durations($course_id = '')
    {
        $total_duration = 0;
        $lessons        = DB::table('lessons')->where('course_id', $course_id)->get();

        foreach ($lessons as $lesson) {
            if ($lesson->duration != '') {

                $time_array = explode(':', $lesson->duration);

                $hour_to_seconds   = $time_array[0] * 60 * 60;
                $minute_to_seconds = $time_array[1] * 60;
                $seconds           = $time_array[2];
                $total_duration += $hour_to_seconds + $minute_to_seconds + $seconds;
            }
        }

        $hours   = floor($total_duration / 3600);
        $minutes = floor(($total_duration - ($hours * 3600)) / 60);
        $seconds = floor($total_duration - ($hours * 3600) - ($minutes * 60));
        return sprintf("%02dh %02dm", $hours, $minutes);
    }
}

if (!function_exists('total_durations_by')) {
    function total_durations_by($course_id = '')
    {
        $total_duration = 0;
        $lessons        = DB::table('lessons')->where('course_id', $course_id)->get();

        foreach ($lessons as $lesson) {
            if ($lesson->duration != '') {

                $time_array = explode(':', $lesson->duration);

                $hour_to_seconds   = $time_array[0] * 60 * 60;
                $minute_to_seconds = $time_array[1] * 60;
                $seconds           = $time_array[2];
                $total_duration += $hour_to_seconds + $minute_to_seconds + $seconds;
            }
        }

        $hours   = floor($total_duration / 3600);
        $minutes = floor(($total_duration - ($hours * 3600)) / 60);
        $seconds = floor($total_duration - ($hours * 3600) - ($minutes * 60));
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
}

    if (!function_exists('lesson_durations')) {
        function lesson_durations($lesson_id = '')
        {
            $total_duration = 0;
            $lesson = App\Models\Lesson::where('id', $lesson_id)->first();

            if ($lesson && $lesson->duration != '') {
                $time_array = explode(':', $lesson->duration);

                $hour_to_seconds   = isset($time_array[0]) ? $time_array[0] * 3600 : 0;
                $minute_to_seconds = isset($time_array[1]) ? $time_array[1] * 60 : 0;
                $seconds           = isset($time_array[2]) ? $time_array[2] : 0;

                $total_duration += $hour_to_seconds + $minute_to_seconds + $seconds;
            }

            $total_minutes = floor($total_duration / 60);

            return $total_minutes . ' دقيقة';
        }
    }

if (!function_exists('get_super_admin_email')) {
    function get_super_admin_email()
    {
        return strtolower(trim((string) config('admin.super_admin_email', '')));
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin($admin_id = '')
    {
        $super_admin_email = get_super_admin_email();
        if ($super_admin_email === '') {
            return false;
        }

        if (empty($admin_id)) {
            $user = auth()->user();
            return $user && strtolower(trim($user->email)) === $super_admin_email;
        }

        $email = App\Models\User::where('id', $admin_id)->value('email');

        return $email && strtolower(trim($email)) === $super_admin_email;
    }
}

if (!function_exists('is_root_admin')) {
    function is_root_admin($admin_id = '')
    {

        // GET THE LOGGEDIN IN ADMIN ID
        if (empty($admin_id)) {
            $admin_id = auth()->user()->id;
        }

        $root_admin_id = App\Models\User::limit(1)->orderBy('id', 'asc')->firstOrNew()->id;
        if ($root_admin_id == $admin_id) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('removeScripts')) {
    function removeScripts($text)
    {
        if(!$text) return;

        // Remove <script> tags and their content
        $pattern_script = '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is';
        $cleanText = preg_replace($pattern_script, '', $text);

        // Remove inline event handlers (e.g., onclick, onmouseover)
        $pattern_inline = '/\s*on\w+="[^"]*"/i';
        $cleanText = preg_replace($pattern_inline, '', $cleanText);

        // Remove JavaScript: URIs
        $pattern_js_uri = '/\s*href="javascript:[^"]*"/i';
        $cleanText = preg_replace($pattern_js_uri, '', $cleanText);

        // Remove other potentially dangerous tags (e.g., <iframe>, <object>, <embed>)
        $pattern_dangerous_tags = '/<(iframe|object|embed|applet|meta|link|style|base|form)\b[^<]*(?:(?!<\/\1>)<[^<]*)*<\/\1>/is';
        $cleanText = preg_replace($pattern_dangerous_tags, '', $cleanText);

        // Remove any remaining dangerous attributes (e.g., srcset on <img>)
        // $pattern_dangerous_attributes = '/\s*(src|srcset|data)="[^"]*"/i';
        // $cleanText = preg_replace($pattern_dangerous_attributes, '', $cleanText);

        return $cleanText;
    }
}

if (!function_exists('has_permission')) {
    function has_permission($route = '', $user_id = '')
    {
        // GET THE LOGGEDIN IN ADMIN ID
        if (empty($user_id)) {
            $user_id = auth()->user()->id;
        }

        static $root_admin_id = null;
        if ($root_admin_id === null) {
            $root_admin_id = App\Models\User::query()->orderBy('id')->value('id');
        }

        if ($user_id == $root_admin_id || is_super_admin($user_id)) {
            return true;
        }

        static $permission_memo = [];
        $version = (int) Cache::get('permissions.version', 1);
        $memo_key = $version . ':' . $user_id;

        if (!array_key_exists($memo_key, $permission_memo)) {
            $permission_memo[$memo_key] = Cache::remember('permissions.admin.' . $version . '.' . $user_id, 3600, function () use ($user_id) {
                $row = App\Models\Permission::where('admin_id', $user_id)->first();
                if (!$row || empty($row->permissions)) {
                    return [];
                }
                $decoded = is_array($row->permissions) ? $row->permissions : json_decode($row->permissions, true);
                return is_array($decoded) ? $decoded : null;
            });
        }

        $permissions = $permission_memo[$memo_key];
        if ($permissions === null) {
            echo '<span class="d-none">' . redirect('/') . '</span>';
            die;
        }

        return in_array($route, $permissions);
    }
}

if (!function_exists('get_image')) {
    function get_image($url = null, $optimized = false)
    {
        if ($url == null) {
            return asset('uploads/system/placeholder.png');
        }

        // If the value of URL is from an online URL
        if (str_contains($url, 'http://') && str_contains($url, 'https://')) {
            return $url;
        }

        $url_arr = explode('/', $url);
        // File name & Folder path
        $file_name = end($url_arr);
        $path      = str_replace($file_name, '', $url);

        //Optimized image url
        $optimized_image = $path . 'optimized/' . $file_name;

        if (!$optimized) {
            if (is_file(public_path($url)) && file_exists(public_path($url))) {
                return asset($url);
            } else {
                return asset($path . 'placeholder/placeholder.png');
            }
        } else {
            if (is_file(public_path($optimized_image)) && file_exists(public_path($optimized_image))) {
                return asset($optimized_image);
            } else {
                return asset($path . 'placeholder/placeholder.png');
            }
        }
    }
}

if (!function_exists('nice_file_name')) {
    function nice_file_name($file_title = "", $extension = "")
    {
        return slugify($file_title) . '-' . time() . '.' . $extension;
    }
}

if (!function_exists('total_review')) {
    function total_review($course_id = "")
    {
        $review = App\Models\Review::where('course_id', $course_id)
            ->count();
        return $review;
    }
}

// Global Settings
if (!function_exists('remove_file')) {
    function remove_file($url = null)
    {
        if(!$url) return;

        $url = str_replace('public/', '', $url);

        $url       = public_path($url);
        $url       = str_replace('optimized/', '', $url);
        $url_arr   = explode('/', $url);
        $file_name = $url_arr[count($url_arr) - 1];

        if (is_file($url) && file_exists($url) && !empty($file_name)) {
            unlink($url);

            $url = str_replace($file_name, 'optimized/' . $file_name, $url);
            if (is_file($url) && file_exists($url)) {
                unlink($url);
            }
        }
    }
}

if (!function_exists('get_all_language')) {
    function get_all_language()
    {
        return DB::table('languages')->select('name')->distinct()->get();
    }
}

if (!function_exists('get_language_direction')) {
    function get_language_direction()
    {
        static $direction = null;

        if ($direction !== null) {
            return $direction;
        }

        $active_lan = session('language') ?? get_settings('language');
        $active_lan_key = strtolower((string) $active_lan);

        $directions = Cache::remember('languages.name_to_direction', 3600, function () {
            $map = [];
            foreach (DB::table('languages')->select('name', 'direction')->get() as $lang) {
                $map[strtolower($lang->name)] = strtolower((string) $lang->direction) === 'rtl' ? 'rtl' : 'ltr';
            }
            return $map;
        });

        $direction = $directions[$active_lan_key] ?? 'ltr';
        return $direction;
    }
}

if (!function_exists('get_phrase')) {
    function get_phrase($phrase = '', $value_replace = array())
    {
        static $lang_ids = null;
        static $dictionaries = [];

        if ($lang_ids === null) {
            $lang_ids = Cache::remember('languages.name_to_id', 3600, function () {
                $map = [];
                foreach (DB::table('languages')->select('id', 'name')->get() as $lang) {
                    $map[strtolower($lang->name)] = $lang->id;
                }
                return $map;
            });
        }

        $active_lan = session('language') ?? get_settings('language');
        $active_lan_key = strtolower((string) $active_lan);
        $active_lan_id = $lang_ids[$active_lan_key] ?? ($lang_ids['english'] ?? null);

        $version = (int) Cache::get('phrases.version', 1);
        $dict_key = $version . ':' . $active_lan_id;

        if ($active_lan_id && !isset($dictionaries[$dict_key])) {
            $dictionaries[$dict_key] = Cache::remember('phrases.map.' . $version . '.' . $active_lan_id, 3600, function () use ($active_lan_id) {
                return DB::table('language_phrases')
                    ->where('language_id', $active_lan_id)
                    ->pluck('translated', 'phrase')
                    ->toArray();
            });
        }

        if ($active_lan_id && isset($dictionaries[$dict_key][$phrase])) {
            $translated = $dictionaries[$dict_key][$phrase];
        } else {
            $translated = $phrase;
            $english_lan_id = $lang_ids['english'] ?? null;
            if ($english_lan_id) {
                $english_key = $version . ':' . $english_lan_id;
                if (!isset($dictionaries[$english_key])) {
                    $dictionaries[$english_key] = Cache::remember('phrases.map.' . $version . '.' . $english_lan_id, 3600, function () use ($english_lan_id) {
                        return DB::table('language_phrases')
                            ->where('language_id', $english_lan_id)
                            ->pluck('translated', 'phrase')
                            ->toArray();
                    });
                }

                if (!isset($dictionaries[$english_key][$phrase])) {
                    DB::table('language_phrases')->insert([
                        'language_id' => $english_lan_id,
                        'phrase' => $phrase,
                        'translated' => $translated,
                    ]);
                    $dictionaries[$english_key][$phrase] = $translated;
                    Cache::put('phrases.map.' . $version . '.' . $english_lan_id, $dictionaries[$english_key], 3600);
                }
            }
        }

        if (!is_array($value_replace)) {
            $value_replace = array($value_replace);
        }
        foreach ($value_replace as $replace) {
            $translated = preg_replace('/____/', $replace, $translated, 1); // Replace one placeholder at a time
        }

        return $translated;
    }
}

if (!function_exists('script_checker')) {
    function script_checker($string = '', $convert_string = true)
    {
        if ($convert_string) {
            return nl2br(htmlspecialchars(strip_tags($string)));
        } else {
            return $string;
        }
    }
}
if (!function_exists('get_user_by_blogcomment')) {
    function get_user_by_blogcomment($user_id = '')
    {
        if ($user_id != '') {
            $user_info = App\Models\User::where('id', $user_id)->firstOrNew();
            return $user_info;
        }
    }
}

if (!function_exists('date_formatter')) {
    function date_formatter($strtotime = "", $format = "")
    {
        if ($strtotime && !is_numeric($strtotime)) {
            $strtotime = strtotime($strtotime);
        } elseif (!$strtotime) {
            $strtotime = time();
        }

        if ($format == "") {
            return date('d', $strtotime) . ' ' . date('M', $strtotime) . ' ' . date('Y', $strtotime);
        }

        if ($format == 1) {
            return date('D', $strtotime) . ', ' . date('d', $strtotime) . ' ' . date('M', $strtotime) . ' ' . date('Y', $strtotime);
        }

        if ($format == 2) {
            $time_difference = time() - $strtotime;
            if ($time_difference <= 10) {
                return get_phrase('Just now');
            }
            //864000 = 10 days
            if ($time_difference > 864000) {
                return date_formatter($strtotime, 3);
            }

            $condition = array(
                12 * 30 * 24 * 60 * 60 => get_phrase('year'),
                30 * 24 * 60 * 60      => get_phrase('month'),
                24 * 60 * 60           => get_phrase('day'),
                60 * 60                => 'hour',
                60                     => 'minute',
                1                      => 'second',
            );

            foreach ($condition as $secs => $str) {
                $d = $time_difference / $secs;
                if ($d >= 1) {
                    $t = round($d);
                    return $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ' . get_phrase('ago');
                }
            }
        }

        if ($format == 3) {
            $date = date('d', $strtotime);
            $date .= ' ' . date('M', $strtotime);

            if (date('Y', $strtotime) != date('Y', time())) {
                $date .= date(' Y', $strtotime);
            }

            $date .= ' ' . get_phrase('at') . ' ';
            $date .= date('h:i a', $strtotime);
            return $date;
        }

        if ($format == 4) {
            return date('d', $strtotime) . ' ' . date('M', $strtotime) . ' ' . date('Y', $strtotime) . ', ' . date('h:i:s A', $strtotime);
        }
    }
}

if (!function_exists('currency_code')) {
    function currency_code(): string
    {
        $code = get_theme_settings('currency_code');
        if ($code) {
            return strtoupper((string) $code);
        }

        $system = get_settings('system_currency');

        return $system ? strtoupper((string) $system) : 'EGP';
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        $symbol = get_theme_settings('currency_symbol');
        if ($symbol) {
            return (string) $symbol;
        }

        $fromDb = DB::table('currencies')->where('code', currency_code())->value('symbol');

        return $fromDb ?: 'ج.م';
    }
}

if (!function_exists('currency_position')) {
    function currency_position(): string
    {
        $position = get_theme_settings('currency_position');
        if ($position) {
            return (string) $position;
        }

        return get_settings('currency_position') ?: 'right-space';
    }
}

if (!function_exists('currency')) {
    function currency($price = false)
    {
        $price     = max(0, round(floatval($price), 2));
        $pattern   = currency_position();
        $symbol    = currency_symbol();
        $formatted = number_format($price, 2);

        if ($pattern == 'right') {
            return $formatted . $symbol;
        }
        if ($pattern == 'left') {
            return $symbol . $formatted;
        }
        if ($pattern == 'right-space') {
            return $formatted . ' ' . $symbol;
        }
        if ($pattern == 'left-space') {
            return $symbol . ' ' . $formatted;
        }

        return $symbol . $formatted;
    }
}

if (!function_exists('slugify')) {
    function slugify($string)
    {
        // Normalize the string to NFC (Normalization Form C) to preserve combining characters
        $string = Normalizer::normalize(trim($string), Normalizer::FORM_C);

        // Replace spaces and consecutive dashes with a single dash
        $slug = preg_replace('/[\s-]+/u', '-', $string);

        // Retain all valid Unicode letters, numbers, and combining characters
        $slug = preg_replace('/[^\p{L}\p{M}\p{N}-]/u', '', $slug);

        // Return the slug
        return mb_strtolower($slug, 'UTF-8');
    }
}

if (!function_exists('ellipsis')) {
    function ellipsis($long_string, $max_character = 30)
    {
        $long_string  = strip_tags($long_string);
        $short_string = strlen($long_string) > $max_character ? mb_substr($long_string, 0, $max_character) . "..." : $long_string;
        return $short_string;
    }
}

if (!function_exists('htmlspecialchars_decode_')) {
    function htmlspecialchars_decode_($description = '')
    {
        return htmlspecialchars_decode($description ?? "");
    }
}

// RANDOM NUMBER GENERATOR FOR ELSEWHERE
if (!function_exists('random')) {
    function random($length_of_string, $lowercase = false)
    {
        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Shufle the $str_result and returns substring
        // of specified length
        $randVal = substr(str_shuffle($str_result), 0, $length_of_string);
        if ($lowercase) {
            $randVal = strtolower($randVal);
        }
        return $randVal;
    }
}

if (!function_exists('get_settings')) {
    function get_settings($type = "", $return_type = false)
    {
        static $memo = null;
        if ($memo === null) {
            $memo = Cache::remember('settings.all', 3600, function () {
                return App\Models\Setting::pluck('description', 'type')->toArray();
            });
        }

        if (!array_key_exists($type, $memo)) {
            return false;
        }

        $description = $memo[$type];
        if ($return_type === true) {
            return json_decode($description, true);
        }
        if ($return_type === "object") {
            return json_decode($description);
        }

        return $description;
    }
}


if (!function_exists('get_ordered_course_lessons')) {
    function get_ordered_course_lessons($courseId)
    {
        $lessons = [];
        $sections = App\Models\Section::where('course_id', $courseId)->orderBy('sort')->get();

        foreach ($sections as $section) {
            $sectionLessons = App\Models\Lesson::whereStatus(1)->active()
                ->where('section_id', $section->id)
                ->orderBy('sort')
                ->get();

            foreach ($sectionLessons as $lesson) {
                $lessons[] = $lesson;
            }
        }

        return $lessons;
    }
}

if (!function_exists('get_drip_max_completed_index')) {
    function get_drip_max_completed_index($courseId, $completedLessonIds)
    {
        if (!is_array($completedLessonIds)) {
            $completedLessonIds = [];
        }

        $maxIndex = -1;
        $orderedLessons = get_ordered_course_lessons($courseId);

        foreach ($orderedLessons as $index => $lesson) {
            if (in_array($lesson->id, $completedLessonIds)) {
                $maxIndex = $index;
            }
        }

        return $maxIndex;
    }
}

if (!function_exists('is_lesson_drip_unlocked')) {
    function is_lesson_drip_unlocked($courseId, $userId, $lessonId)
    {
        if (check_course_admin($userId) == 'admin' && is_course_instructor($courseId, $userId) == true) {
            return true;
        }

        $course = App\Models\Course::find($courseId);
        if (!$course || $course->enable_drip_content != 1) {
            return true;
        }

        $watchHistory = App\Models\Watch_history::where('course_id', $courseId)
            ->where('student_id', $userId)
            ->first();

        $completedLessons = [];
        if ($watchHistory) {
            $completedLessons = json_decode($watchHistory->completed_lesson, true) ?? [];
            if (!is_array($completedLessons)) {
                $completedLessons = [];
            }
        }

        if (in_array($lessonId, $completedLessons)) {
            return true;
        }

        $orderedLessons = get_ordered_course_lessons($courseId);
        $lessonIndex = null;

        foreach ($orderedLessons as $index => $lesson) {
            if ($lesson->id == $lessonId) {
                $lessonIndex = $index;
                break;
            }
        }

        if ($lessonIndex === null) {
            return false;
        }

        $maxCompletedIndex = get_drip_max_completed_index($courseId, $completedLessons);

        if ($maxCompletedIndex == -1) {
            return $lessonIndex === 0;
        }

        return $lessonIndex <= $maxCompletedIndex + 1;
    }
}

if (!function_exists('viewLesson')) {
    function viewLesson($courseId, $lessonId)
    {
        if (!auth()->check()) {
            return false;
        }

        $course = App\Models\Course::find($courseId);
        if (!$course) {
            return false;
        }

        if ($course->enable_drip_content != 1) {
            return true;
        }

        $lesson = App\Models\Lesson::find($lessonId);
        if (!$lesson) {
            return false;
        }

        return is_lesson_drip_unlocked($courseId, auth()->user()->id, $lessonId);
    }
}

if (!function_exists('lastUpdate')) {
    function lastUpdate($courseId)
    {
        $lesson = App\Models\Lesson::where('course_id',$courseId)->orderBy('id','DESC')->first();
        $course = App\Models\Course::where('id', $courseId)->first();
        if(!$lesson){
            return $course->created_at;
        }

        return $lesson->created_at;
    }
}


if (!function_exists('get_theme_settings')) {
    function get_theme_settings($type = "", $return_type = false)
    {
        static $memo = null;
        if ($memo === null) {
            $memo = Cache::remember('theme_settings.all', 3600, function () {
                if (!class_exists(\Modules\Theme\App\Models\theme_setting::class)) {
                    return [];
                }
                return \Modules\Theme\App\Models\theme_setting::pluck('description', 'type')->toArray();
            });
        }

        if (!array_key_exists($type, $memo)) {
            return false;
        }

        $description = $memo[$type];
        if ($return_type === true) {
            return json_decode($description, true);
        }
        if ($return_type === "object") {
            return json_decode($description);
        }

        return $description;
    }
}

if (!function_exists('is_national_image_required')) {
    /**
     * Whether birth certificate / national ID image is required on register & profile.
     * Defaults to required when the setting is missing (legacy behavior).
     */
    function is_national_image_required(): bool
    {
        $value = get_theme_settings('national_image_required');

        if ($value === false || $value === null || $value === '') {
            return true;
        }

        return (string) $value === '1';
    }
}

if (!function_exists('is_email_required')) {
    /**
     * Whether email is required on register & profile.
     * Defaults to required when the setting is missing (legacy behavior).
     */
    function is_email_required(): bool
    {
        $value = get_theme_settings('email_required');

        if ($value === false || $value === null || $value === '') {
            return true;
        }

        return (string) $value === '1';
    }
}

if (!function_exists('is_national_id_required')) {
    /**
     * Whether national ID is required on register & profile.
     * Defaults to required when the setting is missing (legacy behavior).
     */
    function is_national_id_required(): bool
    {
        $value = get_theme_settings('national_id_required');

        if ($value === false || $value === null || $value === '') {
            return true;
        }

        return (string) $value === '1';
    }
}

if (!function_exists('student_grade_source')) {
    /**
     * Where student grades (صفوف) come from on register/profile.
     * Values: "category" (parents) or "subcategory" (children).
     * Defaults to category when the setting is missing.
     */
    function student_grade_source(): string
    {
        $value = get_theme_settings('student_grade_source');

        if ($value === 'subcategory') {
            return 'subcategory';
        }

        return 'category';
    }
}

if (!function_exists('get_student_grade_categories')) {
    /**
     * Categories shown as الصف الدراسي based on student_grade_source.
     */
    function get_student_grade_categories()
    {
        if (student_grade_source() === 'subcategory') {
            return \App\Models\Category::where('parent_id', '>', 0)
                ->orderBy('sort')
                ->orderBy('title')
                ->get();
        }

        return \App\Models\Category::where('parent_id', 0)
            ->orderBy('sort')
            ->orderBy('title')
            ->get();
    }
}

if (!function_exists('student_grade_category_rule')) {
    /**
     * Validation rule ensuring the chosen category matches the configured grade source.
     */
    function student_grade_category_rule(): array
    {
        return ['required', function ($attribute, $value, $fail) {
            $category = \App\Models\Category::find($value);
            if (!$category) {
                $fail(get_phrase('الصف الدراسي غير صالح'));
                return;
            }

            $isSubcategory = (int) $category->parent_id > 0;
            $expectsSubcategory = student_grade_source() === 'subcategory';

            if ($expectsSubcategory !== $isSubcategory) {
                $fail(get_phrase('الصف الدراسي غير صالح'));
            }
        }];
    }
}

if (!function_exists('theme_color_palettes')) {
    function theme_color_palettes(): array
    {
        static $palettes = null;
        if ($palettes === null) {
            $path = null;
            if (function_exists('module_path')) {
                $path = module_path('Theme', 'config/color_themes.php');
            }
            if (!is_string($path) || !file_exists($path)) {
                $path = base_path('Modules/Theme/config/color_themes.php');
            }
            $palettes = file_exists($path) ? require $path : [];
        }

        return is_array($palettes) ? $palettes : [];
    }
}

if (!function_exists('normalize_hex_color')) {
    function normalize_hex_color(?string $hex, string $fallback = '#009CCC'): string
    {
        $hex = trim((string) $hex);
        if ($hex === '') {
            return strtoupper($fallback);
        }
        if ($hex[0] !== '#') {
            $hex = '#' . $hex;
        }
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) {
            return strtoupper($fallback);
        }

        return strtoupper($hex);
    }
}

if (!function_exists('hex_to_rgb_csv')) {
    function hex_to_rgb_csv(?string $hex, string $fallback = '#009CCC'): string
    {
        $hex = ltrim(normalize_hex_color($hex, $fallback), '#');
        return hexdec(substr($hex, 0, 2)) . ', ' . hexdec(substr($hex, 2, 2)) . ', ' . hexdec(substr($hex, 4, 2));
    }
}

if (!function_exists('darken_hex_color')) {
    function darken_hex_color(?string $hex, float $percent = 12, string $fallback = '#009CCC'): string
    {
        $hex = ltrim(normalize_hex_color($hex, $fallback), '#');
        $ratio = max(0, min(100, $percent)) / 100;
        $r = (int) round(hexdec(substr($hex, 0, 2)) * (1 - $ratio));
        $g = (int) round(hexdec(substr($hex, 2, 2)) * (1 - $ratio));
        $b = (int) round(hexdec(substr($hex, 4, 2)) * (1 - $ratio));

        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }
}

if (!function_exists('get_active_theme_colors')) {
    /**
     * Resolve active website brand colors (hex) from settings + presets.
     *
     * @return array{theme:string,accent:string,accent_hover:string,primary:string,secondary:string,gray:string}
     */
    function get_active_theme_colors(): array
    {
        $defaults = [
            'theme' => 'sky',
            'accent' => '#009CCC',
            'accent_hover' => '#008CB8',
            'primary' => '#1C2B3D',
            'secondary' => '#32B4DC',
            'gray' => '#78828C',
        ];

        $palettes = theme_color_palettes();
        $themeKey = (string) (get_theme_settings('color_theme') ?: 'sky');

        if ($themeKey !== 'custom' && isset($palettes[$themeKey])) {
            $preset = $palettes[$themeKey];

            return [
                'theme' => $themeKey,
                'accent' => normalize_hex_color($preset['accent'], $defaults['accent']),
                'accent_hover' => normalize_hex_color($preset['accent_hover'] ?? darken_hex_color($preset['accent']), $defaults['accent_hover']),
                'primary' => normalize_hex_color($preset['primary'], $defaults['primary']),
                'secondary' => normalize_hex_color($preset['secondary'], $defaults['secondary']),
                'gray' => normalize_hex_color($preset['gray'], $defaults['gray']),
            ];
        }

        $accent = normalize_hex_color(get_theme_settings('color_accent') ?: $defaults['accent'], $defaults['accent']);
        $hover = get_theme_settings('color_accent_hover');

        return [
            'theme' => $themeKey === 'custom' ? 'custom' : (isset($palettes[$themeKey]) ? $themeKey : 'custom'),
            'accent' => $accent,
            'accent_hover' => normalize_hex_color($hover ?: darken_hex_color($accent), $defaults['accent_hover']),
            'primary' => normalize_hex_color(get_theme_settings('color_primary') ?: $defaults['primary'], $defaults['primary']),
            'secondary' => normalize_hex_color(get_theme_settings('color_secondary') ?: $defaults['secondary'], $defaults['secondary']),
            'gray' => normalize_hex_color(get_theme_settings('color_gray') ?: $defaults['gray'], $defaults['gray']),
        ];
    }
}

if (!function_exists('lesson_progress')) {
    function lesson_progress($lesson_id = "", $user_id = "", $course_id = "")
    {
        if ($user_id == "") {
            $user_id = Auth()->user()->id;
        }
        if ($course_id == "") {
            $course_id = DB::table('lessons')->where('id', $lesson_id)->value('course_id');
        }

        $watch_history = DB::table('watch_histories')
            ->where('student_id', $user_id)
            ->where('course_id', $course_id)
            ->first(); // Get the first record as an object

        if ($watch_history && !empty($watch_history->completed_lesson)) {
            $lesson_ids = json_decode($watch_history->completed_lesson, true); // Decode the string
            if (is_array($lesson_ids) && in_array($lesson_id, $lesson_ids)) {
                return 1;
            }
        }
        return 0;
    }
}

if (!function_exists('htmlspecialchars_decode')) {
    function htmlspecialchars_decode($description = '')
    {
        return htmlspecialchars_decode($description ?? "");
    }
}

if (!function_exists('get_frontend_settings')) {
    function get_frontend_settings($type = "", $return_type = false)
    {
        static $memo = null;
        if ($memo === null) {
            $memo = Cache::remember('frontend_settings.all', 3600, function () {
                return DB::table('frontend_settings')->pluck('value', 'key')->toArray();
            });
        }

        if (!array_key_exists($type, $memo)) {
            return false;
        }

        $value = $memo[$type];
        if ($return_type === true) {
            return json_decode($value, true);
        }
        if ($return_type === "object") {
            return json_decode($value);
        }

        return $value;
    }
}
if (!function_exists('check_registered')) {
    function check_registered($email = "")
    {

        if ($email != '') {
            $check = DB::table('users')->where('email', $email)->exists();
        }
        return $check;
    }
}

if (!function_exists('is_permission')) {
    function is_permission($permission_for = '', $admin_id = '')
    {

        // GET THE LOGGEDIN IN ADMIN ID
        if (empty($admin_id)) {
            $admin_id = Auth()->user()->id;
        }

        $get_admin_permissions = DB::table('permissions')->where('admin_id', $admin_id);

        if (is_root_admin($admin_id) || is_super_admin($admin_id)) {
            return true;
        } else {
            $get_admin_permissions = $get_admin_permissions->firstOrNew();
            $permissions           = json_decode($get_admin_permissions->permissions, true);
            if (in_array($permission_for, $permissions) && is_array($permissions)) {
                return true;
            } else {
                return false;
            }
        }
    }
}

// count comments by blog id
if (!function_exists('count_comments_by_blog_id')) {
    function count_comments_by_blog_id($id)
    {
        $count_comments = App\Models\BlogComment::where('blog_id', $id)->count();
        $comments       = format_count($count_comments) . ' ' . get_phrase('comment');
        if ($count_comments > 1) {
            $comments = format_count($count_comments) . ' ' . get_phrase('comments');
        }
        return $comments;
    }
}

// count likes by blog id
if (!function_exists('count_likes_by_blog_id')) {
    function count_likes_by_blog_id($id)
    {
        $count_likes = App\Models\BlogLike::where('blog_id', $id)->count();
        $likes       = format_count($count_likes) . ' ' . get_phrase('like');
        if ($count_likes > 1) {
            $likes = format_count($count_likes) . ' ' . get_phrase('likes');
        }
        return $likes;
    }
}

// format numbers
if (!function_exists('format_count')) {
    function format_count($num)
    {
        if ($num >= 1000 && $num < 1000000) {
            $count = round($num / 1000, 1) . get_phrase('K');
        } elseif ($num >= 1000000) {
            $count = round($num / 1000000, 1) . get_phrase('M');
        } elseif ($num >= 10000000) {
            $count = round($num / 10000000, 1) . get_phrase('B');
        } else {
            $count = $num;
        }
        return $count;
    }
}
// get by conversation media files
if (!function_exists('get_files')) {
    function get_files($conversation_id)
    {
        $file = App\Models\MediaFile::where('chat_id', $conversation_id)->get();
        return $file;
    }
}
// instructor experience
if (!function_exists('instructor_experience')) {
    function instructor_experience($user_id)
    {
        $first_course = App\Models\Course::where('user_id', $user_id)->firstOrNew();
        $time_diff    = time() - strtotime($first_course->created_at);
        $units        = ["year" => 31536000, "month" => 2592000];

        foreach ($units as $unit => $value) {
            if ($time_diff < $value) {
                continue;
            }

            $time_units = floor($time_diff / $value);
            return ($time_units <= 1) ? "1 $unit" : $time_units . ' ' . $unit . 's';
        }
        return get_phrase('Recently appointed');
    }
}
// instructor reviews
if (!function_exists('instructor_reviews')) {
    function instructor_reviews($user_id)
    {
        $reviews = App\Models\Review::join('courses', 'reviews.course_id', '=', 'courses.id')
            ->select('reviews.*', 'courses.user_id as instructor_id')
            ->where('courses.user_id', $user_id)
            ->count();

        return ($reviews > 1) ? "{$reviews} " . get_phrase('reviews') : "{$reviews} " . get_phrase('review');
    }
}
// instructor rating
if (!function_exists('instructor_rating')) {
    function instructor_rating($user_id)
    {
        $rating = App\Models\Review::join('courses', 'reviews.course_id', '=', 'courses.id')
            ->select('reviews.*', 'courses.user_id as instructor_id')
            ->where('courses.user_id', $user_id)
            ->sum('reviews.rating');

        return number_format(($rating / 5), 1);
    }
}

// count lessons by instructor
if (!function_exists('count_instructor_lesson')) {
    function count_instructor_lesson($user_id)
    {
        $courses = App\Models\Course::where('user_id', $user_id)->pluck('id')->toArray();
        $lessons = App\Models\Lesson::whereIn('course_id', $courses)->count();
        return ($lessons > 1) ? "{$lessons} " . get_phrase('lessons') : "{$lessons} " . get_phrase('lesson');
    }
}

// count course by category
if (!function_exists('count_category_courses')) {
    function count_category_courses($category_id)
    {
        $category = App\Models\Category::where('id', $category_id)->firstOrNew();

        if ($category) {
            if ($category->parent_id > 0) {
                $courses = App\Models\Course::where('status', 'active')->where('category_id', $category_id)->count();
            } else {
                $categories = App\Models\Category::where('parent_id', $category_id)->pluck('id')->toArray();
                $categories[] = $category_id; //add this parent category to the array
                $courses    = App\Models\Course::where('status', 'active')->whereIn('category_id', $categories)->count();
            }
            return $courses;
        } else {
            return '0';
        }
    }
}

// count user certificates
if (!function_exists('count_user_certificate')) {
    function count_user_certificate($user_id)
    {
        $certificates = App\Models\Certificate::where('user_id', $user_id)->count();
        return ($certificates > 1) ? "{$certificates} " . get_phrase('certificates') : "{$certificates} " . get_phrase('certificate');
    }
}

// top category
if (!function_exists('top_categories')) {
    function top_categories()
    {
        $data = App\Models\Payment_history::join('courses', 'payment_histories.course_id', 'courses.id')
            ->join('categories', 'courses.category_id', 'categories.id')
            ->select(
                'payment_histories.course_id',
                'courses.category_id',
                'categories.*',
            )
            ->take(5)->get();

        $categoryCounts   = $data->groupBy('category_id')->map->count();
        $sortedCategories = $categoryCounts->sortDesc()->keys()->all();

        if (count($sortedCategories) > 0) {
            $categories = App\Models\Category::whereIn('id', $sortedCategories)->get();
            return $categories;
        }
        return [];
    }
}

// top category
if (!function_exists('get_blog_tags')) {
    function get_blog_tags()
    {
        $blogs = App\Models\Blog::whereNotNull('keywords')->get(['keywords']);

        $tags = $blogs->flatMap(function ($blog) {
            $keywords = json_decode($blog->keywords, true);
            return collect($keywords ?? []);
        })->pluck('value')->unique()->take(15);

        return $tags;
    }
}

// Get Home page Settings Data
if (!function_exists('replace_url_symbol')) {
    function replace_url_symbol($str)
    {
        $pattern = '/[\?#&\/:@=%]/';
        return preg_replace($pattern, '-', $str);
    }
}

// count bootcamps
if (!function_exists('count_bootcamps_by_category')) {
    function count_bootcamps_by_category($id = "")
    {
        if ($id != '') {
            $bootcamps = DB::table('bootcamps')->where('status', 1)->where('category_id', $id)->count();
            return $bootcamps;
        }
    }
}

// count bootcamp modules
if (!function_exists('count_bootcamp_modules')) {
    function count_bootcamp_modules($id = "")
    {
        $query = DB::table('bootcamp_modules');
        if ($id) {
            $query = $query->where('bootcamp_id', $id);
        }
        return $query->count();
    }
}

// count bootcamp live classes
if (!function_exists('count_bootcamp_classes')) {
    function count_bootcamp_classes($id = "", $type = "bootcamp")
    {
        $query = DB::table('bootcamp_live_classes')
            ->join('bootcamp_modules', 'bootcamp_live_classes.module_id', 'bootcamp_modules.id')
            ->join('bootcamps', 'bootcamp_modules.bootcamp_id', 'bootcamps.id');
        if ($id && $type == 'bootcamp') {
            $query = $query->where('bootcamp_modules.bootcamp_id', $id);
        } elseif ($id && $type == 'module') {
            $query = $query->where('bootcamp_live_classes.module_id', $id);
        }
        return $query->count();
    }
}

// activated theme path
if (!function_exists('theme_path')) {
    function theme_path()
    {
        return 'frontend.' . get_frontend_settings('theme') . '.';
    }
}

// bootcamp purchase
if (!function_exists('is_purchased_bootcamp')) {
    function is_purchased_bootcamp($bootcamp_id, $user_id = null)
    {
        $user_id  = $user_id ?? auth()->user()->id;
        $purchase = App\Models\BootcampPurchase::where('user_id', $user_id)->where('bootcamp_id', $bootcamp_id)->count();
        return $purchase;
    }
}

// bootcamp enrolls
if (!function_exists('bootcamp_enrolls')) {
    function bootcamp_enrolls($bootcamp_id, $user_id = null)
    {
        $bootcamp = App\Models\Bootcamp::where('id', $bootcamp_id)->firstOrNew();
        $user_id  = $user_id ?? $bootcamp->user_id;

        $purchases = App\Models\BootcampPurchase::join('bootcamps', 'bootcamp_purchases.bootcamp_id', 'bootcamps.id')
            ->select('bootcamp_purchases.*', 'bootcamps.user_id as creator')
            ->where('bootcamp_purchases.bootcamp_id', $bootcamp_id)
            ->where('bootcamps.user_id', $user_id)->count();
        return $purchases;
    }
}

// check online class
if (!function_exists('class_started')) {
    function class_started($class_id)
    {
        $current_time  = time();
        $extended_time = $current_time + (60 * 15);

        $bootcamp = App\Models\BootcampLiveClass::where('id', $class_id)
            ->where('force_stop', 0)
            ->whereNotNull('joining_data')
            ->where('start_time', '<', $extended_time)
            ->where('end_time', '>', $current_time)
            ->firstOrNew();
        return $bootcamp ? true : null;
    }
}

// check online class
if (!function_exists('count_instructor_bootcamps')) {
    function count_instructor_bootcamps($user_id)
    {
        if ($user_id != '') {
            $count_course = DB::table('bootcamps')->where('status', 1)->where('user_id', $user_id)->count();
            return ($count_course > 1) ? "{$count_course} " . get_phrase('Bootcamps') : "{$count_course} " . get_phrase('Bootcamp');
        }
    }
}

// delete relevant bootcamp data
if (!function_exists('remove_bootcamp_data')) {
    function remove_bootcamp_data($id)
    {
        remove_module_data($id);
        App\Models\Bootcamp::where('id', $id)->latest('id')->delete();
    }
}

// delete relevant module
if (!function_exists('remove_module_data')) {
    function remove_module_data($id)
    {
        $modules = App\Models\BootcampModule::where('bootcamp_id', $id)->latest('id')->get();
        foreach ($modules as $module) {
            remove_live_class_data($module->id);
            remove_resource_data($module->id);
        }
        App\Models\BootcampModule::where('bootcamp_id', $id)->delete();
    }
}

// delete relevant live class
if (!function_exists('remove_live_class_data')) {
    function remove_live_class_data($id)
    {
        App\Models\BootcampLiveClass::where('module_id', $id)->delete();
    }
}

// delete relevant resource
if (!function_exists('remove_resource_data')) {
    function remove_resource_data($id)
    {
        App\Models\BootcampResource::where('module_id', $id)->delete();
    }
}

// player settings
if (! function_exists('get_player_settings')) {
    function get_player_settings($title = "", $return_type = false)
    {
        $value = App\Models\PlayerSettings::where('title', $title);
        if ($value->count() > 0) {
            if ($return_type === true) {
                return json_decode($value->value('description'), true);
            } elseif ($return_type === "object") {
                return json_decode($value->value('description'));
            } else {
                return $value->value('description');
            }
        } else {
            return false;
        }
    }
}

// get reserved team members
if (! function_exists('reserved_team_members')) {
    function reserved_team_members($package_id)
    {
        $count_members = App\Models\TeamPackageMember::where('team_package_id', $package_id)->count();
        return $count_members;
    }
}

// get team purchases
if (! function_exists('team_package_purchases')) {
    function team_package_purchases($package_id)
    {
        $count_purchase = App\Models\TeamPackagePurchase::where('package_id', $package_id)->count();
        return $count_purchase;
    }
}

// get team packages by course category
if (! function_exists('team_packages_by_course_category')) {
    function team_packages_by_course_category($category_id)
    {
        $course_ids          = App\Models\Course::where('category_id', $category_id)->pluck('id')->toArray();
        $count_team_packages = [];
        foreach ($course_ids as $course_id) {
            $count_team_packages[] = App\Models\TeamTrainingPackage::where('course_id', $course_id)->count();
        }
        return array_sum($count_team_packages);
    }
}

// team package purchase
if (! function_exists('is_purchased_package')) {
    function is_purchased_package($package_id, $user_id = null)
    {
        $user_id  = $user_id ?? auth()->user()->id;
        $purchase = App\Models\TeamPackagePurchase::where('user_id', $user_id)->where('package_id', $package_id)->firstOrNew();
        return $purchase;
    }
}

// get instructor course revenue
if (! function_exists('instructor_course_revenue')) {
    function instructor_course_revenue($user_id = null)
    {
        $id             = $user_id ?? auth()->user()->id;
        $course_revenue = App\Models\Course::join('payment_histories', 'courses.id', 'payment_histories.course_id')
            ->select('payment_histories.*', 'courses.id as course_id')
            ->where('courses.user_id', $id)
            ->sum('payment_histories.instructor_revenue');
        return $course_revenue;
    }
}

// get instructor bootcamp revenue
if (! function_exists('instructor_bootcamp_revenue')) {
    function instructor_bootcamp_revenue($user_id = null)
    {
        $id               = $user_id ?? auth()->user()->id;
        $bootcamp_revenue = App\Models\BootcampPurchase::join('bootcamps', 'bootcamp_purchases.bootcamp_id', 'bootcamps.id')
            ->where('bootcamps.user_id', $id)->sum('bootcamp_purchases.instructor_revenue');
        return $bootcamp_revenue;
    }
}

// get instructor team training revenue
if (! function_exists('instructor_team_training_revenue')) {
    function instructor_team_training_revenue($user_id = null)
    {
        $id      = $user_id ?? auth()->user()->id;
        $revenue = App\Models\TeamPackagePurchase::join('team_training_packages', 'team_package_purchases.package_id', 'team_training_packages.id')
            ->where('team_training_packages.user_id', $id)->sum('team_package_purchases.instructor_revenue');
        return $revenue;
    }
}

// get instructor bootcamp revenue
if (! function_exists('instructor_tution_revenue')) {
    function instructor_tution_revenue($user_id = null)
    {
        $id               = $user_id ?? auth()->user()->id;
        $tution_revenue = App\Models\TutorBooking::where('tutor_id', $id)->sum('instructor_revenue');
        return $tution_revenue;
    }
}

// get instructor total revenue
if (! function_exists('instructor_total_revenue')) {
    function instructor_total_revenue($user_id = null)
    {
        $id            = $user_id ?? auth()->user()->id;
        $total_revenue = instructor_course_revenue($id) + instructor_bootcamp_revenue($id) + instructor_team_training_revenue($id) + + instructor_tution_revenue($id);
        return $total_revenue;
    }
}

// get instructor total payout
if (! function_exists('instructor_total_payout')) {
    function instructor_total_payout($user_id = null)
    {
        $id           = $user_id ?? auth()->user()->id;
        $total_payout = App\Models\Payout::where(['user_id' => $id, 'status' => 1])->sum('amount');
        return $total_payout;
    }
}

// get instructor available balance
if (! function_exists('instructor_available_balance')) {
    function instructor_available_balance($user_id = null)
    {
        // sum all the revenue sources (course, ebook, bootcamp, team_training etc)
        $id                = $user_id ?? auth()->user()->id;
        $available_balance = instructor_total_revenue($id) - instructor_total_payout($id);
        return $available_balance;
    }
}

if(! function_exists('total_schedule_by_tutor_id')) {
    function total_schedule_by_tutor_id($tutor_id = null)
    {
        $todayStart = strtotime('today');

        $total_schedule = App\Models\TutorSchedule::where('tutor_id', $tutor_id)->where('start_time', '>=', $todayStart)->count();
        return $total_schedule;
    }
}

if(! function_exists('total_booked_schedule_by_tutor_id')) {
    function total_booked_schedule_by_tutor_id($tutor_id = null)
    {
        $todayStart = strtotime('today');

        $total_schedule = App\Models\TutorBooking::where('tutor_id', $tutor_id)->where('start_time', '>=', $todayStart)->count();
        return $total_schedule;
    }
}

if(! function_exists('total_review_by_tutor_id')) {
    function total_review_by_tutor_id($tutor_id = null)
    {
        $total_review = App\Models\TutorReview::where('tutor_id', $tutor_id)->count();
        return $total_review;
    }
}

if(! function_exists('total_booked_schedule_by_schedule_id')) {
    function total_booked_schedule_by_schedule_id($schedule_id = null)
    {
        $total_booked = App\Models\TutorBooking::where('schedule_id', $schedule_id)->count();
        return $total_booked;
    }
}

// check tution class
if (!function_exists('tution_started')) {
    function tution_started($booking_id)
    {
        $current_time  = time();
        $extended_time = $current_time + (60 * 15);

        $booking = App\Models\TutorBooking::where('id', $booking_id)
            ->whereNotNull('joining_data')
            ->where('start_time', '<', $extended_time)
            ->where('end_time', '>', $current_time)
            ->firstOrNew();
        return $booking ? true : null;
    }
}

if (!function_exists('enroll_status')) {
    function enroll_status($course_id = "", $user_id = "")
    {
        if ($course_id != '' && $user_id != "") {
            $enrolled = App\Models\Enrollment::where('course_id', $course_id)->where('user_id', $user_id)->orderBy('id', 'desc')->first();

            if ($enrolled) {
                // إذا الكورس مدفوع والطالب أخده مجاناً - لا يسمح له بالوصول إلا لو اشتراه
                $course = get_course_info($course_id);
                if ($course->is_paid && empty($enrolled->payment_history_id)) {
                    return 'free_locked'; // كان مجاني وأصبح مدفوع - يحتاج شراء
                }

                $expiry_date = $enrolled->expiry_date;
                if ($expiry_date == null || $expiry_date >= time()) {
                    return 'valid';
                } else {
                    return 'expired';
                }
            } else {
                return false;
            }
        }
    }
}


// Human readable time
if (!function_exists('seconds_to_time_format')) {
    function seconds_to_time_format($seconds = "0")
    {
        if ($seconds) {
            $hours = floor($seconds / 3600); // Calculate the number of hours
            $minutes = floor(($seconds % 3600) / 60); // Calculate the number of minutes
            $totalSeconds = $seconds % 60; // Calculate the number of seconds

            return sprintf("%02d:%02d:%02d", $hours, $minutes, $totalSeconds); // Format the time as HH:MM:SS
        } else {
            $duration = '00:00:00';
        }
        return $duration;
    }
}

if (! function_exists('remove_js')) {
    function remove_js($description = '', $convert_string = false) {

        if ($convert_string == true) {
            $description = nl2br(htmlspecialchars($description));
        } else {
            //make script to string
            $description = str_replace("&lt;script&gt;", "", $description);
            $description = str_replace("&lt;/script&gt;", "", $description);

            //removing <script> tags
            $description = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $description);
            $description = preg_replace("/[<][^<]*script.*[>].*[<].*[\/].*script*[>]/i", "", $description);

            //removing inline js events
            $description = preg_replace("/([ ]on[a-zA-Z0-9_-]{1,}=\".*\")|([ ]on[a-zA-Z0-9_-]{1,}='.*')|([ ]on[a-zA-Z0-9_-]{1,}=.*[.].*)/", "", $description);
            $description = preg_replace('/(<.+?)(?<=\s)on[a-z]+\s*=\s*(?:([\'"])(?!\2).+?\2|(?:\S+?\(.*?\)(?=[\s>])))(.*?>)/i', "$1 $3", $description);

            //removing inline js
            $description = preg_replace("/([ ]href.*=\".*javascript:.*\")|([ ]href.*='.*javascript:.*')|([ ]href.*=.*javascript:.*)/i", "", $description);
        }

        return $description;
    }
}

if (!function_exists('get_locked_lesson_ids')) {
    function get_locked_lesson_ids($courseId, $userId) {

        $lockedLessonIds = [];

        if(check_course_admin($userId) == 'admin' && is_course_instructor($courseId, $userId) == true){
            return $lockedLessonIds;
        }

        $course = App\Models\Course::find($courseId);
        if(!$course || $course->enable_drip_content != 1){
            return $lockedLessonIds;
        }

        $lessonHistory = App\Models\Watch_history::where('course_id', $courseId)
            ->where('student_id', $userId)
            ->first();

        $completedLessonArr = [];
        if ($lessonHistory) {
            $completedLessonArr = json_decode($lessonHistory->completed_lesson, true) ?? [];
            if (!is_array($completedLessonArr)) {
                $completedLessonArr = [];
            }
        }

        $maxCompletedIndex = get_drip_max_completed_index($courseId, $completedLessonArr);
        $orderedLessons = get_ordered_course_lessons($courseId);

        foreach ($orderedLessons as $index => $lesson) {
            if (in_array($lesson->id, $completedLessonArr)) {
                continue;
            }

            if ($maxCompletedIndex == -1) {
                if ($index !== 0) {
                    $lockedLessonIds[] = $lesson->id;
                }
            } elseif ($index > $maxCompletedIndex + 1) {
                $lockedLessonIds[] = $lesson->id;
            }
        }

        return $lockedLessonIds;
    }
}


if (!function_exists('get_watched_duration')) {
    function get_watched_duration($lessonId, $userId) {
        $query = DB::table('watch_durations')
                    ->where('watched_lesson_id', $lessonId)
                    ->where('watched_student_id', $userId)
                    ->first();

        return json_encode($query);
    }
}

if (!function_exists('next_lesson')) {
    function next_lesson($course_id = "", $lesson_id = "")
    {
        // Get all lessons for the given course, ordered by section and then by sort order
        $lesson_list = App\Models\Lesson::whereStatus(1)
            ->active()
            ->where('course_id', $course_id)
            ->orderBy('section_id', 'asc')
            ->orderBy('sort', 'asc')
            ->get();

        // Find the current lesson position in the list
        $current_index = -1;
        foreach ($lesson_list as $index => $lesson) {
            if ($lesson->id == $lesson_id) {
                $current_index = $index;
                break;
            }
        }

        // If the lesson is found and there's a next lesson
        if ($current_index != -1 && isset($lesson_list[$current_index + 1])) {
            // Return the next lesson's ID
            return $lesson_list[$current_index + 1]->id;
        } else {
            // Return null if no next lesson exists
            return null;  // or 'No next lesson'
        }
    }
}


if(!function_exists('check_recaptcha')){
    function check_recaptcha($GRecaptchaResponse = "")
    {
        if (isset($GRecaptchaResponse)) {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $secret = get_frontend_settings('recaptcha_secretkey');
            $data = array(
                'secret' => $secret,
                'response' => $GRecaptchaResponse
            );
            $query = http_build_query($data);
            $options = array(
                'http' => array(
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                        "Content-Length: " . strlen($query) . "\r\n" .
                        "User-Agent:MyAgent/1.0\r\n",
                    'method' => 'POST',
                    'content' => $query
                )
            );
            $context  = stream_context_create($options);
            $verify = file_get_contents($url, false, $context);
            $captcha_success = json_decode($verify);
            if ($captcha_success->success == false) {
                return false;
            } else if ($captcha_success->success == true) {
                return true;
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('get_saudi_regions')) {
    function get_saudi_regions(): array
    {
        return [
            'الرياض',
            'مكة المكرمة',
            'المدينة المنورة',
            'القصيم',
            'المنطقة الشرقية',
            'عسير',
            'تبوك',
            'حائل',
            'الحدود الشمالية',
            'جازان',
            'نجران',
            'الباحة',
            'الجوف',
        ];
    }
}

if (!function_exists('iqama_validation_rules')) {
    function iqama_validation_rules(?int $exceptUserId = null, bool $required = true): array
    {
        $uniqueRule = $exceptUserId
            ? 'unique:users,national_id,' . $exceptUserId
            : 'unique:users,national_id';

        return [
            $required ? 'required' : 'nullable',
            'numeric',
            'digits:10',
            'regex:/^[12]\d{9}$/',
            $uniqueRule,
        ];
    }
}

if (!function_exists('iqama_validation_messages')) {
    function iqama_validation_messages(): array
    {
        return [
            'national_id.required' => 'رقم الإقامة مطلوب.',
            'national_id.numeric'  => 'يجب أن يكون رقم الإقامة أرقامًا فقط.',
            'national_id.digits'   => 'يجب أن يكون رقم الإقامة مكونًا من 10 أرقام.',
            'national_id.regex'    => 'رقم الإقامة يجب أن يبدأ بـ 1 أو 2 ويتكون من 10 أرقام.',
            'national_id.unique'   => 'رقم الإقامة مستخدم بالفعل.',
            'goverment.required'   => 'المنطقة مطلوبة.',
        ];
    }
}

if (!function_exists('saudi_phone_validation_rules')) {
    function saudi_phone_validation_rules(bool $required = true): array
    {
        return [
            $required ? 'required' : 'nullable',
            'numeric',
            'digits:10',
            'regex:/^05\d{8}$/',
        ];
    }
}

if (!function_exists('saudi_phone_validation_messages')) {
    function saudi_phone_validation_messages(): array
    {
        return [
            'phone.required'        => 'رقم الجوال مطلوب.',
            'phone.numeric'         => 'يجب أن يكون رقم الجوال أرقامًا فقط.',
            'phone.digits'          => 'يجب أن يكون رقم الجوال مكونًا من 10 أرقام.',
            'phone.regex'           => 'رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.',
            'phone.different'       => 'يجب أن يكون رقم الجوال مختلفًا عن رقم ولي الأمر.',
     
        ];
    }
}

if (!function_exists('get_all_admin_permission_keys')) {
    function get_all_admin_permission_keys(): array
    {
        return Cache::remember('admin.permission.keys.all', 3600, function () {
            $path = resource_path('views/admin/admin/permission.blade.php');
            if (!is_readable($path)) {
                return [];
            }

            preg_match_all("/'admin\\.[^']+'/", file_get_contents($path), $matches);
            $keys = array_map(static fn ($key) => trim($key, "'"), $matches[0] ?? []);

            $exclude = [
                'admin.admins.permission.preset',
                'admin.admins.permission.store',
            ];

            return array_values(array_unique(array_diff($keys, $exclude)));
        });
    }
}

if (!function_exists('get_admin_permission_presets')) {
    function get_admin_permission_presets(): array
    {
        return [
            'full_access' => [
                'label' => 'صلاحيات كاملة',
                'description' => 'عرض كامل القائمة الجانبية مثل الأدمن الأساسي',
                'permissions' => get_all_admin_permission_keys(),
            ],
            'reception' => [
                'label' => 'موظف استقبال',
                'description' => 'لوحة التحكم، الطلاب، والاشتراكات',
                'permissions' => [
                    'admin.dashboard',
                    'admin.dashboard.index',
                    'admin.manage.profile',
                    'admin.student.index',
                    'admin.student.search',
                    'admin.student.export',
                    'admin.enroll.history',
                    'admin.enroll.history.search',
                    'admin.student.enroll',
                    'admin.student.get',
                    'admin.student.post',
                ],
            ],
            'content' => [
                'label' => 'مدير محتوى',
                'description' => 'الكورسات، التصنيفات، المنهج، والاختبارات',
                'permissions' => [
                    'admin.dashboard',
                    'admin.dashboard.index',
                    'admin.categories',
                    'admin.category.create',
                    'admin.category.edit',
                    'admin.category.delete',
                    'admin.sub_categories.create',
                    'admin.sub_categories.edit',
                    'admin.sub_categories.delete',
                    'admin.courses',
                    'admin.course.create',
                    'admin.course.edit',
                    'admin.course.delete',
                    'admin.course.status',
                    'admin.course.draft',
                    'admin.course.approval',
                    'admin.course.search',
                    'admin.course.filter',
                    'admin.course.export',
                    'admin.course.view_on_front',
                    'admin.course.course_playing',
                    'admin.section.create',
                    'admin.section.edit',
                    'admin.section.delete',
                    'admin.section.sort',
                    'admin.lesson.create',
                    'admin.lesson.edit',
                    'admin.lesson.delete',
                    'admin.lesson.sort',
                    'admin.lesson.copy_move',
                    'admin.quiz.create',
                    'admin.quiz.choose',
                    'admin.assingemnt.choose',
                    'admin.exams.list',
                    'admin.assignments.list',
                    'admin.bank.question.index',
                    'admin.category.bank.questions.index',
                    'admin.bank.quizs.index',
                ],
            ],
            'instructor_manager' => [
                'label' => 'مشرف مدرسين',
                'description' => 'إدارة المدرسين والكورسات',
                'permissions' => [
                    'admin.dashboard',
                    'admin.dashboard.index',
                    'admin.instructor.index',
                    'admin.instructor.create',
                    'admin.instructor.edit',
                    'admin.instructor.course',
                    'admin.courses',
                    'admin.course.create',
                    'admin.course.edit',
                    'admin.course.approval',
                    'admin.course.search',
                    'admin.course.filter',
                ],
            ],
        ];
    }
}

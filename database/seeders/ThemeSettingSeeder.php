<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSettingSeeder extends Seeder
{
    public function run()
    {
        DB::table('theme_settings')->insert([
            [
                'type' => 'jop_title',
                'description' => 'مستر',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-10-24 15:14:21',
            ],
            [
                'type' => 'name',
                'description' => 'احمد عبد الهادي',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-10-24 15:14:21',
            ],
            [

                'type' => 'instructor_description',
                'description' => '<p style="font-size: clamp(1rem, 5vw, 1.4rem); color:#000;">...</p>',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-03-24 23:28:55',
            ],
            [

                'type' => 'book_status',
                'description' => '1',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-03-24 23:34:25',
            ],
            [

                'type' => 'course_status',
                'description' => '1',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-04-05 16:07:43',
            ],
            [

                'type' => 'sub_status',
                'description' => '0',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-03-24 23:28:00',
            ],
            [

                'type' => 'technical_status',
                'description' => '1',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-09-23 15:35:13',
            ],
            [

                'type' => 'technical',
                'description' => '01140404211',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-03-24 23:28:00',
            ],
            [

                'type' => 'footer_description',
                'description' => '<p class="footer-quote mb-4" style="font-weight:700;">...</p>',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-10-24 15:32:49',
            ],
            [

                'type' => 'terms_status',
                'description' => '1',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-10-25 12:31:44',
            ],
            [
                'type' => 'national_image_required',
                'description' => '1',
                'created_at' => '2026-07-24 00:00:00',
                'updated_at' => '2026-07-24 00:00:00',
            ],
            [
                'type' => 'email_required',
                'description' => '1',
                'created_at' => '2026-07-26 00:00:00',
                'updated_at' => '2026-07-26 00:00:00',
            ],
            [
                'type' => 'national_id_required',
                'description' => '1',
                'created_at' => '2026-07-26 00:00:00',
                'updated_at' => '2026-07-26 00:00:00',
            ],
            [
                'type' => 'student_grade_source',
                'description' => 'category',
                'created_at' => '2026-07-25 00:00:00',
                'updated_at' => '2026-07-25 00:00:00',
            ],
            [
                'type' => 'hero_stats_status',
                'description' => '1',
                'created_at' => '2026-07-25 00:00:00',
                'updated_at' => '2026-07-25 00:00:00',
            ],
            [
                'type' => 'hero_stats_students',
                'description' => '12500',
                'created_at' => '2026-07-25 00:00:00',
                'updated_at' => '2026-07-25 00:00:00',
            ],
            [
                'type' => 'hero_stats_students_label',
                'description' => 'طالب مشترك',
                'created_at' => '2026-07-25 00:00:00',
                'updated_at' => '2026-07-25 00:00:00',
            ],
            [
                'type' => 'hero_stats_youtube',
                'description' => '85000',
                'created_at' => '2026-07-25 00:00:00',
                'updated_at' => '2026-07-25 00:00:00',
            ],
            [
                'type' => 'hero_stats_youtube_label',
                'description' => 'مشترك يوتيوب',
                'created_at' => '2026-07-25 00:00:00',
                'updated_at' => '2026-07-25 00:00:00',
            ],
            [
                'type' => 'hero_stats_facebook',
                'description' => '120000',
                'created_at' => '2026-07-25 00:00:00',
                'updated_at' => '2026-07-25 00:00:00',
            ],
            [
                'type' => 'hero_stats_facebook_label',
                'description' => 'متابع فيسبوك',
                'created_at' => '2026-07-25 00:00:00',
                'updated_at' => '2026-07-25 00:00:00',
            ],
            [

                'type' => 'thumbnail',
                'description' => 'uploads/theme-thumbnail/احمد-عبد-الهادي-1761308485.png',
                'created_at' => '2025-03-24 23:28:00',
                'updated_at' => '2025-10-24 15:21:25',
            ],
            [

                'type' => 'logo',
                'description' => 'uploads/theme-thumbnail/احمد-عبد-الهادي-1761308061.png',
                'created_at' => '2025-04-14 20:57:15',
                'updated_at' => '2025-10-24 15:14:21',
            ],
            [

                'type' => 'telegram_username',
                'description' => '01140404211',
                'created_at' => '2025-10-24 15:14:21',
                'updated_at' => '2025-10-24 15:14:21',
            ],
            [

                'type' => 'dark_logo',
                'description' => 'uploads/theme-thumbnail/احمد-عبد-الهادي-1761310326.png',
                'created_at' => '2025-10-24 15:45:26',
                'updated_at' => '2025-10-24 15:52:06',
            ],
            [

                'type' => 'dark_thumbnail',
                'description' => 'uploads/theme-thumbnail/احمد-عبد-الهادي-1761310326.png',
                'created_at' => '2025-10-24 15:45:26',
                'updated_at' => '2025-10-24 15:52:06',
            ],
            [

                'type' => 'terms_condition',
                'description' => '<p>الحمد لله&nbsp;</p>',
                'created_at' => '2025-10-25 12:31:44',
                'updated_at' => '2025-10-25 12:32:17',
            ],
            [
                'type' => 'color_theme',
                'description' => 'sky',
                'created_at' => '2026-07-24 00:00:00',
                'updated_at' => '2026-07-24 00:00:00',
            ],
            [
                'type' => 'color_accent',
                'description' => '#009CCC',
                'created_at' => '2026-07-24 00:00:00',
                'updated_at' => '2026-07-24 00:00:00',
            ],
            [
                'type' => 'color_accent_hover',
                'description' => '#008CB8',
                'created_at' => '2026-07-24 00:00:00',
                'updated_at' => '2026-07-24 00:00:00',
            ],
            [
                'type' => 'color_primary',
                'description' => '#1C2B3D',
                'created_at' => '2026-07-24 00:00:00',
                'updated_at' => '2026-07-24 00:00:00',
            ],
            [
                'type' => 'color_secondary',
                'description' => '#32B4DC',
                'created_at' => '2026-07-24 00:00:00',
                'updated_at' => '2026-07-24 00:00:00',
            ],
            [
                'type' => 'color_gray',
                'description' => '#78828C',
                'created_at' => '2026-07-24 00:00:00',
                'updated_at' => '2026-07-24 00:00:00',
            ],
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            'hero_stats_status' => '1',
            'hero_stats_students' => '12500',
            'hero_stats_students_label' => 'طالب مشترك',
            'hero_stats_youtube' => '85000',
            'hero_stats_youtube_label' => 'مشترك يوتيوب',
            'hero_stats_facebook' => '120000',
            'hero_stats_facebook_label' => 'متابع فيسبوك',
        ];

        $now = now();

        foreach ($settings as $type => $description) {
            $exists = DB::table('theme_settings')
                ->where('type', $type)
                ->exists();

            if (! $exists) {
                DB::table('theme_settings')->insert([
                    'type' => $type,
                    'description' => $description,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('theme_settings')
            ->whereIn('type', [
                'hero_stats_status',
                'hero_stats_students',
                'hero_stats_students_label',
                'hero_stats_youtube',
                'hero_stats_youtube_label',
                'hero_stats_facebook',
                'hero_stats_facebook_label',
            ])
            ->delete();
    }
};

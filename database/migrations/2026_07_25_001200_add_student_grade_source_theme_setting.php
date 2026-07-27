<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('theme_settings')
            ->where('type', 'student_grade_source')
            ->exists();

        if (! $exists) {
            DB::table('theme_settings')->insert([
                'type' => 'student_grade_source',
                'description' => 'category',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('theme_settings')
            ->where('type', 'student_grade_source')
            ->delete();
    }
};

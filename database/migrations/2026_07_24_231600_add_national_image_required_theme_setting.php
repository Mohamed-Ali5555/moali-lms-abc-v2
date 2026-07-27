<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('theme_settings')
            ->where('type', 'national_image_required')
            ->exists();

        if (! $exists) {
            DB::table('theme_settings')->insert([
                'type' => 'national_image_required',
                'description' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('theme_settings')
            ->where('type', 'national_image_required')
            ->delete();
    }
};

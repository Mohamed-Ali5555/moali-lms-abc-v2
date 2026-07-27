<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['email_required', 'national_id_required'] as $type) {
            $exists = DB::table('theme_settings')
                ->where('type', $type)
                ->exists();

            if (! $exists) {
                DB::table('theme_settings')->insert([
                    'type' => $type,
                    'description' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('national_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('theme_settings')
            ->whereIn('type', ['email_required', 'national_id_required'])
            ->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('national_id')->nullable(false)->change();
        });
    }
};

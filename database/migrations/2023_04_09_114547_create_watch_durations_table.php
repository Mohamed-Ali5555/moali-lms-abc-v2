<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watch_durations', function (Blueprint $table) {
            $table->id();
            $table->integer('current_duration')->nullable();
            $table->longText('watched_counter')->nullable();
            $table->foreignId('watched_student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('watched_lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('watched_course_id')->constrained('courses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_durations');
    }
};

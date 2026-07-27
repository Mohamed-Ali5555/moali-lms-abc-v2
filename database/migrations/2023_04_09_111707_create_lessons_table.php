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
        Schema::create('lessons', function (Blueprint $table) {
           $table->id();
            $table->string('title');
            $table->string('lesson_type');
            $table->string('duration')->nullable();
            $table->string('lesson_src')->nullable();
            $table->integer('is_free')->nullable();
            $table->integer('sort')->nullable();
            $table->longText('description')->nullable();
            $table->integer('status');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('bank_quizs')->onDelete('cascade');
            $table->integer('total_mark')->nullable();
            $table->integer('pass_mark')->nullable();
            $table->integer('retake')->nullable();
            $table->longText('attachment')->nullable();
            $table->string('attachment_type')->nullable();
            $table->text('video_type')->nullable();
            $table->longText('summary')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};

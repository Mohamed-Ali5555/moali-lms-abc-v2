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
        Schema::create('quize_quiestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('bank_quizs')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('bank_questions')->onDelete('cascade');
            $table->integer('sort')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quize_quiestions');
    }
};

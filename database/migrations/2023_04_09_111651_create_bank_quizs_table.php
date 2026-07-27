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
        Schema::create('bank_quizs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->constrained('bank_quizs_categories')->onDelete('cascade');
            $table->string('duration');
            $table->integer('total_mark');
            $table->integer('pass_mark');
            $table->integer('retake');
            $table->integer('sort')->nullable();
            $table->longText('description')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_quizs');
    }
};

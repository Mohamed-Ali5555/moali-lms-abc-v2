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
        Schema::create('bank_questions', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->string('type');
            $table->mediumText('answer')->nullable();
            $table->longText('options')->nullable();
            $table->integer('sort')->nullable();
            $table->foreignId('category_id')->constrained('bank_quizs_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_questions');
    }
};

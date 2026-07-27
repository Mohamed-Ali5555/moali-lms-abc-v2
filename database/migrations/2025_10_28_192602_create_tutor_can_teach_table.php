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
        Schema::create('tutor_can_teach', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('tutor_categories')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('tutor_subjects')->onDelete('cascade');
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('price')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_can_teach');
    }
};

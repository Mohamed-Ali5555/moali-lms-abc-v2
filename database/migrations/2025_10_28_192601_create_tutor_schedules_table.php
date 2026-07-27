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
        Schema::create('tutor_schedules', function (Blueprint $table) {
        $table->id();
            $table->foreignId('booking_id')->constrained('tutor_bookings')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('tutor_categories')->onDelete('cascade');
            $table->integer('tutor_id')->nullable()->default(0);
            $table->integer('subject_id')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->integer('tution_type')->nullable();
            $table->integer('duration')->nullable();
            $table->longText('description')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_schedules');
    }
};

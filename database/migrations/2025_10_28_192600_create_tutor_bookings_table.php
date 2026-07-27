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
        Schema::create('tutor_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice');
            $table->integer('schedule_id')->nullable();
            $table->integer('tutor_id')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->longText('joining_data')->nullable();
            $table->double('price')->default(0);
            $table->double('admin_revenue')->default(0);
            $table->double('instructor_revenue')->default(0);
            $table->double('tax')->default(0);
            $table->string('payment_method')->nullable();
            $table->text('payment_details')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_bookings');
    }
};

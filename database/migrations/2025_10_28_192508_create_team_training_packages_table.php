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
        Schema::create('team_training_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->double('price')->default(0);
            $table->string('course_privacy')->nullable();
            $table->integer('allocation')->nullable();
            $table->string('expiry_type')->nullable();
            $table->integer('start_date')->nullable();
            $table->integer('expiry_date')->nullable();
            $table->longText('features')->nullable();
            $table->string('thumbnail');
            $table->integer('pricing_type')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_training_packages');
    }
};

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
        Schema::create('bootcamp_live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('bootcamp_modules')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->integer('start_time');
            $table->integer('end_time');
            $table->integer('sort')->nullable();
            $table->string('status')->default(0);
            $table->string('provider')->nullable();
            $table->longText('joining_data')->nullable();
            $table->integer('force_stop')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bootcamp_live_classes');
    }
};

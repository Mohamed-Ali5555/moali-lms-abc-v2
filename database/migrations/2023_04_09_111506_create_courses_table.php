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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('course_type')->nullable();
            $table->string('status');
            $table->string('level')->nullable();
            $table->string('language')->nullable();
            $table->double('price', 10, 2);
            $table->integer('discount_flag')->nullable();
            $table->double('discount_price', 10, 2)->nullable();
            $table->string('thumbnail');
            $table->mediumText('description')->nullable();
            $table->integer('is_paid')->default(1);
            $table->text('instructor_ids')->nullable();
            $table->integer('expiry_period')->nullable();
            $table->integer('enable_drip_content')->nullable();
            $table->longText('drip_content_settings')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

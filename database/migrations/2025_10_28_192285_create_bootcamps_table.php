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
        Schema::create('bootcamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('bootcamp_categories')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->text('short_description')->nullable();
            $table->integer('is_paid')->nullable();
            $table->double('price')->nullable();
            $table->integer('discount_flag')->nullable();
            $table->double('discount_price')->nullable();
            $table->integer('publish_date')->nullable();
            $table->string('thumbnail')->nullable();
            $table->longText('faqs')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('outcomes')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->longText('meta_description')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bootcamps');
    }
};

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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->integer('status')->default(1);
            $table->decimal('price', 10)->default(0);
            $table->double('discount_price')->nullable();
            $table->integer('if_discount')->nullable()->default(0);
            $table->string('title');
            $table->string('thumbnail');
            $table->string('slug')->nullable();
            $table->text('disc')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};

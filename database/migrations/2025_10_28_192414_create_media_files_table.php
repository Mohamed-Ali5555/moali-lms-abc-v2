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
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('post_id');
            $table->integer('story_id');
            $table->integer('album_id');
            $table->integer('product_id');
            $table->integer('page_id');
            $table->integer('group_id');
            $table->integer('chat_id');
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->string('privacy', 200)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};

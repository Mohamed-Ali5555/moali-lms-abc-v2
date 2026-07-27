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
        Schema::create('builder_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('html')->nullable();
            $table->string('identifier')->nullable();
            $table->integer('is_permanent')->nullable();
            $table->integer('status')->default(1);
            $table->integer('edit_home_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('builder_pages');
    }
};

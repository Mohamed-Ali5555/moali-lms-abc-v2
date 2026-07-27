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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role', 100);
            $table->string('email')->unique();
            $table->integer('status')->default(1);
            $table->string('national_id')->unique();
            $table->string('goverment');
            $table->string('parent_phone');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->text('about')->nullable();
            $table->string('photo');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->integer('gender')->default(1);
            $table->decimal('wallet', 10)->default(0);
            $table->string('national_image')->nullable();
            $table->integer('current_device_id')->nullable();
            $table->integer('number_devices')->nullable();
            $table->foreignId('category')->constrained('categories')->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

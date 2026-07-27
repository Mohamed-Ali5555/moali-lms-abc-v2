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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255);
            $table->string('title', 255)->nullable();
            $table->string('discount_type', 255)->nullable();
            $table->decimal('remaining_value', 10, 2)->nullable();
            $table->integer('is_partially_used')->default(0);
            $table->integer('used_by')->nullable();
            $table->text('balance_handling')->nullable();
            $table->decimal('minimum_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable();
            $table->enum('type',['recharge','discount','payment'])->default('discount');
            $table->decimal('value', 10, 2);
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');
            $table->integer('limit')->default(1);
            $table->dateTime('start_date');
            $table->text('user_id');
            $table->integer('status')->default(1);
            $table->float('discount', 10, 2)->nullable();
            $table->dateTime('expiry')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

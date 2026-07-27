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
        Schema::create('bootcamp_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->foreignId('bootcamp_id')->constrained('bootcamps')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->double('price');
            $table->double('tax')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payment_details')->nullable();
            $table->integer('status')->default(1);
            $table->double('admin_revenue')->nullable();
            $table->double('instructor_revenue')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bootcamp_purchases');
    }
};

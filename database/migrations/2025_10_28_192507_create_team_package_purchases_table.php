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
        Schema::create('team_package_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('package_id')->nullable();
            $table->double('price')->default(0);
            $table->double('admin_revenue')->default(0);
            $table->double('instructor_revenue')->default(0);
            $table->double('tax')->default(0);
            $table->string('payment_method')->nullable();
            $table->text('payment_details')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_package_purchases');
    }
};

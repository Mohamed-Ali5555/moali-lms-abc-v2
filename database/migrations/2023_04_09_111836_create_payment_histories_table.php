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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('payment_type', 255);
            $table->float('amount', 10, 2)->default(0);
            $table->float('admin_revenue', 10, 2)->default(0);
            $table->float('instructor_revenue', 10, 2)->default(0);
            $table->float('tax', 10, 2)->default(0);
            $table->string('coupon', 255)->nullable();
            $table->string('invoice', 255)->nullable();
            $table->integer('instructor_payment_status')->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->string('session_id', 255)->nullable();
            $table->string('uuid', 255)->nullable();
            $table->integer('paid')->default(0)->nullable();
            $table->enum('status', ['paid', 'un-paid', 'failed']);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};

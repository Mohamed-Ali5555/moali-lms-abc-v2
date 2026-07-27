<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('offline_payments', function (Blueprint $table) {
            $table->id();
            $table->string('items', 255)->nullable();
            $table->float('tax', 10, 2)->default(0);
            $table->float('total_amount', 10, 2)->default(0);
            $table->string('bank_no', 255)->nullable();
            $table->string('doc', 255)->nullable();
            $table->integer('status')->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('item_type')->nullable();
            $table->string('coupon')->nullable();
            $table->string('phone_no')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('offline_payments');
    }
};

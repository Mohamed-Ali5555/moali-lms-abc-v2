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
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_category_id')->nullable()->constrained('wallet_log_categories')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->string('payment_id', 200)->nullable();
            $table->string('uuid');
            $table->enum('status', ['0', '1'])->default('0');
            $table->enum('type', ['paymob', 'Wallet', 'fawrypay', 'by_hand', 'gift', 'paypal', 'decreased']);
            $table->decimal('balance', 10)->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_logs');
    }
};

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
        Schema::create('wallet_log_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->enum('status', ['0', '1'])->default(1);
            $table->enum('type', ['fawry', 'eodafone_cash', 'etisalat_cash', 'by_hand', 'gift', 'paypal']);
            $table->text('note')->nullable();
            $table->decimal('balance', 10)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_log_categories');
    }
};

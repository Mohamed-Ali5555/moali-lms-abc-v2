<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payment_history;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoce_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_history_id')->constrained('payment_histories')->onDelete('cascade');
            $table->morphs('productable');
            $table->float('amount', 10, 2);
            $table->integer('qty')->default(1);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoce_items');
    }
};

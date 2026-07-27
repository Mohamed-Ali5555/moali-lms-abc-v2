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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');
            $table->string('currency');
            $table->string('title');
            $table->string('model_name');
            $table->text('description')->nullable();
            $table->text('keys')->nullable();
            $table->integer('status')->default(0);
            $table->integer('test_mode')->nullable();
            $table->integer('is_addon')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};

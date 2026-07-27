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
        Schema::create('team_package_members', function (Blueprint $table) {
            $table->id();
            $table->integer('leader_id')->nullable();
            $table->integer('team_package_id')->nullable();
            $table->integer('member_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_package_members');
    }
};

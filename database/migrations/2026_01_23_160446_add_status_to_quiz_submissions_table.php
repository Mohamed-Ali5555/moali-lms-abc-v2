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
        Schema::table('quiz_submissions', function (Blueprint $table) {
            // Status: 'in_progress' = student started but not submitted, 'completed' = submitted
            $table->string('status')->default('completed')->after('submits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_submissions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

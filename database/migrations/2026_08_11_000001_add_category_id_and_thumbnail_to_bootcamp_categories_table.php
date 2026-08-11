<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bootcamp_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('bootcamp_categories', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('slug')
                    ->constrained('categories')
                    ->onDelete('cascade');
            }

            if (! Schema::hasColumn('bootcamp_categories', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('category_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bootcamp_categories', function (Blueprint $table) {
            if (Schema::hasColumn('bootcamp_categories', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }

            if (Schema::hasColumn('bootcamp_categories', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });
    }
};

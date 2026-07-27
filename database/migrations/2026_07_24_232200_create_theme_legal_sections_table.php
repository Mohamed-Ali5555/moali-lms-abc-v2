<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_legal_sections', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['terms', 'privacy'])->index();
            $table->string('title');
            $table->longText('body');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        $oldTerms = DB::table('theme_settings')
            ->where('type', 'terms_condition')
            ->value('description');

        if (! empty($oldTerms)) {
            DB::table('theme_legal_sections')->insert([
                'type' => 'terms',
                'title' => 'الشروط والأحكام',
                'body' => $oldTerms,
                'sort_order' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('theme_legal_sections')->insert([
                [
                    'type' => 'terms',
                    'title' => 'قبول الشروط',
                    'body' => 'باستخدامك للمنصة فإنك توافق على الالتزام بهذه الشروط والأحكام.',
                    'sort_order' => 1,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'privacy',
                    'title' => 'جمع البيانات',
                    'body' => 'نقوم بجمع البيانات اللازمة لتقديم الخدمة وتحسين تجربة المستخدم فقط.',
                    'sort_order' => 1,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_legal_sections');
    }
};

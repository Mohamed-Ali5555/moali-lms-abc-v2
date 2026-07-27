<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique();
            $table->string('title');
            $table->text('body');
            $table->boolean('send_to_student')->default(true);
            $table->boolean('send_to_parent')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('placeholders_hint')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('recipient_type', 20)->nullable(); // student|parent|test
            $table->string('phone', 30);
            $table->text('message');
            $table->string('status', 20)->default('pending'); // pending|success|failed
            $table->text('response')->nullable();
            $table->timestamps();
        });

        $settings = [
            'wapilot_enabled' => '0',
            'wapilot_api_url' => 'https://api.wapilot.net',
            'wapilot_api_key' => '',
            'wapilot_sender' => '',
            'wapilot_default_country_code' => '20',
            'wapilot_send_path' => '/api/send',
        ];

        foreach ($settings as $type => $description) {
            if (!DB::table('settings')->where('type', $type)->exists()) {
                DB::table('settings')->insert([
                    'type' => $type,
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $templates = [
            [
                'event_key' => 'lesson_published',
                'title' => 'محاضرة / درس جديد',
                'body' => "مرحباً [student_name]\nتم إضافة درس جديد في كورس [course_title]:\n[lesson_title]\nالوقت: [start_time]",
                'send_to_student' => true,
                'send_to_parent' => true,
                'is_active' => true,
                'placeholders_hint' => '[student_name] [course_title] [lesson_title] [start_time] [end_time] [link] [system_name]',
            ],
            [
                'event_key' => 'quiz_activated',
                'title' => 'تفعيل / نشر كويز أو واجب',
                'body' => "مرحباً [student_name]\nتم نشر اختبار/واجب جديد في كورس [course_title]:\n[quiz_title]\nمن [start_time] إلى [end_time]\nالدرجة الكلية: [total_mark]",
                'send_to_student' => true,
                'send_to_parent' => true,
                'is_active' => true,
                'placeholders_hint' => '[student_name] [course_title] [quiz_title] [start_time] [end_time] [total_mark] [pass_mark] [system_name]',
            ],
            [
                'event_key' => 'quiz_result',
                'title' => 'نتيجة اختبار / واجب',
                'body' => "نتيجة [student_name] في [quiz_title] (كورس [course_title]):\nالدرجة: [score] من [total]\nالحالة: [pass_status]",
                'send_to_student' => true,
                'send_to_parent' => true,
                'is_active' => true,
                'placeholders_hint' => '[student_name] [course_title] [quiz_title] [score] [total] [pass_status] [system_name]',
            ],
            [
                'event_key' => 'enrollment_confirmed',
                'title' => 'تأكيد التسجيل في كورس',
                'body' => "مرحباً [student_name]\nتم تسجيلك بنجاح في كورس [course_title].\nالمبلغ: [amount]\n[system_name]",
                'send_to_student' => true,
                'send_to_parent' => true,
                'is_active' => true,
                'placeholders_hint' => '[student_name] [course_title] [amount] [system_name]',
            ],
        ];

        foreach ($templates as $template) {
            if (!DB::table('whatsapp_templates')->where('event_key', $template['event_key'])->exists()) {
                DB::table('whatsapp_templates')->insert(array_merge($template, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('whatsapp_templates');

        DB::table('settings')->whereIn('type', [
            'wapilot_enabled',
            'wapilot_api_url',
            'wapilot_api_key',
            'wapilot_sender',
            'wapilot_default_country_code',
            'wapilot_send_path',
        ])->delete();
    }
};

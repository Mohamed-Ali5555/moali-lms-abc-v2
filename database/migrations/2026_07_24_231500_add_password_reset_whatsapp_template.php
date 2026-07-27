<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('whatsapp_templates')) {
            return;
        }

        $exists = DB::table('whatsapp_templates')
            ->where('event_key', 'password_reset')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('whatsapp_templates')->insert([
            'event_key' => 'password_reset',
            'title' => 'استعادة كلمة المرور',
            'body' => "مرحباً [student_name]\nطلبت إعادة تعيين كلمة المرور على [system_name].\nاضغط على الرابط التالي لإدخال كلمة مرور جديدة:\n[reset_link]\nإذا لم تطلب ذلك تجاهل الرسالة.",
            'send_to_student' => 1,
            'send_to_parent' => 0,
            'is_active' => 1,
            'placeholders_hint' => '[student_name] [phone] [reset_link] [system_name]',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('whatsapp_templates')) {
            return;
        }

        DB::table('whatsapp_templates')->where('event_key', 'password_reset')->delete();
    }
};

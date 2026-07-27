<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Setting;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use App\Services\WaPilot\WaPilotClient;
use App\Services\WaPilot\WhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class WaPilotController extends Controller
{
    public function settings()
    {
        $page_data['templates'] = WhatsappTemplate::orderBy('id')->get();
        $page_data['logs'] = WhatsappLog::orderByDesc('id')->limit(50)->get();
        $page_data['categories'] = Category::where('parent_id', 0)->orderBy('sort')->orderBy('title')->get(['id', 'title']);
        $page_data['courses'] = Course::orderBy('title')->get(['id', 'title', 'category_id']);

        return view('admin.setting.wapilot_setting', $page_data);
    }

    public function settingsUpdate(Request $request)
    {
        $keys = [
            'wapilot_enabled',
            'wapilot_api_url',
            'wapilot_api_key',
            'wapilot_sender',
            'wapilot_default_country_code',
            'wapilot_send_path',
        ];

        foreach ($keys as $key) {
            $value = $request->input($key, $key === 'wapilot_enabled' ? '0' : '');
            if ($key === 'wapilot_enabled') {
                $value = $request->boolean('wapilot_enabled') ? '1' : '0';
            }

            if (Setting::where('type', $key)->exists()) {
                Setting::where('type', $key)->update(['description' => $value]);
            } else {
                Setting::insert([
                    'type' => $key,
                    'description' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        clear_lms_cache('settings');
        Session::flash('success', get_phrase('WaPilot settings updated successfully'));

        return redirect()->back();
    }

    public function templateUpdate(Request $request, $id)
    {
        $template = WhatsappTemplate::findOrFail($id);

        $template->update([
            'title' => $request->input('title', $template->title),
            'body' => $request->input('body', $template->body),
            'send_to_student' => $request->boolean('send_to_student'),
            'send_to_parent' => $request->boolean('send_to_parent'),
            'is_active' => $request->boolean('is_active'),
        ]);

        Session::flash('success', get_phrase('WhatsApp template updated successfully'));

        return redirect()->back()->with('tab', 'templates');
    }

    public function testSend(Request $request, WhatsAppNotifier $notifier)
    {
        $request->validate([
            'test_phone' => 'required|string|min:8|max:20',
            'test_message' => 'nullable|string|max:1000',
        ]);

        $message = $request->input('test_message')
            ?: ('اختبار تكامل WaPilot من ' . (get_settings('system_name') ?: config('app.name')));

        $result = $notifier->sendTest($request->input('test_phone'), $message);

        WhatsappLog::create([
            'event_key' => 'test_send',
            'user_id' => auth()->id(),
            'recipient_type' => 'test',
            'phone' => $request->input('test_phone'),
            'message' => $message,
            'status' => ($result['success'] ?? false) ? 'success' : 'failed',
            'response' => is_string($result['response'] ?? null)
                ? substr($result['response'], 0, 2000)
                : json_encode($result['response'] ?? null),
        ]);

        if ($result['success'] ?? false) {
            Session::flash('success', get_phrase('Test message sent successfully'));
        } else {
            Session::flash('error', get_phrase('Test message failed') . ': ' . ($result['response'] ?? ''));
        }

        return redirect()->back()->with('tab', 'test');
    }

    public function broadcastPreview(Request $request, WhatsAppNotifier $notifier)
    {
        $validated = $request->validate([
            'audience_type' => ['required', Rule::in(['category', 'course'])],
            'audience_id' => 'required|integer|min:1',
        ]);

        if ($validated['audience_type'] === 'category') {
            if (!Category::where('id', $validated['audience_id'])->where('parent_id', 0)->exists()) {
                return response()->json(['ok' => false, 'message' => get_phrase('التصنيف غير موجود')], 422);
            }
        } else {
            if (!Course::where('id', $validated['audience_id'])->exists()) {
                return response()->json(['ok' => false, 'message' => get_phrase('الكورس غير موجود')], 422);
            }
        }

        $counts = $notifier->countAudience($validated['audience_type'], (int) $validated['audience_id']);

        return response()->json([
            'ok' => true,
            'counts' => $counts,
        ]);
    }

    public function broadcastSend(Request $request, WhatsAppNotifier $notifier, WaPilotClient $client)
    {
        $validated = $request->validate([
            'audience_type' => ['required', Rule::in(['category', 'course'])],
            'audience_id' => 'required|integer|min:1',
            'title' => 'nullable|string|max:180',
            'body' => 'required|string|max:2000',
            'send_to_student' => 'nullable|boolean',
            'send_to_parent' => 'nullable|boolean',
            'confirm' => 'accepted',
        ]);

        if (!$client->isEnabled()) {
            return redirect()->back()
                ->with('tab', 'broadcast')
                ->with('error', get_phrase('فعّل تكامل WaPilot أولاً من إعدادات API'));
        }

        $sendToStudent = $request->boolean('send_to_student');
        $sendToParent = $request->boolean('send_to_parent');

        if (!$sendToStudent && !$sendToParent) {
            return redirect()->back()
                ->with('tab', 'broadcast')
                ->with('error', get_phrase('اختر مستلماً واحداً على الأقل (طالب أو ولي أمر)'));
        }

        if ($validated['audience_type'] === 'category') {
            if (!Category::where('id', $validated['audience_id'])->where('parent_id', 0)->exists()) {
                return redirect()->back()
                    ->with('tab', 'broadcast')
                    ->with('error', get_phrase('التصنيف غير موجود'));
            }
        } else {
            if (!Course::where('id', $validated['audience_id'])->exists()) {
                return redirect()->back()
                    ->with('tab', 'broadcast')
                    ->with('error', get_phrase('الكورس غير موجود'));
            }
        }

        $result = $notifier->broadcastCustom(
            $validated['audience_type'],
            (int) $validated['audience_id'],
            (string) ($validated['title'] ?? ''),
            (string) $validated['body'],
            $sendToStudent,
            $sendToParent
        );

        if (($result['error'] ?? null) === 'disabled') {
            return redirect()->back()
                ->with('tab', 'broadcast')
                ->with('error', get_phrase('فعّل تكامل WaPilot أولاً من إعدادات API'));
        }

        $message = get_phrase('تم جدولة الإرسال') . ': '
            . ($result['queued'] ?? 0) . ' ' . get_phrase('رسالة')
            . ' · ' . get_phrase('الجمهور') . ': ' . ($result['audience'] ?? 0)
            . ' ' . get_phrase('طالب');

        if (($result['skipped'] ?? 0) > 0) {
            $message .= ' · ' . get_phrase('تم التخطي') . ': ' . $result['skipped'];
        }

        Session::flash('success', $message);

        return redirect()->back()->with('tab', 'broadcast');
    }
}

<?php

namespace Modules\Theme\App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsappTemplate;
use App\Services\WaPilot\WaPilotClient;
use App\Services\WaPilot\WhatsAppNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('theme::auth.forgot-password');
    }

    /**
     * Handle an incoming password reset request via phone + WhatsApp.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, WhatsAppNotifier $notifier, WaPilotClient $client): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب.',
        ]);

        $this->ensureIsNotRateLimited($request);

        if (!$client->isEnabled()) {
            return back()
                ->withInput($request->only('phone'))
                ->withErrors(['phone' => 'خدمة واتساب غير مفعّلة حالياً. تواصل مع الإدارة.']);
        }

        $template = WhatsappTemplate::where('event_key', 'password_reset')->first();
        if (!$template || !$template->is_active) {
            return back()
                ->withInput($request->only('phone'))
                ->withErrors(['phone' => 'قالب استعادة كلمة المرور غير مفعّل في إعدادات واتساب. تواصل مع الإدارة.']);
        }

        $user = $this->findUserByPhone((string) $request->input('phone'));

        // Always show a generic success message when the phone is unknown
        // so we do not leak whether an account exists.
        if (!$user) {
            RateLimiter::hit($this->throttleKey($request));

            return back()->with('status', 'إذا كان الرقم مسجّلاً لدينا فستصلك رسالة واتساب برابط إعادة تعيين كلمة المرور.');
        }

        if (!$client->normalizePhone($user->phone)) {
            return back()
                ->withInput($request->only('phone'))
                ->withErrors(['phone' => 'رقم الهاتف المسجّل غير صالح لإرسال واتساب. تواصل مع الإدارة.']);
        }

        $token = Password::broker()->createToken($user);
        $resetLink = url(route('theme.password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        $notifier->notifyPasswordReset($user, $resetLink);

        RateLimiter::clear($this->throttleKey($request));

        return back()->with('status', 'تم إرسال رابط إعادة تعيين كلمة المرور عبر واتساب. تحقق من رسائلك.');
    }

    protected function findUserByPhone(string $phone): ?User
    {
        $raw = trim($phone);
        $digits = preg_replace('/\D+/', '', $raw) ?: '';

        $candidates = array_values(array_unique(array_filter([
            $raw,
            $digits,
            ltrim($digits, '0'),
            $digits !== '' ? ('0' . ltrim($digits, '0')) : null,
            str_starts_with($digits, '20') ? ('0' . substr($digits, 2)) : null,
            str_starts_with($digits, '20') ? substr($digits, 2) : null,
            str_starts_with($digits, '0') ? ('20' . substr($digits, 1)) : null,
        ])));

        if ($candidates === []) {
            return null;
        }

        return User::query()
            ->whereIn('phone', $candidates)
            ->first();
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'phone' => 'محاولات كثيرة. حاول مرة أخرى بعد ' . $seconds . ' ثانية.',
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('phone')) . '|' . $request->ip());
    }
}

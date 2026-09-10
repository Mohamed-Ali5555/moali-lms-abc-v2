<?php

namespace Modules\Theme\App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
     * Handle an incoming password reset request via email.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'يرجى إدخال بريد إلكتروني صالح.',
        ]);

        $this->ensureIsNotRateLimited($request);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        $genericSuccess = 'إذا كان البريد مسجّلاً لدينا فستصلك رسالة تحتوي على رابط إعادة تعيين كلمة المرور. تحقق من صندوق الوارد ومجلد الرسائل غير المرغوب فيها (Spam).';

        if ($status === Password::RESET_LINK_SENT || $status === Password::INVALID_USER) {
            RateLimiter::hit($this->throttleKey($request));

            return back()->with('status', $genericSuccess);
        }

        RateLimiter::hit($this->throttleKey($request));

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $this->translateStatus($status)]);
    }

    protected function translateStatus(string $status): string
    {
        return match ($status) {
            Password::RESET_THROTTLED => 'تم إرسال رابط مؤخراً. يرجى الانتظار قبل المحاولة مرة أخرى.',
            default => 'تعذّر إرسال رابط إعادة التعيين. حاول مرة أخرى لاحقاً.',
        };
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => 'محاولات كثيرة. حاول مرة أخرى بعد ' . $seconds . ' ثانية.',
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')) . '|' . $request->ip());
    }
}

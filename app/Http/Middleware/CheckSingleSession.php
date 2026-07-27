<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckSingleSession
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            $currentSessionId = $user->current_session_id;

            // إذا كانت الجلسة الحالية لا تطابق الجلسة المسجلة
            if ($currentSessionId !== $request->session()->getId()) {
                Auth::logout();
                $request->session()->invalidate();
                return response()->json([
                    'message' => 'أنت مسجل الدخول بالفعل من جهاز آخر.',
                ], 400);
                // return redirect()->route('login')
                //     ->withErrors(['email' => 'تم تسجيل الدخول من جهاز آخر.']);
            }
        }

        return $next($request);
    }
}
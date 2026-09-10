@extends('theme::layouts.master')

@push('title', get_phrase('نسيت كلمة المرور'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/login-modern.css') }}">
@endpush

@section('content')
@php
    $siteName = trim((get_theme_settings('jop_title') ?: '') . ' ' . (get_theme_settings('name') ?: ''));
    $logo = get_theme_settings('logo');
@endphp

<div class="lg-page main_content">
    <div class="container">
        <div class="lg-shell">
            <aside class="lg-brand">
                <div>
                    @if ($logo)
                        <img class="lg-brand__logo" src="{{ asset($logo) }}" alt="{{ $siteName }}">
                    @endif

                    <span class="lg-brand__badge">
                        <i class="fa-solid fa-envelope"></i>
                        {{ get_phrase('استعادة عبر البريد الإلكتروني') }}
                    </span>

                    <h1 class="lg-brand__title">
                        {{ get_phrase('نسيت كلمة المرور؟') }}
                    </h1>
                    <p class="lg-brand__desc">
                        {{ get_phrase('لا تقلق — يمكنك استعادة حسابك بخطوات بسيطة عبر البريد الإلكتروني المسجّل.') }}
                    </p>

                    <ol class="lg-brand__points" style="list-style: none; counter-reset: step; padding: 0;">
                        <li style="counter-increment: step; display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                            <span style="min-width: 26px; height: 26px; border-radius: 50%; background: rgba(255,255,255,.2); display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold;">1</span>
                            <span>{{ get_phrase('أدخل بريدك الإلكتروني المسجّل في الحساب') }}</span>
                        </li>
                        <li style="counter-increment: step; display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                            <span style="min-width: 26px; height: 26px; border-radius: 50%; background: rgba(255,255,255,.2); display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold;">2</span>
                            <span>{{ get_phrase('افتح بريدك واضغط على رابط إعادة التعيين') }}</span>
                        </li>
                        <li style="counter-increment: step; display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                            <span style="min-width: 26px; height: 26px; border-radius: 50%; background: rgba(255,255,255,.2); display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold;">3</span>
                            <span>{{ get_phrase('اختر كلمة مرور جديدة وسجّل الدخول') }}</span>
                        </li>
                    </ol>
                </div>

                <div class="lg-brand__art">
                    <img src="{{ asset('modules/theme/images/login.svg') }}" alt="{{ get_phrase('نسيت كلمة المرور') }}">
                </div>
            </aside>

            <div class="lg-form-wrap">
                <p class="lg-form__eyebrow">{{ get_phrase('PASSWORD RESET') }}</p>
                <h2 class="lg-form__title">{{ get_phrase('استعادة الحساب') }}</h2>
                <p class="lg-form__sub">
                    {{ get_phrase('اكتب بريدك الإلكتروني وسنرسل لك رابطاً آمناً لتعيين كلمة مرور جديدة.') }}
                </p>

                @if (session('status'))
                    <div class="lg-alert lg-alert--success" role="status">
                        <strong>{{ get_phrase('تم إرسال الطلب') }}</strong>
                        <p style="margin: 8px 0 0;">{{ session('status') }}</p>
                        <ul style="margin: 12px 0 0; padding-right: 18px; font-size: 14px; line-height: 1.7;">
                            <li>{{ get_phrase('تحقق من صندوق الوارد') }}</li>
                            <li>{{ get_phrase('راجع مجلد الرسائل غير المرغوب فيها (Spam / Junk)') }}</li>
                            <li>{{ get_phrase('الرابط صالح لمدة 60 دقيقة فقط') }}</li>
                        </ul>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="lg-alert" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('theme.password.email') }}" class="global-form login-form" method="POST">
                    @csrf

                    <div class="lg-field">
                        <label for="email">{{ get_phrase('البريد الإلكتروني') }}</label>
                        <div class="lg-input">
                            <i class="fa-solid fa-envelope lg-input__icon"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="example@email.com"
                                autocomplete="email"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="lg-submit eBtn gradient">
                        <i class="fa-solid fa-paper-plane"></i>
                        {{ get_phrase('إرسال رابط إعادة التعيين') }}
                    </button>
                </form>

                <div class="lg-footer login-link">
                    <span>{{ get_phrase('تذكرت كلمة المرور؟') }}</span>
                    <a href="{{ route('theme.login') }}">{{ get_phrase('العودة لتسجيل الدخول') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

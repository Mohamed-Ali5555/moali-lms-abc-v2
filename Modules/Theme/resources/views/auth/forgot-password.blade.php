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
                        <i class="fa-brands fa-whatsapp"></i>
                        {{ get_phrase('استعادة عبر واتساب') }}
                    </span>

                    <h1 class="lg-brand__title">
                        {{ get_phrase('نسيت كلمة المرور؟') }}
                    </h1>
                    <p class="lg-brand__desc">
                        {{ get_phrase('أدخل رقم هاتفك المسجّل وسنرسل لك رابط إعادة التعيين عبر رسالة واتساب.') }}
                    </p>

                    <ul class="lg-brand__points">
                        <li>
                            <i class="fa-solid fa-mobile-screen"></i>
                            {{ get_phrase('استخدم نفس رقم تسجيل الدخول') }}
                        </li>
                        <li>
                            <i class="fa-brands fa-whatsapp"></i>
                            {{ get_phrase('الرابط يصل فوراً على واتساب') }}
                        </li>
                        <li>
                            <i class="fa-solid fa-shield-halved"></i>
                            {{ get_phrase('الرابط صالح لفترة محدودة فقط') }}
                        </li>
                    </ul>
                </div>

                <div class="lg-brand__art">
                    <img src="{{ asset('modules/theme/images/login.svg') }}" alt="{{ get_phrase('نسيت كلمة المرور') }}">
                </div>
            </aside>

            <div class="lg-form-wrap">
                <p class="lg-form__eyebrow">{{ get_phrase('PASSWORD RESET') }}</p>
                <h2 class="lg-form__title">{{ get_phrase('استعادة الحساب') }}</h2>
                <p class="lg-form__sub">
                    {{ get_phrase('اكتب رقم الهاتف المسجّل في حسابك وسنرسل لك رسالة واتساب برابط تعيين كلمة مرور جديدة.') }}
                </p>

                @if (session('status'))
                    <div class="lg-alert lg-alert--success" role="status">
                        {{ session('status') }}
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
                        <label for="phone">{{ get_phrase('رقم الهاتف') }}</label>
                        <div class="lg-input">
                            <i class="fa-solid fa-phone lg-input__icon"></i>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="01XXXXXXXXX"
                                inputmode="numeric"
                                autocomplete="tel"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="lg-submit eBtn gradient">
                        <i class="fa-brands fa-whatsapp"></i>
                        {{ get_phrase('إرسال رابط واتساب') }}
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

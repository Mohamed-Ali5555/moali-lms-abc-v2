@extends('theme::layouts.master')

@push('title', get_phrase('تسجيل الدخول'))
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
                        <i class="fa-solid fa-shield-halved"></i>
                        {{ get_phrase('دخول آمن للمنصة') }}
                    </span>

                    <h1 class="lg-brand__title">
                        {{ get_phrase('مرحباً بعودتك') }}
                    </h1>
                    <p class="lg-brand__desc">
                        {{ get_phrase('سجّل دخولك للوصول إلى كورساتك وكتبك ومتابعة تقدّمك التعليمي من مكان واحد.') }}
                    </p>

                    <ul class="lg-brand__points">
                        <li>
                            <i class="fa-solid fa-play"></i>
                            {{ get_phrase('محتوى تعليمي منظّم') }}
                        </li>
                        <li>
                            <i class="fa-solid fa-wallet"></i>
                            {{ get_phrase('محفظة وفواتير بسهولة') }}
                        </li>
                        <li>
                            <i class="fa-solid fa-mobile-screen"></i>
                            {{ get_phrase('تجربة سلسة على كل الأجهزة') }}
                        </li>
                    </ul>
                </div>

                <div class="lg-brand__art">
                    <img src="{{ asset('modules/theme/images/login.svg') }}" alt="{{ get_phrase('تسجيل الدخول') }}">
                </div>
            </aside>

            <div class="lg-form-wrap">
                <p class="lg-form__eyebrow">{{ get_phrase('ACCOUNT LOGIN') }}</p>
                <h2 class="lg-form__title">{{ get_phrase('تسجيل الدخول') }}</h2>
                <p class="lg-form__sub">
                    {{ get_phrase('ادخل البريد الالكتروني وكلمة المرور المسجّل بهم مسبقاً للوصول إلى حسابك.') }}
                </p>

                @if ($errors->any())
                    <div class="lg-alert" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('theme.login') }}" class="global-form login-form" id="login-form" method="POST">
                    @csrf
                    <input type="hidden" name="device" id="device" value="">

                    <div class="lg-field">
                    <label for="email">{{ get_phrase('البريد الالكتروني') }}</label>                        <div class="lg-input">
                            <i class="fa-solid fa-phone lg-input__icon"></i>
                            <input
                                type="text"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="acb@mail"
                          
                                required
                            >
                        </div>
                    </div>

                    <div class="lg-field">
                        <label for="password">{{ get_phrase('كلمة المرور') }}</label>
                        <div class="lg-input">
                            <i class="fa-solid fa-lock lg-input__icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="lg-input__toggle" id="showpassword" aria-label="{{ get_phrase('إظهار كلمة المرور') }}">
                                <i class="fa-regular fa-eye" id="showpasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="lg-row">
                        <label class="lg-check" for="remember">
                            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember" checked>
                            <span>{{ get_phrase('تذكرني') }}</span>
                        </label>
                        <!-- <a class="lg-forgot" href="{{ route('theme.password.request') }}">
                            {{ get_phrase('نسيت كلمة المرور؟') }}
                        </a> -->
                    </div>

                    @if (get_frontend_settings('recaptcha_status'))
                        <button class="lg-submit eBtn gradient g-recaptcha"
                            data-sitekey="{{ get_frontend_settings('recaptcha_sitekey') }}"
                            data-callback="onLoginSubmit"
                            data-action="submit"
                            type="button">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            {{ get_phrase('تسجيل الدخول') }}
                        </button>
                    @else
                        <button type="submit" class="lg-submit eBtn gradient" id="login">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            {{ get_phrase('تسجيل الدخول') }}
                        </button>
                    @endif
                </form>

                <div class="lg-footer login-link">
                    <span>{{ get_phrase('ليس لديك حساب؟') }}</span>
                    <a href="{{ route('theme.show_register') }}">{{ get_phrase('أنشئ حسابك الآن') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    "use strict";

    $(document).ready(function () {
        var device = localStorage.getItem("device");
        if (device) {
            $("#device").val(device);
        }

        $("#showpassword").on("click", function (e) {
            e.preventDefault();
            var $input = $("#password");
            var $icon = $("#showpasswordIcon");
            var isPassword = $input.attr("type") === "password";
            $input.attr("type", isPassword ? "text" : "password");
            $icon.toggleClass("fa-eye fa-eye-slash");
        });
    });

    function onLoginSubmit(token) {
        document.getElementById("login-form").submit();
    }
</script>
@endpush

@extends('theme::layouts.master')

@push('title', get_phrase('تعيين كلمة مرور جديدة'))
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
                        <i class="fa-solid fa-key"></i>
                        {{ get_phrase('تعيين كلمة مرور جديدة') }}
                    </span>

                    <h1 class="lg-brand__title">
                        {{ get_phrase('أعد تعيين كلمة المرور') }}
                    </h1>
                    <p class="lg-brand__desc">
                        {{ get_phrase('اختر كلمة مرور قوية وجديدة لحسابك، ثم سجّل الدخول مجدداً.') }}
                    </p>
                </div>

                <div class="lg-brand__art">
                    <img src="{{ asset('modules/theme/images/login.svg') }}" alt="{{ get_phrase('تعيين كلمة مرور جديدة') }}">
                </div>
            </aside>

            <div class="lg-form-wrap">
                <p class="lg-form__eyebrow">{{ get_phrase('NEW PASSWORD') }}</p>
                <h2 class="lg-form__title">{{ get_phrase('كلمة مرور جديدة') }}</h2>
                <p class="lg-form__sub">
                    {{ get_phrase('أدخل كلمة المرور الجديدة مرتين للتأكيد.') }}
                </p>

                @if ($errors->any())
                    <div class="lg-alert" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('theme.password.store') }}" method="post" class="global-form login-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                    <div class="lg-field">
                        <label for="password">{{ get_phrase('كلمة المرور الجديدة') }}</label>
                        <div class="lg-input">
                            <i class="fa-solid fa-lock lg-input__icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="new-password"
                                required
                            >
                        </div>
                    </div>

                    <div class="lg-field">
                        <label for="password_confirmation">{{ get_phrase('تأكيد كلمة المرور') }}</label>
                        <div class="lg-input">
                            <i class="fa-solid fa-lock lg-input__icon"></i>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="••••••••"
                                autocomplete="new-password"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="lg-submit eBtn gradient">
                        <i class="fa-solid fa-check"></i>
                        {{ get_phrase('حفظ كلمة المرور') }}
                    </button>
                </form>

                <div class="lg-footer login-link">
                    <span>{{ get_phrase('أو') }}</span>
                    <a href="{{ route('theme.login') }}">{{ get_phrase('العودة لتسجيل الدخول') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

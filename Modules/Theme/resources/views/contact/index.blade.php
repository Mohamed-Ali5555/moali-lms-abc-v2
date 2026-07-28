@extends('theme::layouts.master')

@push('title', get_phrase('تواصل معنا'))

@push('css')
<link rel="stylesheet" href="{{ asset('modules/theme/css/theme-pages.css') }}">
@endpush

@section('content')

<section class="theme-page-banner" dir="rtl">
    <div class="theme-page-banner__bg"></div>
    <div class="container position-relative">
        <nav class="theme-page-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('theme.home') }}">الرئيسية</a>
            <span>/</span>
            <span>تواصل معنا</span>
        </nav>
        <h1 class="theme-page-banner__title">تواصل معنا</h1>
        <p class="theme-page-banner__subtitle">{{ get_theme_settings('contact_subtitle') ?: 'نحن هنا لمساعدتك دائماً' }}</p>
    </div>
</section>

<section class="contact-page-section" dir="rtl">
    <div class="container">
        @if (get_theme_settings('contact_intro'))
            <p class="contact-page-intro text-center">{{ get_theme_settings('contact_intro') }}</p>
        @endif

        <div class="row g-4 contact-info-row mb-5">
            @if (get_theme_settings('contact_email'))
                <div class="col-md-6 col-lg-3">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>البريد الإلكتروني</h3>
                        <a href="mailto:{{ get_theme_settings('contact_email') }}">{{ get_theme_settings('contact_email') }}</a>
                    </div>
                </div>
            @endif

            @if (get_theme_settings('contact_phone'))
                <div class="col-md-6 col-lg-3">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>الهاتف</h3>
                        <a href="tel:{{ get_theme_settings('contact_phone') }}" dir="ltr">{{ get_theme_settings('contact_phone') }}</a>
                    </div>
                </div>
            @endif

            @if (get_theme_settings('contact_address'))
                <div class="col-md-6 col-lg-3">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>العنوان</h3>
                        <p>{{ get_theme_settings('contact_address') }}</p>
                    </div>
                </div>
            @endif

            @if (get_theme_settings('contact_hours'))
                <div class="col-md-6 col-lg-3">
                    <div class="contact-info-card">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>ساعات العمل</h3>
                        <p>{{ get_theme_settings('contact_hours') }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="contact-mail-wrap">
            <div class="contact-mail">
                <div class="contact-mail__flap"></div>
                <div class="contact-mail__body">
                    <div class="contact-mail__header">
                        <div class="contact-mail__stamp">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div>
                            <h2>أرسل لنا رسالة</h2>
                            <p>املأ النموذج وسنرد عليك في أقرب وقت</p>
                        </div>
                    </div>

                    <form action="{{ route('theme.contact.store') }}" method="post" class="contact-mail-form" id="theme-contact-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">الاسم</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="اسمك الكامل" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="example@email.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="01xxxxxxxxx" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">العنوان <span class="text-muted">(اختياري)</span></label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}"
                                    class="form-control @error('address') is-invalid @enderror"
                                    placeholder="مدينتك / منطقتك">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">رسالتك</label>
                                <textarea name="message" id="message" rows="6"
                                    class="form-control @error('message') is-invalid @enderror"
                                    placeholder="اكتب رسالتك هنا..." required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="contact-mail-form__actions mt-4">
                            @if (get_frontend_settings('recaptcha_status'))
                                <button type="button" class="btn btn-hero-primary g-recaptcha"
                                    data-sitekey="{{ get_frontend_settings('recaptcha_sitekey') }}"
                                    data-callback="onThemeContactSubmit"
                                    data-action="submit">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    إرسال الرسالة
                                </button>
                            @else
                                <button type="submit" class="btn btn-hero-primary">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    إرسال الرسالة
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
@if (get_frontend_settings('recaptcha_status'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
<script>
    "use strict";
    function onThemeContactSubmit(token) {
        document.getElementById("theme-contact-form").submit();
    }
</script>
@endsection

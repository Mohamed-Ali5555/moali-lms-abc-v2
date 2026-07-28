@extends('theme::layouts.master')

@push('title', get_phrase('من نحن'))

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
            <span>من نحن</span>
        </nav>
        <h1 class="theme-page-banner__title">من نحن</h1>
        <p class="theme-page-banner__subtitle">{{ get_theme_settings('about_subtitle') ?: 'تعرف علينا أكثر' }}</p>
    </div>
</section>

<section class="about-page-section" dir="rtl">
    <div class="container">
        <div class="about-page-card">
            <div class="about-page-card__accent"></div>
            <div class="about-page-content">
                @php $about = get_theme_settings('about_us'); @endphp
                @if ($about)
                    {!! $about !!}
                @else
                    <div class="about-page-empty text-center">
                        <i class="fas fa-info-circle mb-3"></i>
                        <p>سيتم إضافة محتوى صفحة التعريف قريباً.</p>
                    </div>
                @endif
            </div>
        </div>

        @if (get_theme_settings('contact_status') !== '0')
            <div class="about-page-cta text-center">
                <p>هل لديك سؤال؟ يسعدنا التواصل معك</p>
                <a href="{{ route('theme.contact.us') }}" class="btn btn-hero-primary">
                    <i class="fas fa-envelope me-2"></i>
                    تواصل معنا
                </a>
            </div>
        @endif
    </div>
</section>
@endsection

@extends('theme::layouts.master')

@push('title', get_phrase('معسكراتي'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/my-bootcamps-modern.css') }}">
@endpush

@section('content')
    @php
        \Carbon\Carbon::setLocale('ar');
        $totalBootcamps = $my_bootcamps->total();
    @endphp

    <section class="my-course-content main_content mb-page" dir="rtl">
        <div class="profile-banner-area"></div>
        <div class="container profile-banner-area-container">
            <div class="row">
                @include('theme::student.left_sidebar')

                <div class="col-lg-9">
                    <div class="mb-header">
                        <div class="mb-header__intro">
                            <div class="mb-header__icon" aria-hidden="true">
                                <i class="fa-solid fa-campground"></i>
                            </div>
                            <div>
                                <h1 class="mb-header__title">{{ get_phrase('معسكراتي') }}</h1>
                                <p class="mb-header__sub">
                                    {{ get_phrase('تابع معسكراتك التدريبية وانضم للحصص المباشرة من مكان واحد.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($totalBootcamps > 0)
                        <div class="mb-stats">
                            <div class="mb-stat">
                                <span class="mb-stat__icon"><i class="fa-solid fa-layer-group"></i></span>
                                <div>
                                    <span class="mb-stat__label">{{ get_phrase('إجمالي المعسكرات') }}</span>
                                    <span class="mb-stat__value">{{ $totalBootcamps }}</span>
                                </div>
                            </div>
                            <div class="mb-stat">
                                <span class="mb-stat__icon"><i class="fa-solid fa-video"></i></span>
                                <div>
                                    <span class="mb-stat__label">{{ get_phrase('في هذه الصفحة') }}</span>
                                    <span class="mb-stat__value">{{ $my_bootcamps->count() }}</span>
                                </div>
                            </div>
                            <div class="mb-stat">
                                <span class="mb-stat__icon"><i class="fa-solid fa-bolt"></i></span>
                                <div>
                                    <span class="mb-stat__label">{{ get_phrase('حصص مباشرة') }}</span>
                                    <span class="mb-stat__value">{{ get_phrase('جاهز') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-list">
                            @foreach ($my_bootcamps as $bootcamp)
                                <a href="{{ route('theme.my.bootcamp.details', $bootcamp->slug) }}" class="mb-card">
                                    <div class="mb-card__thumb">
                                        <img src="{{ get_image($bootcamp->thumbnail ?? '') }}"
                                            alt="{{ $bootcamp->title }}"
                                            loading="lazy">
                                    </div>
                                    <div class="mb-card__body">
                                        <h2 class="mb-card__title">{{ $bootcamp->title }}</h2>
                                        <div class="mb-card__meta">
                                            <span>
                                                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                                {{ date('d M, Y', $bootcamp->publish_date) }}
                                            </span>
                                            <span>
                                                <i class="fa-solid fa-video" aria-hidden="true"></i>
                                                {{ count_bootcamp_classes($bootcamp->id) }}
                                                {{ get_phrase('Live class') }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="mb-card__arrow" aria-hidden="true">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </span>
                                </a>
                            @endforeach
                        </div>

                        @if ($my_bootcamps->hasPages())
                            <div class="mb-pagination">
                                {{ $my_bootcamps->links() }}
                            </div>
                        @endif
                    @else
                        <div class="mb-empty">
                            <div class="mb-empty__icon" aria-hidden="true">
                                <i class="fa-solid fa-campground"></i>
                            </div>
                            <h2>{{ get_phrase('لا توجد معسكرات بعد') }}</h2>
                            <p>{{ get_phrase('عند اشتراكك في معسكر تدريبي سيظهر هنا لتتابع حصصك بسهولة.') }}</p>
                            <a href="{{ route('theme.bootcamps') }}" class="mb-btn mb-btn--primary">
                                <i class="fa-solid fa-compass"></i>
                                {{ get_phrase('استكشف المعسكرات') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

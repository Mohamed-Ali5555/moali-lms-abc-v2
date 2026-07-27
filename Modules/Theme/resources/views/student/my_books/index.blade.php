@extends('theme::layouts.master')

@push('title', get_phrase('كتبي'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/my-books-modern.css') }}">
@endpush

@section('content')
    @php
        $bookItems = collect($books ?? [])->filter(fn ($row) => !empty($row['book']));
        $booksCount = $bookItems->count();
        $purchasesCount = $bookItems->sum(fn ($row) => (int) ($row['count'] ?? 0));
    @endphp

    <section class="myCourses main_content mb-page" dir="rtl">
        <div class="profile-banner-area"></div>
        <div class="container profile-banner-area-container">
            <div class="row">
                @include('theme::student.left_sidebar')

                <div class="col-lg-9">
                    <div class="mb-header">
                        <div class="mb-header__intro">
                            <div class="mb-header__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="mb-header__title">{{ get_phrase('كتبي') }}</h1>
                                <p class="mb-header__sub">
                                    {{ get_phrase('مكتبتك الخاصة — كل الكتب التي قمت بشرائها في مكان واحد.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($booksCount > 0)
                        <div class="mb-stats">
                            <div class="mb-stat">
                                <span class="mb-stat__label">{{ get_phrase('عدد الكتب') }}</span>
                                <span class="mb-stat__value">{{ $booksCount }}</span>
                            </div>
                            <div class="mb-stat mb-stat--accent">
                                <span class="mb-stat__label">{{ get_phrase('إجمالي مرات الشراء') }}</span>
                                <span class="mb-stat__value">{{ $purchasesCount }}</span>
                            </div>
                        </div>

                        <div class="mb-panel">
                            <div class="mb-grid">
                                @foreach ($bookItems as $row)
                                    @php
                                        $book = $row['book'];
                                        $qty = (int) ($row['count'] ?? 1);
                                    @endphp
                                    <article class="mb-card">
                                        <div class="mb-card__cover">
                                            <span class="mb-card__badge">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                {{ get_phrase('تم الشراء') }}
                                            </span>
                                            @if ($qty > 1)
                                                <span class="mb-card__qty" title="{{ get_phrase('عدد النسخ') }}">
                                                    ×{{ $qty }}
                                                </span>
                                            @endif
                                            <img src="{{ get_image($book->thumbnail ?? '') }}"
                                                alt="{{ $book->title }}"
                                                loading="lazy">
                                        </div>

                                        <div class="mb-card__body">
                                            <h2 class="mb-card__title">
                                                <a href="{{ route('theme.book.details', $book->id) }}">
                                                    {{ $book->title }}
                                                </a>
                                            </h2>

                                            <div class="mb-card__meta">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M6 2h12v20H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
                                                        <path d="M6 2v20" />
                                                    </svg>
                                                    {{ get_phrase('نسخ مشتراة') }}: {{ $qty }}
                                                </span>
                                            </div>

                                            <div class="mb-card__actions">
                                                <a href="{{ route('theme.book.details', $book->id) }}"
                                                    class="mb-btn mb-btn--primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                    {{ get_phrase('عرض الكتاب') }}
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mb-empty">
                            <div class="mb-empty__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                </svg>
                            </div>
                            <h2 class="mb-empty__title">{{ get_phrase('لا توجد كتب بعد') }}</h2>
                            <p class="mb-empty__text">
                                {{ get_phrase('عندما تشتري كتابًا سيظهر هنا مباشرة في مكتبتك.') }}
                            </p>
                            <a href="{{ route('theme.home') }}" class="mb-btn mb-btn--primary">
                                {{ get_phrase('تصفح الكتب') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js')@endpush

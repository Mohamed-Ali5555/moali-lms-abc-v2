@extends('theme::layouts.master')

@push('title', get_phrase('المعسكرات'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/category-modern.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/theme/css/bootcamp-modern.css') }}">
@endpush

@section('content')
    @php
        $categories = App\Models\BootcampCategory::get();
        $activeCategorySlug = request()->route()->parameter('category');
        $activeCategory = $categories->firstWhere('slug', $activeCategorySlug);
        $totalBootcamps = $bootcamps->total();
        \Carbon\Carbon::setLocale('ar');
    @endphp

    <section class="cat-page bc-page" dir="rtl">
        <span class="cat-page__glow cat-page__glow--1" aria-hidden="true"></span>
        <span class="cat-page__glow cat-page__glow--2" aria-hidden="true"></span>

        <div class="container">
            <header class="cat-masthead">
                <div class="cat-masthead__media cat-masthead__media--fallback" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                        <path d="M8 7h8" />
                        <path d="M8 11h8" />
                        <path d="M8 15h5" />
                    </svg>
                </div>

                <div class="cat-masthead__copy">
                    <p class="cat-masthead__eyebrow">{{ get_phrase('المعسكرات التدريبية') }}</p>
                    <h1 class="cat-masthead__title">
                        {{ $activeCategory->title ?? get_phrase('جميع المعسكرات') }}
                    </h1>
                    <p class="cat-masthead__desc">
                        {{ get_phrase('اكتشف معسكراتنا التدريبية المباشرة واختر ما يناسب أهدافك المهنية.') }}
                    </p>
                </div>
            </header>

            <div class="cat-stats">
                <div class="cat-stat">
                    <span class="cat-stat__label">{{ get_phrase('الفئات') }}</span>
                    <span class="cat-stat__value">{{ $categories->count() }}</span>
                </div>
                <div class="cat-stat cat-stat--accent">
                    <span class="cat-stat__label">{{ get_phrase('المعسكرات') }}</span>
                    <span class="cat-stat__value">{{ $totalBootcamps }}</span>
                </div>
                <div class="cat-stat">
                    <span class="cat-stat__label">{{ get_phrase('في هذه الصفحة') }}</span>
                    <span class="cat-stat__value">{{ $bootcamps->count() }}</span>
                </div>
            </div>

            <div class="bc-toolbar">
                <form class="bc-search" action="{{ route('theme.bootcamps', $activeCategorySlug) }}" method="get">
                    <input type="text" name="search" placeholder="{{ get_phrase('ابحث عن معسكر، مهارة، أو مدرب...') }}"
                        value="{{ request('search') }}">
                    <button type="submit" class="bc-search__btn">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        {{ get_phrase('بحث') }}
                    </button>
                </form>
            </div>

            <div class="bc-layout">
                <aside class="bc-sidebar">
                    <h2 class="bc-sidebar__title">
                        <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
                        {{ get_phrase('فئات المعسكرات') }}
                    </h2>
                    <ul class="bc-cat-list">
                        <li>
                            <a href="{{ route('theme.bootcamps') }}"
                                class="bc-cat-link {{ empty($activeCategorySlug) ? 'is-active' : '' }}">
                                <span>{{ get_phrase('الكل') }}</span>
                                <span class="bc-cat-count">{{ App\Models\Bootcamp::where('status', 1)->count() }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('theme.bootcamps', $category->slug) }}"
                                    class="bc-cat-link {{ $category->slug == $activeCategorySlug ? 'is-active' : '' }}">
                                    <span>{{ $category->title }}</span>
                                    <span class="bc-cat-count">{{ count_bootcamps_by_category($category->id) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </aside>

                <div class="bc-main">
                    <div class="bc-panel__head">
                        <h2 class="bc-panel__title">
                            {{ $activeCategory->title ?? get_phrase('جميع المعسكرات') }}
                        </h2>
                        <p class="bc-panel__meta">
                            {{ $totalBootcamps }} {{ get_phrase('معسكر') }}
                        </p>
                    </div>

                    @if ($bootcamps->isNotEmpty())
                        <div class="cat-grid">
                            @foreach ($bootcamps as $bootcamp)
                                @php
                                    $isFree = (int) $bootcamp->is_paid === 0;
                                    $hasDiscount = (int) $bootcamp->discount_flag === 1;
                                    $excerpt = \Illuminate\Support\Str::limit(trim(strip_tags($bootcamp->description ?? '')), 110);
                                    $btnUrl = route('theme.purchase.bootcamp', $bootcamp->id);
                                    $btnText = get_phrase('اشترك الآن');
                                    $btnClass = 'cat-btn cat-btn--primary';

                                    if (auth()->check()) {
                                        $myBootcamp = App\Models\BootcampPurchase::where('user_id', auth()->user()->id)
                                            ->where('bootcamp_id', $bootcamp->id)
                                            ->where('status', 1)
                                            ->first();

                                        if ($myBootcamp) {
                                            $btnText = get_phrase('اشتراكاتي');
                                            $btnUrl = route('theme.my.bootcamp.details', $bootcamp->slug);
                                            $btnClass = 'cat-btn cat-btn--ghost';
                                        }

                                        $pendingPayment = DB::table('offline_payments')
                                            ->where('user_id', auth()->user()->id)
                                            ->where('item_type', 'bootcamp')
                                            ->where('items', $bootcamp->id)
                                            ->where('status', 0)
                                            ->first();

                                        if ($pendingPayment) {
                                            $btnText = get_phrase('قيد المعالجة');
                                            $btnUrl = 'javascript:void(0);';
                                            $btnClass = 'cat-btn cat-btn--ghost';
                                        }
                                    }

                                    if ($isFree && ! auth()->check()) {
                                        $btnText = get_phrase('ابدأ مجانًا');
                                        $btnClass = 'cat-btn cat-btn--success';
                                    } elseif ($isFree) {
                                        $btnText = get_phrase('ابدأ مجانًا');
                                        $btnClass = 'cat-btn cat-btn--success';
                                    }
                                @endphp

                                <article class="cat-card">
                                    <div class="cat-card__media">
                                        @if ($hasDiscount)
                                            <span class="cat-card__badge cat-card__badge--sale">
                                                {{ get_phrase('خصم') }}
                                            </span>
                                        @elseif ($isFree)
                                            <span class="cat-card__badge cat-card__badge--free">
                                                {{ get_phrase('مجاني') }}
                                            </span>
                                        @endif

                                        <a href="{{ route('theme.bootcamp.details', $bootcamp->slug) }}"
                                            aria-label="{{ $bootcamp->title }}">
                                            <img src="{{ get_image($bootcamp->thumbnail ?? '') }}"
                                                alt="{{ $bootcamp->title }}"
                                                loading="lazy">
                                        </a>
                                    </div>

                                    <div class="cat-card__body">
                                        <h3 class="cat-card__title">
                                            <a href="{{ route('theme.bootcamp.details', $bootcamp->slug) }}">
                                                {{ $bootcamp->title }}
                                            </a>
                                        </h3>

                                        @if ($excerpt !== '')
                                            <p class="cat-card__excerpt">{{ $excerpt }}</p>
                                        @endif

                                        <div class="cat-card__meta">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                    <line x1="16" y1="2" x2="16" y2="6" />
                                                    <line x1="8" y1="2" x2="8" y2="6" />
                                                    <line x1="3" y1="10" x2="21" y2="10" />
                                                </svg>
                                                {{ \Carbon\Carbon::createFromTimestamp($bootcamp->publish_date)->isoFormat('D MMM YYYY') }}
                                            </span>
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                                </svg>
                                                {{ count_bootcamp_classes($bootcamp->id) }} {{ get_phrase('حصة') }}
                                            </span>
                                        </div>

                                        <div class="cat-card__footer">
                                            <div class="cat-card__price">
                                                @if ($isFree)
                                                    <span class="cat-card__price-free">{{ get_phrase('مجاني') }}</span>
                                                @elseif ($hasDiscount)
                                                    <span class="cat-card__price-now">
                                                        {{ currency($bootcamp->discount_price) }}
                                                    </span>
                                                    <span class="cat-card__price-old">
                                                        {{ currency($bootcamp->price) }}
                                                    </span>
                                                @else
                                                    <span class="cat-card__price-now">
                                                        {{ currency($bootcamp->price) }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($btnUrl === 'javascript:void(0);')
                                                <span class="{{ $btnClass }}">{{ $btnText }}</span>
                                            @else
                                                <a href="{{ $btnUrl }}" class="{{ $btnClass }}">
                                                    {{ $btnText }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @if ($bootcamps->hasPages())
                            <nav class="bc-pagination" aria-label="{{ get_phrase('Pagination') }}">
                                @if ($bootcamps->onFirstPage())
                                    <span class="bc-page-link is-disabled">
                                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                                        {{ get_phrase('السابق') }}
                                    </span>
                                @else
                                    <a href="{{ $bootcamps->previousPageUrl() }}" class="bc-page-link">
                                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                                        {{ get_phrase('السابق') }}
                                    </a>
                                @endif

                                @foreach (range(1, $bootcamps->lastPage()) as $page)
                                    @if ($page == $bootcamps->currentPage())
                                        <span class="bc-page-link is-active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $bootcamps->url($page) }}" class="bc-page-link">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if ($bootcamps->hasMorePages())
                                    <a href="{{ $bootcamps->nextPageUrl() }}" class="bc-page-link">
                                        {{ get_phrase('التالي') }}
                                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                                    </a>
                                @else
                                    <span class="bc-page-link is-disabled">
                                        {{ get_phrase('التالي') }}
                                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                                    </span>
                                @endif
                            </nav>
                        @endif
                    @else
                        <div class="cat-empty">
                            <div class="cat-empty__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                </svg>
                            </div>
                            <h3 class="cat-empty__title">{{ get_phrase('لا توجد معسكرات') }}</h3>
                            <p class="cat-empty__text">
                                {{ get_phrase('لم يتم العثور على معسكرات تطابق معايير البحث. جرّب مصطلحات أخرى أو تصفّح جميع الفئات.') }}
                            </p>
                            <a href="{{ route('theme.bootcamps') }}" class="cat-btn cat-btn--primary" style="margin-top: 1rem;">
                                {{ get_phrase('عرض جميع المعسكرات') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

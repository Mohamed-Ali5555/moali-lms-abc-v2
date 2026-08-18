@extends('theme::layouts.master')

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/category-modern.css') }}">
@endpush

@section('content')
    @php
        $enrolledSet = collect($enrolledCourseIds ?? []);
        $totalCourses = $courses->count();
        $totalBooks = isset($books) ? $books->count() : 0;
        $showDesc = (int) get_theme_settings('course_status') === 1;
        $categoryThumb = !empty($mainCategory->thumbnail) ? get_image($mainCategory->thumbnail) : null;
        $categoryDesc = trim(strip_tags($mainCategory->description ?? ''));
        \Carbon\Carbon::setLocale('ar');
    @endphp

    <section class="cat-page" dir="rtl">
        <span class="cat-page__glow cat-page__glow--1" aria-hidden="true"></span>
        <span class="cat-page__glow cat-page__glow--2" aria-hidden="true"></span>

        <div class="container">
            <header class="cat-masthead">
                @if ($categoryThumb)
                    <div class="cat-masthead__media">
                        <img src="{{ $categoryThumb }}" alt="{{ $mainCategory->title }}" loading="lazy">
                    </div>
                @else
                    <div class="cat-masthead__media cat-masthead__media--fallback" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                        </svg>
                    </div>
                @endif

                <div class="cat-masthead__copy">
                    <p class="cat-masthead__eyebrow">{{ get_phrase('تصنيف الدورات') }}</p>
                    <h1 class="cat-masthead__title">{{ $mainCategory->title }}</h1>
                    @if ($categoryDesc !== '')
                        <p class="cat-masthead__desc">{{ \Illuminate\Support\Str::limit($categoryDesc, 160) }}</p>
                    @else
                        <p class="cat-masthead__desc">
                            {{ get_phrase('استعرض الدورات المتاحة داخل هذا التصنيف واختر ما يناسب مستواك.') }}
                        </p>
                    @endif
                </div>
            </header>

            <div class="cat-stats">
                <div class="cat-stat">
                    <span class="cat-stat__label">{{ get_phrase('الأقسام') }}</span>
                    <span class="cat-stat__value">{{ $categories->count() }}</span>
                </div>
                <div class="cat-stat cat-stat--accent">
                    <span class="cat-stat__label">{{ get_phrase('الدورات') }}</span>
                    <span class="cat-stat__value">{{ $totalCourses }}</span>
                </div>
                <div class="cat-stat">
                    <span class="cat-stat__label">{{ get_phrase('الكتب') }}</span>
                    <span class="cat-stat__value">{{ $totalBooks }}</span>
                </div>
            </div>

            @if ($categories->isNotEmpty())
                <div class="cat-nav-wrap" data-cat-nav-wrap>
                    <button type="button"
                        class="cat-nav__arrow cat-nav__arrow--prev"
                        data-cat-nav-prev
                        aria-label="{{ get_phrase('السابق') }}"
                        hidden>
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </button>

                    <div class="cat-nav" role="tablist" aria-label="{{ get_phrase('أقسام التصنيف') }}" data-cat-nav>
                        @foreach ($categories as $category)
                            @php
                                $count = ($coursesByCategory[$category->id] ?? collect())->count();
                            @endphp
                            <button type="button"
                                class="cat-nav__btn {{ $loop->first ? 'is-active' : '' }}"
                                role="tab"
                                id="cat-tab-{{ $category->id }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                aria-controls="cat-panel-{{ $category->id }}"
                                data-cat-tab="{{ $category->id }}">
                                {{ $category->title }}
                                <span class="cat-nav__count">{{ $count }}</span>
                            </button>
                        @endforeach
                    </div>

                    <button type="button"
                        class="cat-nav__arrow cat-nav__arrow--next"
                        data-cat-nav-next
                        aria-label="{{ get_phrase('التالي') }}"
                        hidden>
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </button>
                </div>

                @foreach ($categories as $category)
                    @php
                        $systemCourses = $coursesByCategory[$category->id] ?? collect();
                    @endphp
                    <div class="cat-panel {{ $loop->first ? 'is-active' : '' }}"
                        id="cat-panel-{{ $category->id }}"
                        role="tabpanel"
                        aria-labelledby="cat-tab-{{ $category->id }}"
                        @unless ($loop->first) hidden @endunless>

                        <div class="cat-panel__head">
                            <h2 class="cat-panel__title">{{ $category->title }}</h2>
                            <p class="cat-panel__meta">
                                {{ $systemCourses->count() }} {{ get_phrase('دورة') }}
                            </p>
                        </div>

                        @if ($systemCourses->isNotEmpty())
                            <div class="cat-grid">
                                @foreach ($systemCourses as $course)
                                    @php
                                        $isEnrolled = $enrolledSet->contains((int) $course->id);
                                        $isFree = (int) $course->is_paid === 0
                                            && ($course->price == 0 || $course->price < 0 || $course->price === null);
                                        $hasDiscount = (int) $course->discount_flag === 1;
                                        $excerpt = $showDesc
                                            ? \Illuminate\Support\Str::limit(trim(strip_tags($course->description ?? '')), 110)
                                            : '';
                                        $updatedAt = lastUpdate($course->id);
                                        $playerUrl = $playerUrls[$course->id]
                                            ?? route('course.player', ['slug' => $course->slug]);
                                    @endphp

                                    <article class="cat-card">
                                        <div class="cat-card__media">
                                            @if ($isEnrolled)
                                                <span class="cat-card__badge cat-card__badge--enrolled">
                                                    {{ get_phrase('مشترك') }}
                                                </span>
                                            @elseif ($isFree)
                                                <span class="cat-card__badge cat-card__badge--free">
                                                    {{ get_phrase('مجاني') }}
                                                </span>
                                            @elseif ($hasDiscount)
                                                <span class="cat-card__badge cat-card__badge--sale">
                                                    {{ get_phrase('خصم') }}
                                                </span>
                                            @endif

                                            <a href="{{ route('theme.course.details', $course->id) }}"
                                                aria-label="{{ $course->title }}">
                                                <img src="{{ get_image($course->thumbnail ?? '') }}"
                                                    alt="{{ $course->title }}"
                                                    loading="lazy">
                                            </a>
                                        </div>

                                        <div class="cat-card__body">
                                            <h3 class="cat-card__title">
                                                <a href="{{ route('theme.course.details', $course->id) }}">
                                                    {{ $course->title }}
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
                                                    {{ \Carbon\Carbon::parse($course->created_at)->isoFormat('D MMM YYYY') }}
                                                </span>
                                                @if ($updatedAt)
                                                    <span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <polyline points="23 4 23 10 17 10" />
                                                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($updatedAt)->isoFormat('D MMM YYYY') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="cat-card__footer">
                                                <div class="cat-card__price">
                                                    @if ($isEnrolled)
                                                        <span class="cat-card__price-free">{{ get_phrase('تم الاشتراك') }}</span>
                                                    @elseif ($isFree)
                                                        <span class="cat-card__price-free">{{ get_phrase('مجاني') }}</span>
                                                    @elseif ($hasDiscount)
                                                        <span class="cat-card__price-now">
                                                            {{ currency($course->discount_price) }}
                                                        </span>
                                                        <span class="cat-card__price-old">
                                                            {{ currency($course->price) }}
                                                        </span>
                                                    @else
                                                        <span class="cat-card__price-now">
                                                            {{ currency($course->price) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($isEnrolled)
                                                    <a href="{{ $playerUrl }}" class="cat-btn cat-btn--ghost">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <circle cx="12" cy="12" r="10" />
                                                            <polygon points="10 8 16 12 10 16 10 8" />
                                                        </svg>
                                                        {{ get_phrase('عرض الكورس') }}
                                                    </a>
                                                @elseif ($isFree)
                                                    <a href="{{ route('theme.payment.successFree', $course->id) }}"
                                                        class="cat-btn cat-btn--success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <polyline points="20 12 20 22 4 22 4 12" />
                                                            <rect x="2" y="7" width="20" height="5" />
                                                            <line x1="12" y1="22" x2="12" y2="7" />
                                                            <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z" />
                                                            <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z" />
                                                        </svg>
                                                        {{ get_phrase('ابدأ مجانًا') }}
                                                    </a>
                                                @else
                                                    <button type="button"
                                                        class="cat-btn cat-btn--primary add-to-cart"
                                                        element-type="course"
                                                        id-element="{{ $course->id }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <circle cx="9" cy="21" r="1" />
                                                            <circle cx="20" cy="21" r="1" />
                                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                                        </svg>
                                                        {{ get_phrase('اشترك الآن') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
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
                                <h3 class="cat-empty__title">{{ get_phrase('لا توجد دورات حالياً') }}</h3>
                                <p class="cat-empty__text">
                                    {{ get_phrase('لم يتم إضافة دورات داخل هذا القسم بعد.') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="cat-empty">
                    <div class="cat-empty__icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                    </div>
                    <h3 class="cat-empty__title">{{ get_phrase('لا يوجد محتوى') }}</h3>
                    <p class="cat-empty__text">{{ get_phrase('هذا التصنيف لا يحتوي على أقسام أو دورات بعد.') }}</p>
                </div>
            @endif
        </div>
    </section>
  <!-- End Courses Section-->
    @if ($bootcampCategories->isNotEmpty())
        <section class="academic-years-tilt-section" id="bootcamps-section" dir="rtl" style="padding: 0 0 70px;">
            <div class="section-bg-shapes" aria-hidden="true">
                <div class="shape-1"></div>
                <div class="shape-2"></div>
                <div class="shape-3"></div>
                <div class="shape-4"></div>
            </div>
            <div class="container">
                <div class="ay-head">
                    <span class="ay-head__eyebrow">{{ get_phrase('تعلّم مباشر') }}</span>
                    <h2 class="section-title-modern display-5 ay-head__title">{{ get_phrase('المعسكرات') }}</h2>
                    <p class="section-subtitle description-text ay-head__desc">
                        {{ get_phrase('انضم لمعسكراتنا التدريبية المباشرة وطوّر مهاراتك مع أفضل المدربين.') }}
                    </p>
                </div>

                <div class="row g-4 g-xl-5 justify-content-center">
                    @foreach ($bootcampCategories as $bootcamp_cat)
                        <div class="col-lg-4 col-md-6 card-tilt-wrapper">
                            <a href="{{ route('theme.bootcamps', $bootcamp_cat->slug) }}" class="year-portal h-100">
                                <span class="year-portal__shine" aria-hidden="true"></span>
                                <div class="year-portal__media">
                                    <img
                                        src="{{ get_image($bootcamp_cat->thumbnail ?? '') }}"
                                        class="year-portal__img"
                                        alt="{{ $bootcamp_cat->title }}"
                                        loading="lazy"
                                    />
                                    <span class="year-portal__veil" aria-hidden="true"></span>
                                    <span class="year-portal__orbit" aria-hidden="true"></span>
                                    <span class="year-portal__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="year-portal__body">
                                    <span class="year-portal__eyebrow">{{ get_phrase('معسكر تدريبي') }}</span>
                                    <h3 class="year-portal__title">{{ $bootcamp_cat->title }}</h3>
                                    <span class="year-portal__cta">
                                        <span>{{ get_phrase('عرض المعسكرات') }}</span>
                                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    @include('theme::includes.book')
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('[data-cat-tab]');
            const panels = document.querySelectorAll('.cat-panel');
            const wrap = document.querySelector('[data-cat-nav-wrap]');
            const nav = document.querySelector('[data-cat-nav]');
            const prevBtn = document.querySelector('[data-cat-nav-prev]');
            const nextBtn = document.querySelector('[data-cat-nav-next]');

            function updateNavArrows() {
                if (!wrap || !nav || !prevBtn || !nextBtn) return;

                const maxScroll = nav.scrollWidth - nav.clientWidth;
                const canScroll = maxScroll > 4;

                wrap.classList.toggle('is-scrollable', canScroll);

                if (!canScroll) {
                    prevBtn.hidden = true;
                    nextBtn.hidden = true;
                    wrap.classList.remove('has-prev', 'has-next');
                    return;
                }

                // RTL-safe: use abs distance from either end
                const scrollStart = Math.abs(nav.scrollLeft);
                const atStart = scrollStart <= 4;
                const atEnd = scrollStart >= maxScroll - 4;

                prevBtn.hidden = false;
                nextBtn.hidden = false;
                prevBtn.disabled = atStart;
                nextBtn.disabled = atEnd;
                wrap.classList.toggle('has-prev', !atStart);
                wrap.classList.toggle('has-next', !atEnd);
            }

            function scrollNav(direction) {
                if (!nav) return;
                const amount = Math.max(200, Math.round(nav.clientWidth * 0.65));
                const rtl = getComputedStyle(nav).direction === 'rtl';
                // In RTL, "next" (toward more items on the left) needs opposite scrollLeft sign in most browsers
                const delta = direction * amount * (rtl ? -1 : 1);
                nav.scrollBy({ left: delta, behavior: 'smooth' });
            }

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    const id = tab.getAttribute('data-cat-tab');

                    tabs.forEach(function (btn) {
                        const active = btn === tab;
                        btn.classList.toggle('is-active', active);
                        btn.setAttribute('aria-selected', active ? 'true' : 'false');
                    });

                    panels.forEach(function (panel) {
                        const match = panel.id === 'cat-panel-' + id;
                        panel.classList.toggle('is-active', match);
                        if (match) {
                            panel.removeAttribute('hidden');
                        } else {
                            panel.setAttribute('hidden', 'hidden');
                        }
                    });

                    tab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    setTimeout(updateNavArrows, 320);
                });
            });

            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    scrollNav(-1);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function () {
                    scrollNav(1);
                });
            }

            if (nav) {
                nav.addEventListener('scroll', updateNavArrows, { passive: true });
                window.addEventListener('resize', updateNavArrows);
                updateNavArrows();
                // Recheck after fonts/layout settle
                setTimeout(updateNavArrows, 100);
                setTimeout(updateNavArrows, 400);
            }

            const bootcampSection = document.getElementById('bootcamps-section');
            if (bootcampSection) {
                const bootcampObserver = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        entry.target.querySelectorAll('.card-tilt-wrapper').forEach(function (wrapper, index) {
                            setTimeout(function () {
                                wrapper.classList.add('is-visible');
                            }, index * 150);
                        });
                        bootcampObserver.unobserve(entry.target);
                    });
                }, { threshold: 0.1 });
                bootcampObserver.observe(bootcampSection);

                if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    bootcampSection.querySelectorAll('.card-tilt-wrapper').forEach(function (wrapper) {
                        const tiltCard = wrapper.querySelector('.year-portal');
                        if (!tiltCard) return;

                        wrapper.addEventListener('mousemove', function (e) {
                            const rect = wrapper.getBoundingClientRect();
                            const x = e.clientX - rect.left;
                            const y = e.clientY - rect.top;
                            const centerX = rect.width / 2;
                            const centerY = rect.height / 2;
                            const rotateX = ((y - centerY) / centerY) * -8;
                            const rotateY = ((x - centerX) / centerX) * 8;

                            tiltCard.style.transform = 'rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-6px)';
                            tiltCard.style.setProperty('--mouse-x', x + 'px');
                            tiltCard.style.setProperty('--mouse-y', y + 'px');
                        });

                        wrapper.addEventListener('mouseleave', function () {
                            tiltCard.style.transform = 'rotateX(0deg) rotateY(0deg) translateY(0)';
                        });
                    });
                }
            }
        });
    </script>
@endsection

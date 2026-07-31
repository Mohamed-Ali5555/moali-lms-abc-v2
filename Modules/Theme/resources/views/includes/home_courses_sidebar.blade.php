@if (isset($homeCourses) && $homeCourses->isNotEmpty())
    <section class="hc-slider" id="home-courses-slider" dir="rtl">
        <div class="hc-slider__bg" aria-hidden="true">
            <span class="hc-slider__orb hc-slider__orb--1"></span>
            <span class="hc-slider__orb hc-slider__orb--2"></span>
            <span class="hc-slider__grid"></span>
        </div>

        <div class="container">
            <div class="hc-slider__head">
                <div class="hc-slider__copy">
                    <span class="hc-slider__eyebrow">{{ get_phrase('جديد الآن') }}</span>
                    <h2 class="hc-slider__title">{{ get_phrase('أحدث الكورسات') }}</h2>
                    <p class="hc-slider__desc">
                        {{ get_phrase('اكتشف أحدث الكورسات المختارة وانطلق مباشرة نحو درسك القادم.') }}
                    </p>
                </div>
                <div class="hc-slider__nav">
                    <button type="button" class="hc-slider__btn hc-slider__btn--prev" aria-label="{{ get_phrase('السابق') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </button>
                    <button type="button" class="hc-slider__btn hc-slider__btn--next" aria-label="{{ get_phrase('التالي') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="swiper hc-swiper" id="swiperHomeCourses">
                <div class="swiper-wrapper">
                    @foreach ($homeCourses as $course)
                        @php
                            $cat = $course->category;
                            $parent = $cat && (int) $cat->parent_id > 0 ? $cat->parent : null;
                            $isFree = (int) $course->is_paid === 0
                                && ($course->price == 0 || $course->price < 0 || $course->price === null);
                            $hasDiscount = (int) $course->discount_flag === 1;
                        @endphp

                        <div class="swiper-slide">
                            <article class="hc-card">
                                <a href="{{ route('theme.course.details', $course->id) }}"
                                    class="hc-card__media"
                                    aria-label="{{ $course->title }}">
                                    <img src="{{ get_image($course->thumbnail ?? '') }}"
                                        alt="{{ $course->title }}"
                                        loading="lazy">
                                    <span class="hc-card__shine" aria-hidden="true"></span>
                                    @if ($isFree)
                                        <span class="hc-card__badge hc-card__badge--free">{{ get_phrase('مجاني') }}</span>
                                    @elseif ($hasDiscount)
                                        <span class="hc-card__badge hc-card__badge--sale">{{ get_phrase('خصم') }}</span>
                                    @else
                                        <span class="hc-card__badge">{{ get_phrase('جديد') }}</span>
                                    @endif
                                </a>

                                <div class="hc-card__body">
                                    <div class="hc-card__cats">
                                        @if ($parent)
                                            <span class="hc-card__cat">{{ $parent->title }}</span>
                                            @if ($cat)
                                                <span class="hc-card__sep" aria-hidden="true">/</span>
                                                <span class="hc-card__sub">{{ $cat->title }}</span>
                                            @endif
                                        @elseif ($cat)
                                            <span class="hc-card__cat">{{ $cat->title }}</span>
                                        @endif
                                    </div>

                                    <h3 class="hc-card__title">
                                        <a href="{{ route('theme.course.details', $course->id) }}">
                                            {{ $course->title }}
                                        </a>
                                    </h3>

                                    <div class="hc-card__footer">
                                        <div class="hc-card__price">
                                            @if ($isFree)
                                                <span class="hc-card__price-now hc-card__price-now--free">
                                                    {{ get_phrase('مجاني') }}
                                                </span>
                                            @elseif ($hasDiscount)
                                                <span class="hc-card__price-now">
                                                    {{ currency($course->discount_price) }}
                                                </span>
                                                <del class="hc-card__price-old">{{ currency($course->price) }}</del>
                                            @else
                                                <span class="hc-card__price-now">
                                                    {{ currency($course->price) }}
                                                </span>
                                            @endif
                                        </div>

                                        <a href="{{ route('theme.course.details', $course->id) }}"
                                            class="hc-card__cta">
                                            <span>{{ get_phrase('عرض الكورس') }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <line x1="19" y1="12" x2="5" y2="12" />
                                                <polyline points="12 19 5 12 12 5" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                <div class="hc-slider__pagination"></div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swiper === 'undefined') return;

            new Swiper('#swiperHomeCourses', {
                loop: {{ $homeCourses->count() > 3 ? 'true' : 'false' }},
                speed: 750,
                grabCursor: true,
                autoplay: {
                    delay: 3200,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                slidesPerView: 1.15,
                spaceBetween: 14,
                centeredSlides: true,
                navigation: {
                    nextEl: '.hc-slider__btn--next',
                    prevEl: '.hc-slider__btn--prev',
                },
                pagination: {
                    el: '.hc-slider__pagination',
                    clickable: true,
                },
                breakpoints: {
                    576: {
                        slidesPerView: 1.45,
                        spaceBetween: 16,
                        centeredSlides: true,
                    },
                    768: {
                        slidesPerView: 2.15,
                        spaceBetween: 18,
                        centeredSlides: false,
                    },
                    992: {
                        slidesPerView: 3,
                        spaceBetween: 22,
                        centeredSlides: false,
                    },
                    1200: {
                        slidesPerView: 3.35,
                        spaceBetween: 24,
                        centeredSlides: false,
                    },
                },
            });
        });
    </script>
@endif

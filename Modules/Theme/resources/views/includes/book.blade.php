@if (get_theme_settings('book_status') == 1 && isset($books) && count($books) > 0)

    <section class="books-pro" id="books-section" dir="rtl">
        <div class="books-pro__bg" aria-hidden="true">
            <span class="books-pro__orb books-pro__orb--1"></span>
            <span class="books-pro__orb books-pro__orb--2"></span>
        </div>

        <div class="container">
            <div class="books-pro__head">
                <span class="books-pro__eyebrow">اقرأ · راجع · تفوّق</span>
                <h2 class="books-pro__title">مكتبة التفوق</h2>
                <p class="books-pro__desc">تجربة فريدة تليق بطموحك — كتب مختارة تدعمك خطوة بخطوة.</p>
            </div>

            <div class="swiper" id="swiperBooks">
                <div class="swiper-wrapper">
                    @foreach ($books as $book)
                        @php
                            $bookQty = $cartItems[$book->id] ?? 0;
                            $hasDiscount = (int) ($book->if_discount ?? 0) === 1;
                        @endphp
                        <div class="swiper-slide">
                            <article class="book-stage pro-book-card">
                                <span class="book-stage__glow" aria-hidden="true"></span>
                                <span class="book-stage__ring" aria-hidden="true"></span>

                                <div class="book-stage__cover-wrap book-3d-container">
                                    @if ($hasDiscount)
                                        <span class="book-stage__badge">خصم</span>
                                    @endif
                                    <a href="{{ route('theme.book.details', $book->id) }}" class="book-stage__cover-link" aria-label="{{ $book->title }}">
                                        <img
                                            src="{{ get_image($book->thumbnail ?? '') }}"
                                            alt="{{ $book->title }}"
                                            class="book-cover-3d book-stage__cover"
                                            loading="lazy"
                                        >
                                    </a>
                                </div>

                                <div class="book-stage__meta card-content-pro">
                                    <a href="{{ route('theme.book.details', $book->id) }}" class="book-stage__title-link">
                                        <h3 class="book-stage__title">{{ $book->title }}</h3>
                                    </a>

                                    <div class="book-stage__price">
                                        @if ($hasDiscount)
                                            <span class="book-stage__price-now">{{ $book->discount_price }}</span>
                                            <del class="book-stage__price-old">{{ $book->price }}</del>
                                        @else
                                            <span class="book-stage__price-now">{{ $book->price }}</span>
                                        @endif
                                        <span class="book-stage__currency">جنيهًا</span>
                                    </div>

                                    <button
                                        type="button"
                                        class="book-stage__cta btn-pro-cart add-to-cart"
                                        id-element="{{ $book->id }}"
                                        element-type="book"
                                    >
                                        <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                                        <span>أضف إلى السلة</span>
                                    </button>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-prev" aria-label="السابق"></div>
                <div class="swiper-button-next" aria-label="التالي"></div>
            </div>
        </div>
    </section>

@endif

@include('theme::includes.addCart')
@if (get_theme_settings('book_status') == 1 && isset($books) && count($books) > 0)
<script defer>
    const bookObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                bookObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12
    });

    document.querySelectorAll('.pro-book-card').forEach(card => {
        bookObserver.observe(card);
    });

    const swiperBooks = new Swiper("#swiperBooks", {
        loop: true,
        slidesPerView: 1.2,
        spaceBetween: 18,
        centeredSlides: true,
        navigation: {
            nextEl: ".books-pro .swiper-button-next",
            prevEl: ".books-pro .swiper-button-prev",
        },
        breakpoints: {
            576: {
                slidesPerView: 1.45,
                spaceBetween: 18,
            },
            768: {
                slidesPerView: 2.1,
                spaceBetween: 22,
                centeredSlides: false,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 26,
                centeredSlides: false,
            }
        },
        on: {
            slideChange: function() {
                document.querySelectorAll('.pro-book-card').forEach(card => {
                    card.classList.remove('is-visible');
                });
                setTimeout(() => {
                    document.querySelectorAll(
                        '.swiper-slide-active .pro-book-card, .swiper-slide-next .pro-book-card, .swiper-slide-prev .pro-book-card'
                    ).forEach(card => {
                        bookObserver.observe(card);
                    });
                }, 80);
            }
        }
    });
</script>
@endif

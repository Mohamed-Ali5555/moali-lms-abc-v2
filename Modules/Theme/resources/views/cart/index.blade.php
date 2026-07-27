@extends('theme::layouts.master')
@push('title', get_phrase('Cart'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/cart-modern.css') }}">
@endpush

@section('content')
    @php
        $count_items_price = 0;
        $courses_count = count($cart_items);
        $books_count = count($cart_items_books);
        $items_count = $courses_count + $books_count;
        $has_items = $items_count > 0;
    @endphp

    <section class="cart-page-section cart-modern" dir="rtl">
        <div class="section-background-aurora" aria-hidden="true">
            <div class="animated-blob blob-1"></div>
            <div class="animated-blob blob-2"></div>
        </div>

        <div class="container cart-modern__container">
            <header class="cm-header">
                <div class="cm-header__intro">
                    <div class="cm-header__icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1" />
                            <circle cx="20" cy="21" r="1" />
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="cm-header__title">{{ get_phrase('عربة التسوق') }}</h1>
                        <p class="cm-header__sub">
                            @if ($has_items)
                                {{ get_phrase('راجع مشترياتك وأكمل الدفع بخطوات بسيطة.') }}
                            @else
                                {{ get_phrase('عرّبتك فارغة حالياً — ابدأ بإضافة كورس أو كتاب.') }}
                            @endif
                        </p>
                    </div>
                </div>

                @if ($has_items)
                    <div class="cm-header__meta">
                        <span class="cm-chip">
                            <strong>{{ $items_count }}</strong>
                            {{ get_phrase('عنصر') }}
                        </span>
                        @if ($courses_count > 0)
                            <span class="cm-chip cm-chip--soft">{{ $courses_count }} {{ get_phrase('كورس') }}</span>
                        @endif
                        @if ($books_count > 0)
                            <span class="cm-chip cm-chip--soft">{{ $books_count }} {{ get_phrase('كتاب') }}</span>
                        @endif
                    </div>
                @endif
            </header>

            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="cm-panel">
                        @if ($has_items)
                            <div class="cm-list" id="cart-items-list">
                                @foreach ($cart_items as $course)
                                    @php
                                        $course_unit = $course->discount_flag == 1 ? $course->discount_price : $course->price;
                                        $count_items_price += $course_unit;
                                    @endphp
                                    <article class="cm-item" data-type="course">
                                        <div class="cm-item__media">
                                            <img src="{{ get_image($course->thumbnail) }}"
                                                alt="{{ ucfirst($course->title) }}" loading="lazy">
                                            <span class="cm-badge cm-badge--course">{{ get_phrase('كورس') }}</span>
                                        </div>

                                        <div class="cm-item__body">
                                            <h3 class="cm-item__title">{{ ucfirst($course->title) }}</h3>
                                            <div class="cm-item__price">
                                                @if ($course->discount_flag == 1)
                                                    <s class="cm-item__old">{{ currency($course->price, 2) }}</s>
                                                    <span class="cm-item__now">{{ currency($course->discount_price, 2) }}</span>
                                                @else
                                                    <span class="cm-item__now">{{ currency($course->price, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="cm-item__qty cm-item__qty--fixed">
                                            <span class="cm-qty-label">{{ get_phrase('الكمية') }}</span>
                                            <span class="cm-qty-value">1</span>
                                        </div>

                                        <div class="cm-item__total">
                                            <span class="cm-item__total-label">{{ get_phrase('الإجمالي') }}</span>
                                            <span class="cm-item__total-value">
                                                {{ currency($course_unit, 2) }}
                                            </span>
                                        </div>

                                        <a class="cm-item__remove delete-cart-item"
                                            href="{{ route('theme.cart.delete', ['id' => $course->id, 'type' => 'course']) }}"
                                            aria-label="{{ get_phrase('حذف') }}"
                                            data-bs-toggle="tooltip" title="{{ get_phrase('حذف') }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path
                                                    d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14Z"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" />
                                            </svg>
                                        </a>
                                    </article>
                                @endforeach

                                @foreach ($cart_items_books as $book)
                                    @php
                                        $book_unit = $book->if_discount == 1 ? $book->discount_price : $book->price;
                                        $book_line = $book_unit * $book->qty;
                                        $count_items_price += $book_line;
                                    @endphp
                                    <article class="cm-item" data-type="book" data-cart-id="{{ $book->cart_id }}">
                                        <div class="cm-item__media">
                                            <img src="{{ get_image($book->thumbnail) }}"
                                                alt="{{ ucfirst($book->title) }}" loading="lazy">
                                            <span class="cm-badge cm-badge--book">{{ get_phrase('كتاب') }}</span>
                                        </div>

                                        <div class="cm-item__body">
                                            <h3 class="cm-item__title">{{ ucfirst($book->title) }}</h3>
                                            <div class="cm-item__price">
                                                @if ($book->if_discount == 1)
                                                    <s class="cm-item__old">{{ currency($book->price, 2) }}</s>
                                                    <span class="cm-item__now">{{ currency($book->discount_price, 2) }}</span>
                                                @else
                                                    <span class="cm-item__now">{{ currency($book->price, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="cm-item__qty">
                                            <span class="cm-qty-label">{{ get_phrase('الكمية') }}</span>
                                            <div class="cm-qty"
                                                data-url="{{ route('theme.cartBooks.updateQuantity', $book->cart_id) }}">
                                                <button type="button" class="cm-qty__btn decrease"
                                                    data-url="{{ route('theme.cartBooks.updateQuantity', $book->cart_id) }}"
                                                    aria-label="{{ get_phrase('تقليل الكمية') }}"
                                                    @if ($book->qty <= 1) disabled @endif>
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M6 12h12" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                </button>
                                                <span class="cm-qty__value quantity-input">{{ $book->qty }}</span>
                                                <button type="button" class="cm-qty__btn increase"
                                                    data-url="{{ route('theme.cartBooks.updateQuantity', $book->cart_id) }}"
                                                    aria-label="{{ get_phrase('زيادة الكمية') }}">
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M12 6v12M6 12h12" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="cm-item__total">
                                            <span class="cm-item__total-label">{{ get_phrase('الإجمالي') }}</span>
                                            <span class="cm-item__total-value">
                                                {{ currency($book_line, 2) }}
                                            </span>
                                        </div>

                                        <a class="cm-item__remove delete-cart-item"
                                            href="{{ route('theme.cart.delete', ['id' => $book->id, 'type' => 'book']) }}"
                                            aria-label="{{ get_phrase('حذف') }}"
                                            data-bs-toggle="tooltip" title="{{ get_phrase('حذف') }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path
                                                    d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14Z"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" />
                                            </svg>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="cm-empty">
                                <div class="cm-empty__visual" aria-hidden="true">
                                    <dotlottie-player
                                        src="{{ asset('assets/frontend/default/image/icons/empty.lottie') }}"
                                        background="transparent" speed="1"
                                        style="width: 220px; height: 220px;" loop autoplay>
                                    </dotlottie-player>
                                </div>
                                <h2 class="cm-empty__title">{{ get_phrase('عرّبتك فارغة') }}</h2>
                                <p class="cm-empty__text">
                                    {{ get_phrase('تصفّح أحدث الكورسات والكتب وأضف ما يناسبك.') }}
                                </p>
                                <a href="{{ route('theme.home') }}" class="cm-btn cm-btn--primary">
                                    {{ get_phrase('تصفح المحتوى') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    @php
                        $coupon_discount = $count_items_price * ($discount / 100);
                        $tax = (get_settings('course_selling_tax') / 100) * ($count_items_price - $coupon_discount);
                        $payable = $count_items_price - $coupon_discount + $tax;
                    @endphp

                    <aside class="cm-summary {{ !$has_items ? 'cm-summary--disabled' : '' }}">
                        <div class="cm-summary__head">
                            <h2 class="cm-summary__title">{{ get_phrase('ملخص الفاتورة') }}</h2>
                            <p class="cm-summary__hint">{{ get_phrase('راجع التفاصيل قبل إتمام الدفع') }}</p>
                        </div>

                        <div class="cm-summary__rows">
                            <div class="cm-summary__row">
                                <span>{{ get_phrase('المجموع') }}</span>
                                <span>{{ currency($count_items_price, 2) }}</span>
                            </div>

                            @if ($discount)
                                <div class="cm-summary__row cm-summary__row--discount">
                                    <span>
                                        {{ get_phrase('الخصم') }}
                                        ({{ $discount }}{{ get_phrase('%') }})
                                    </span>
                                    <span>- {{ currency($coupon_discount, 2) }}</span>
                                </div>
                            @endif

                            <div class="cm-summary__row">
                                <span>
                                    {{ get_phrase('ضريبة') }}
                                    ({{ get_settings('course_selling_tax') }}{{ get_phrase('%') }})
                                </span>
                                <span>+ {{ currency($tax, 2) }}</span>
                            </div>

                            <div class="cm-summary__row cm-summary__row--total">
                                <span>{{ get_phrase('الإجمالى') }}</span>
                                <span>{{ currency($payable, 2) }}</span>
                            </div>
                        </div>

                        <form action="{{ route('theme.payout') }}" method="post" class="cm-summary__form">
                            @csrf
                            <input type="hidden" name="payable" value="{{ $payable }}">
                            <input type="hidden" name="coupon_code" value="{{ request()->query('coupon') }}">
                            <input type="hidden" name="coupon_discount" value="{{ $coupon_discount }}">
                            <input type="hidden" name="tax" value="{{ $tax }}">
                            <input type="hidden" name="items"
                                value="{{ json_encode($cart_items->map(fn($item) => ['id' => $item->id, 'qty' => $item->qty ?? 1])) }}">
                            <input type="hidden" name="books"
                                value="{{ json_encode($cart_items_books->map(fn($item) => ['id' => $item->id, 'qty' => $item->qty])) }}">

                            <div class="cm-coupon">
                                <label class="cm-coupon__label" for="cart-coupon">
                                    {{ get_phrase('لديك كود خصم؟') }}
                                </label>

                                @if (request()->has('coupon') && isset($coupon) && $coupon_discount > 0)
                                    <div class="cm-coupon__active">
                                        <div>
                                            <span class="cm-coupon__active-label">{{ get_phrase('الكود مفعّل') }}</span>
                                            <strong>{{ request()->query('coupon') }}</strong>
                                            <span class="cm-coupon__pct">({{ $coupon->discount }}%)</span>
                                        </div>
                                        <a href="{{ route('theme.cart') }}" class="cm-coupon__clear"
                                            aria-label="{{ get_phrase('إلغاء الكوبون') }}">
                                            <i class="fi-rr-cross-circle"></i>
                                        </a>
                                    </div>
                                @endif

                                <div class="cm-coupon__group">
                                    <input type="text" class="cm-coupon__input" id="cart-coupon" name="coupon"
                                        dir="rtl" placeholder="{{ get_phrase('كود الخصم') }}"
                                        value="{{ request()->query('coupon') }}"
                                        @disabled(!$has_items)>
                                    <button type="button" class="cm-coupon__btn" id="apply-coupon"
                                        @disabled(!$has_items)>
                                        {{ get_phrase('تفعيل') }}
                                    </button>
                                </div>
                            </div>

                            <div class="cm-gift send_gift_check">
                                <label class="cm-gift__check" for="send_gift">
                                    <input class="form-check-input" type="checkbox" name="is_gift" value="1"
                                        id="send_gift" @disabled(!$has_items)>
                                    <span>{{ get_phrase('إرسال كهدية') }}</span>
                                </label>
                                <input type="tel" inputmode="numeric" class="cm-gift__input gifted_user d-none"
                                    name="" autocomplete="tel"
                                    placeholder="{{ get_phrase('أدخل رقم هاتف المستلم') }}"
                                    maxlength="14" pattern="[0-9]{10,14}">
                            </div>

                            <button type="submit" class="cm-btn cm-btn--checkout"
                                @if ($count_items_price == 0) disabled @endif>
                                <span>{{ get_phrase('مواصلة الدفع') }}</span>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </form>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <script>
        "use strict";
        $(document).ready(function() {
            $('.increase, .decrease').on('click', function() {
                let btn = $(this);
                if (btn.prop('disabled') || btn.data('loading')) {
                    return;
                }

                let item = btn.closest('.cm-item');
                let url = btn.data('url');
                let action = btn.hasClass('increase') ? 'increase' : 'decrease';

                btn.data('loading', true);
                item.addClass('is-updating');

                $.ajax({
                    url: url,
                    type: 'post',
                    data: {
                        action: action,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            item.removeClass('is-updating');
                            btn.data('loading', false);
                            Swal.fire({
                                icon: 'error',
                                title: '{{ get_phrase('حدث خطأ') }}',
                                text: data.message || '{{ get_phrase('تعذر تحديث الكمية') }}'
                            });
                        }
                    },
                    error: function() {
                        item.removeClass('is-updating');
                        btn.data('loading', false);
                        Swal.fire({
                            icon: 'error',
                            title: '{{ get_phrase('حدث خطأ') }}',
                            text: '{{ get_phrase('تعذر تحديث الكمية') }}'
                        });
                    }
                });
            });

            $('.delete-cart-item').on('click', function(e) {
                e.preventDefault();
                let href = $(this).attr('href');

                Swal.fire({
                    title: '{{ get_phrase('هل أنت متأكد؟') }}',
                    text: '{{ get_phrase('لن تتمكن من التراجع عن هذا') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '{{ get_phrase('نعم، احذف') }}',
                    cancelButtonText: '{{ get_phrase('إلغاء') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });

            $('#apply-coupon').on('click', function(e) {
                e.preventDefault();
                let code = $('input[name="coupon"]').val();
                if (!code) {
                    return;
                }
                window.location.href = "{{ route('theme.cart') }}" + "?coupon=" + encodeURIComponent(code);
            });

            $('input[name="coupon"]').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#apply-coupon').trigger('click');
                }
            });

            $('#send_gift').on('change', function() {
                if ($(this).prop('checked')) {
                    $('.gifted_user').attr({
                        'name': 'gifted_user_phone',
                        'required': '1'
                    }).removeClass('d-none').trigger('focus');
                } else {
                    $('.gifted_user').removeAttr('name required').val('').addClass('d-none');
                }
            });

            $('.gifted_user').on('input', function() {
                this.value = this.value.replace(/\D+/g, '').slice(0, 14);
            });
        });
    </script>
@endsection

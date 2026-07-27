@extends('theme::layouts.master')

@php
    $book = $data['book'];
    $hasDiscount = (int) ($book->if_discount ?? 0) === 1;
    $displayPrice = $hasDiscount ? $book->discount_price : $book->price;
    $categoryTitle = optional($book->category)->title;
    $isAvailable = (int) ($book->status ?? 1) === 1;
@endphp

@push('title', $book->title)
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/book-details-modern.css') }}">
@endpush

@section('content')
    <section class="bd-modern" dir="rtl">
        <div class="section-background-aurora" aria-hidden="true">
            <div class="animated-blob blob-1"></div>
            <div class="animated-blob blob-2"></div>
        </div>

        <div class="container bd-modern__container">
            <nav class="bd-crumb" aria-label="{{ get_phrase('مسار التنقل') }}">
                <a href="{{ url('/') }}">{{ get_phrase('الرئيسية') }}</a>
                <span class="bd-crumb__sep" aria-hidden="true">/</span>
                @if ($categoryTitle)
                    <span>{{ $categoryTitle }}</span>
                    <span class="bd-crumb__sep" aria-hidden="true">/</span>
                @endif
                <span class="bd-crumb__current">{{ $book->title }}</span>
            </nav>

            <div class="bd-layout">
                <aside class="bd-cover">
                    <div class="bd-cover__stage">
                        @if ($hasDiscount)
                            <span class="bd-cover__badge">{{ get_phrase('خصم') }}</span>
                        @endif
                        <span class="bd-cover__glow" aria-hidden="true"></span>
                        <div class="bd-cover__frame">
                            <img src="{{ get_image($book->thumbnail ?? '') }}"
                                alt="{{ $book->title }}"
                                loading="eager">
                        </div>
                    </div>
                </aside>

                <div class="bd-info">
                    @if ($categoryTitle)
                        <span class="bd-info__chip">
                            <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                            {{ $categoryTitle }}
                        </span>
                    @endif

                    <h1 class="bd-info__title">{{ $book->title }}</h1>

                    @if (!empty($book->disc))
                        <div class="bd-info__desc">
                            {!! removeScripts($book->disc) !!}
                        </div>
                    @endif

                    <div class="bd-meta">
                        <span class="bd-meta__item">
                            <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                            {{ get_phrase('كتاب رقمي') }}
                        </span>
                        <span class="bd-meta__item">
                            <i class="fa-solid fa-{{ $isAvailable ? 'circle-check' : 'circle-xmark' }}" aria-hidden="true"></i>
                            {{ $isAvailable ? get_phrase('متوفر للشراء') : get_phrase('غير متوفر حالياً') }}
                        </span>
                    </div>

                    <div class="bd-info__panel">
                        <div class="bd-price">
                            <span class="bd-price__label">{{ get_phrase('السعر') }}</span>
                            <span class="bd-price__now">{{ currency($displayPrice) }}</span>
                            @if ($hasDiscount)
                                <del class="bd-price__old">{{ currency($book->price) }}</del>
                            @endif
                        </div>
                        @if ($hasDiscount)
                            <p class="bd-price__hint">{{ get_phrase('سعر خاص لفترة محدودة') }}</p>
                        @endif
                    </div>

                    <div class="bd-actions">
                        @if (!$isAvailable)
                            <div class="bd-unavailable" role="status">
                                {{ get_phrase('عفوًا، سيتم توفير هذا الكتاب مرة أخرى قريبًا.') }}
                            </div>
                        @else
                            <button type="button"
                                class="bd-btn bd-btn--primary add_to_cart"
                                id-element="{{ $book->id }}"
                                element-type="book">
                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                                <span>{{ get_phrase('أضف إلى عربة التسوق') }}</span>
                            </button>

                            <a class="bd-btn bd-btn--secondary" href="{{ route('theme.cart') }}">
                                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                                <span>{{ get_phrase('مراجعة العربة والذهاب للدفع') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                title: "{{ get_phrase('نجاح!') }}",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "{{ get_phrase('حسناً') }}"
            });
        </script>
    @endif

    <script>
        $('.add_to_cart').on('click', function() {
            let id = $(this).attr('id-element');
            let type = $(this).attr('element-type');
            let cartNumber = +$('#cart-number').text();
            let url = "{{ route('theme.cart.store', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    id: id,
                    type: type,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.result) {
                        $('#cart-number').text(cartNumber + 1);

                        if (response.action === 'added') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ get_phrase('تمت الإضافة') }}',
                                text: response.message
                            });
                        } else if (response.action === 'incremented') {
                            Swal.fire({
                                icon: 'info',
                                title: '{{ get_phrase('تم التحديث') }}',
                                text: response.message
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            text: '{{ get_phrase('يجب عليك تسجيل الدخول أولاً لشراء الكتاب') }}',
                            showCancelButton: false,
                            confirmButtonText: '{{ get_phrase('تسجيل الدخول') }}'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route('theme.login') }}';
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let message = '{{ get_phrase('حدث خطأ ما') }}';

                    if (xhr.responseJSON) {
                        message = xhr.responseJSON.message;

                        if (message && message.includes('يجب ان تسجل دخول اولا')) {
                            Swal.fire({
                                icon: 'error',
                                title: message,
                                text: '{{ get_phrase('يجب عليك تسجيل الدخول أولاً لشراء الكتاب') }}',
                                showCancelButton: false,
                                confirmButtonText: '{{ get_phrase('تسجيل الدخول') }}'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '{{ route('theme.show_login') }}';
                                }
                            });
                            return;
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: '{{ get_phrase('خطأ') }}',
                        text: message
                    });
                }
            });
        });
    </script>
@endsection

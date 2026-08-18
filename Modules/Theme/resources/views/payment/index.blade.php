@extends('theme::layouts.master')
@push('title', get_phrase('Payment'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/payment-modern.css') }}">
@endpush

@section('content')
    @php
        $items = $payment_details['items'] ?? [];
        $items_count = count($items);
        $subtotal = 0;
        foreach ($items as $row) {
            $unit = ($row['discount_price'] ?? 0) > 0 ? $row['discount_price'] : ($row['price'] ?? 0);
            $subtotal += $unit * ($row['qty'] ?? 1);
        }
        $tax = (float) ($payment_details['tax'] ?? 0);
        $coupon_code = $payment_details['coupon'] ?? '';
        $coupon_discount = (float) ($payment_details['custom_field']['coupon_discount'] ?? 0);
        $payable = (float) ($payment_details['payable_amount'] ?? 0);
        $cancel_url =  route('theme.cart');
        $is_gift = !empty($payment_details['custom_field']['gifted_user_id'] ?? null);
    @endphp

    <section class="checkout-page-section pay-modern" dir="rtl">
        <div class="section-background-aurora" aria-hidden="true">
            <div class="animated-blob blob-1"></div>
            <div class="animated-blob blob-2"></div>
        </div>

        <div class="container pay-modern__container">
            <header class="pm-header">
                <div class="pm-header__intro">
                    <div class="pm-header__icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                            <line x1="1" y1="10" x2="23" y2="10" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="pm-header__title">{{ get_phrase('إتمام عملية الدفع') }}</h1>
                        <p class="pm-header__sub">
                            {{ get_phrase('اختر طريقة الدفع المناسبة وأكمل طلبك بأمان.') }}
                        </p>
                    </div>
                </div>

                <nav class="pm-steps" aria-label="{{ get_phrase('خطوات الدفع') }}">
                    <a href="{{ $cancel_url }}" class="pm-step">
                        <span class="pm-step__num">1</span>
                        <span class="pm-step__label">{{ get_phrase('العربة') }}</span>
                    </a>
                    <span class="pm-steps__line" aria-hidden="true"></span>
                    <div class="pm-step is-active" aria-current="step">
                        <span class="pm-step__num">2</span>
                        <span class="pm-step__label">{{ get_phrase('الدفع') }}</span>
                    </div>
                </nav>
            </header>

            <div class="row g-4 align-items-start">
                <div class="col-lg-5 order-lg-1 order-2">
                    <aside class="pm-summary">
                        <div class="pm-summary__head">
                            <h2 class="pm-summary__title">{{ get_phrase('ملخص الطلب') }}</h2>
                            <span class="pm-summary__count">{{ $items_count }} {{ get_phrase('عنصر') }}</span>
                        </div>

                        @if ($is_gift)
                            <div class="pm-gift-note">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M20 12v10H4V12M2 7h20v5H2V7zm10-5a3 3 0 0 0-3 3v2h6V5a3 3 0 0 0-3-3z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span>{{ get_phrase('هذا الطلب يُرسل كهدية') }}</span>
                            </div>
                        @endif

                        <div class="pm-items">
                            @foreach ($items as $item)
                                @php
                                    $qty = $item['qty'] ?? 1;
                                    $unit = ($item['discount_price'] ?? 0) > 0 ? $item['discount_price'] : ($item['price'] ?? 0);
                                    $line = $unit * $qty;
                                    $type = $item['type'] ?? 'course';
                                @endphp
                                <article class="pm-item">
                                    <div class="pm-item__meta">
                                        <span class="pm-badge pm-badge--{{ $type === 'book' ? 'book' : 'course' }}">
                                            {{ $type === 'book' ? get_phrase('كتاب') : get_phrase('كورس') }}
                                        </span>
                                        <h3 class="pm-item__title">{{ $item['title'] }}</h3>
                                        @if ($qty > 1)
                                            <span class="pm-item__qty">× {{ $qty }}</span>
                                        @endif
                                    </div>
                                    <div class="pm-item__price">
                                        @if (($item['discount_price'] ?? 0) > 0)
                                            <s>{{ currency($item['price'] * $qty, 2) }}</s>
                                        @endif
                                        <strong>{{ currency($line, 2) }}</strong>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="pm-totals">
                            <div class="pm-totals__row">
                                <span>{{ get_phrase('المجموع') }}</span>
                                <span>{{ currency($subtotal, 2) }}</span>
                            </div>

                            @if ($coupon_discount > 0)
                                <div class="pm-totals__row pm-totals__row--discount">
                                    <span>
                                        {{ get_phrase('الخصم') }}
                                        @if ($coupon_code)
                                            <em>({{ $coupon_code }})</em>
                                        @endif
                                    </span>
                                    <span>- {{ currency($coupon_discount, 2) }}</span>
                                </div>
                            @endif

                            @if ($tax > 0)
                                <div class="pm-totals__row">
                                    <span>{{ get_phrase('الضريبة') }}</span>
                                    <span>+ {{ currency($tax, 2) }}</span>
                                </div>
                            @endif

                            <div class="pm-totals__row pm-totals__row--payable">
                                <span>{{ get_phrase('الإجمالي النهائي') }}</span>
                                <span>{{ currency($payable, 2) }}</span>
                            </div>
                        </div>

                        <a href="{{ $cancel_url }}" class="pm-back">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ get_phrase('العودة للعربة') }}
                        </a>
                    </aside>
                </div>

                <div class="col-lg-7 order-lg-2 order-1">
                    <div class="pm-panel">
                        <div class="pm-panel__head">
                            <h2 class="pm-panel__title">{{ get_phrase('اختر طريقة الدفع') }}</h2>
                            <p class="pm-panel__hint">{{ get_phrase('اضغط على الطريقة المناسبة ثم أكمل البيانات') }}</p>
                        </div>

                        <div class="pm-gateways" role="tablist" aria-label="{{ get_phrase('طرق الدفع') }}">
                            @foreach ($payment_gateways as $payment_gateway)
                                <button type="button"
                                    class="pm-gateway tabItem"
                                    onclick="showPaymentGatewayByAjax('{{ $payment_gateway->identifier }}')"
                                    id="{{ $payment_gateway->identifier }}-tab"
                                    data-gateway="{{ $payment_gateway->identifier }}"
                                    role="tab"
                                    aria-controls="showPaymentGatewayByAjax"
                                    aria-selected="false">
                                    <span class="pm-gateway__logo">
                                        <img src="{{ asset('assets/payment/' . $payment_gateway->identifier . '.png') }}"
                                            alt="{{ $payment_gateway->title }}" loading="lazy">
                                    </span>
                                    <span class="pm-gateway__title">{{ $payment_gateway->title }}</span>
                                    <span class="pm-gateway__check" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <polyline points="20 6 9 17 4 12" stroke="currentColor"
                                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <div class="pm-form-wrap payment-form-container" id="payment-form-container">
                            <div id="showPaymentGatewayByAjax" class="pm-form-pane" role="tabpanel">
                                <div class="pm-placeholder">
                                    <div class="pm-placeholder__icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <rect x="1" y="4" width="22" height="16" rx="2"
                                                stroke="currentColor" stroke-width="2" />
                                            <line x1="1" y1="10" x2="23" y2="10"
                                                stroke="currentColor" stroke-width="2" />
                                        </svg>
                                    </div>
                                    <h3>{{ get_phrase('برجاء اختيار طريقة الدفع') }}</h3>
                                    <p>{{ get_phrase('بعد الاختيار ستظهر هنا خطوات إتمام العملية.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        "use strict";

        function setActiveGateway(identifier) {
            document.querySelectorAll('.pm-gateway').forEach(function(el) {
                var active = el.getAttribute('data-gateway') === identifier;
                el.classList.toggle('is-active', active);
                el.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        }

        function showPaymentGatewayByAjax(identifier) {
            setActiveGateway(identifier);

            $('#showPaymentGatewayByAjax').html(
                '<div class="pm-loading"><div class="spinner-border" role="status"><span class="visually-hidden">{{ get_phrase('جاري التحميل') }}</span></div><p>{{ get_phrase('جاري تحميل طريقة الدفع...') }}</p></div>'
            );

            if (identifier === 'card') {
                $('#showPaymentGatewayByAjax').html(`
                    <div class="pm-card-pay">
                        <div class="pm-card-pay__head">
                            <div class="pm-card-pay__icon" aria-hidden="true">
                                <i class="fi fi-rr-credit-card"></i>
                            </div>
                            <div>
                                <h3>{{ get_phrase('شراء بواسطة الكارت') }}</h3>
                                <p>{{ get_phrase('أدخل رقم الكارت للتحقق وإتمام الدفع') }}</p>
                            </div>
                        </div>
                        <div class="pm-card-pay__body">
                            <label for="card_code" class="pm-card-pay__label">{{ get_phrase('رقم الكارت') }}</label>
                            <div class="pm-card-pay__group">
                                <input type="text" inputmode="numeric" class="pm-card-pay__input" id="card_code"
                                    placeholder="{{ get_phrase('أدخل رقم الكارت') }}" required autocomplete="off">
                                <button type="button" class="pm-card-pay__btn" id="verify_card_btn">
                                    {{ get_phrase('تحقق') }}
                                </button>
                            </div>
                            <div class="alert mt-3 d-none" id="card_result" role="alert"></div>
                        </div>
                    </div>
                `);
                return;
            }

            $.ajax({
                url: "{{ route('theme.payment.show_payment_gateway_by_ajax', '') }}/" + identifier,
                success: function(response) {
                    if (response.status == false) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ get_phrase('خطأ') }}',
                            text: response.message,
                            confirmButtonText: '{{ get_phrase('حسناً') }}'
                        });
                        $('#showPaymentGatewayByAjax').html(
                            '<div class="pm-placeholder"><h3>{{ get_phrase('تعذر تحميل طريقة الدفع') }}</h3><p>' +
                            (response.message || '') + '</p></div>'
                        );
                    } else {
                        if (identifier === "fawrypay" || identifier === "paymob") {
                            if (response.url) {
                                window.open(response.url, "_blank");
                            }
                            $('#showPaymentGatewayByAjax').html(
                                '<div class="pm-placeholder">' +
                                '<div class="pm-placeholder__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>' +
                                '<h3>{{ get_phrase('تم فتح بوابة الدفع') }}</h3>' +
                                '<p>{{ get_phrase('أكمل الدفع في النافذة الجديدة، ثم ارجع هنا بعد الانتهاء.') }}</p>' +
                                (response.url ? '<a class="pm-back" href="' + response.url + '" target="_blank" rel="noopener">{{ get_phrase('فتح بوابة الدفع مرة أخرى') }}</a>' : '') +
                                '</div>'
                            );
                            return;
                        }
                        $('#showPaymentGatewayByAjax').html(response);
                    }
                },
                error: function() {
                    $('#showPaymentGatewayByAjax').html(
                        '<div class="pm-placeholder"><h3>{{ get_phrase('تعذر تحميل طريقة الدفع') }}</h3><p>{{ get_phrase('حاول مرة أخرى.') }}</p></div>'
                    );
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (!(e.target && e.target.id === 'verify_card_btn')) {
                return;
            }

            var cardInput = document.getElementById('card_code');
            var cardCode = cardInput ? cardInput.value.trim() : '';
            var btn = e.target;

            if (cardCode === '') {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ get_phrase('تنبيه') }}',
                    text: '{{ get_phrase('من فضلك أدخل رقم الكارت') }}',
                    confirmButtonText: '{{ get_phrase('حسناً') }}'
                });
                return;
            }

            btn.disabled = true;
            btn.textContent = '{{ get_phrase('جاري التحقق...') }}';

            fetch("{{ route('theme.payment.verify_card') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        card_code: cardCode
                    })
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    var result = document.getElementById('card_result');
                    if (!result) {
                        return;
                    }
                    result.classList.remove('d-none', 'alert-success', 'alert-danger');

                    if (data.success) {
                        btn.textContent = '{{ get_phrase('تم التحقق') }}';
                        result.classList.add('alert-success');
                        result.innerText = "{{ get_phrase('تم دفع الفاتورة بمبلغ') }} " + data.amount +
                            " {{ get_phrase('بنجاح') }}";
                        setTimeout(function() {
                            window.location.href = "{{ route('theme.my.courses') }}";
                        }, 2500);
                    } else {
                        btn.disabled = false;
                        btn.textContent = '{{ get_phrase('تحقق') }}';
                        result.classList.add('alert-danger');
                        result.innerText = data.message || '{{ get_phrase('تعذر التحقق من الكارت') }}';
                    }
                })
                .catch(function() {
                    btn.disabled = false;
                    btn.textContent = '{{ get_phrase('تحقق') }}';
                    Swal.fire({
                        icon: 'error',
                        title: '{{ get_phrase('خطأ') }}',
                        text: '{{ get_phrase('حدث خطأ أثناء التحقق') }}',
                        confirmButtonText: '{{ get_phrase('حسناً') }}'
                    });
                });
        });
    </script>
@endsection

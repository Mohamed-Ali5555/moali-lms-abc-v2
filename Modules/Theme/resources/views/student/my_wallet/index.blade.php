@extends('theme::layouts.master')

@push('title', get_phrase('محفظتي'))
@push('meta')@endpush
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/wallet-modern.css') }}">
@endpush

@section('content')
<section class="wishlist-content main_content wl-page" dir="rtl">
    <div class="profile-banner-area"></div>
    <div class="container profile-banner-area-container">
        <div class="row">
            @include('theme::student.left_sidebar')

            <div class="col-lg-9">
                <div class="wl-header">
                    <div class="wl-header__intro">
                        <div class="wl-header__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4" />
                                <path d="M3 5v14a2 2 0 0 0 2 2h16v-5" />
                                <path d="M18 12a2 2 0 0 0 0 4h4v-4Z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="wl-header__title">{{ get_phrase('محفظتي') }}</h1>
                            <p class="wl-header__sub">
                                {{ get_phrase('تابع رصيدك، راجع معاملاتك، واشحن محفظتك بسهولة.') }}
                            </p>
                        </div>
                    </div>

                    <div class="wl-tabs" role="tablist">
                        <button type="button" class="wl-tab is-active" id="wallet_transaction" role="tab"
                            aria-selected="true">
                            {{ get_phrase('المعاملات') }}
                        </button>
                        <button type="button" class="wl-tab" id="wallet_charging" role="tab"
                            aria-selected="false">
                            {{ get_phrase('شحن المحفظة') }}
                        </button>
                    </div>
                </div>

                <div class="wl-balance">
                    <span class="wl-balance__label">{{ get_phrase('الرصيد الحالي') }}</span>
                    <div class="wl-balance__amount">
                        <strong>{{ number_format($wallet_balance ?? auth()->user()->wallet ?? 0, 0) }}</strong>
                        <span>{{ currency_symbol() }}</span>
                    </div>
                    <div class="wl-balance__actions">
                        <button type="button" class="wl-balance__btn" id="wallet_charging_cta">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            {{ get_phrase('شحن الآن') }}
                        </button>
                    </div>
                    <div class="wl-balance__meta">
                        <div class="wl-balance__stat">
                            <small>{{ get_phrase('إجمالي الشحن') }}</small>
                            <strong>{{ number_format($stats['credits'] ?? 0, 0) }}</strong>
                        </div>
                        <div class="wl-balance__stat">
                            <small>{{ get_phrase('إجمالي الخصم') }}</small>
                            <strong>{{ number_format($stats['debits'] ?? 0, 0) }}</strong>
                        </div>
                        <div class="wl-balance__stat">
                            <small>{{ get_phrase('عمليات معلّقة') }}</small>
                            <strong>{{ $stats['pending'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>

                {{-- سجل المعاملات --}}
                <div class="wallet-transaction">
                    <div class="my-panel purchase-history-panel wl-panel">
                        <h2 class="wl-section-title">{{ get_phrase('سجل المعاملات') }}</h2>

                        @if ($user_wallets->count() > 0)
                            <div class="wl-list">
                                @foreach ($user_wallets as $log)
                                    @php
                                        $isDebit = $log->type === 'decreased';
                                        $isDone = $log->status == true;
                                        $byLabel = $log->student_id == $log->added_by
                                            ? get_phrase('الطالب')
                                            : ($log->added->name ?? get_phrase('النظام'));
                                    @endphp

                                    <article class="wl-card">
                                        <div class="wl-card__method {{ $isDebit ? 'wl-card__method--icon down' : '' }}">
                                            @if ($isDebit)
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <line x1="12" y1="5" x2="12" y2="19" />
                                                    <polyline points="19 12 12 19 5 12" />
                                                </svg>
                                            @else
                                                <img src="{{ get_image('assets/payment/' . $log->type . '.png') }}"
                                                    alt="{{ $log->type }}" loading="lazy">
                                            @endif
                                        </div>

                                        <div class="wl-card__body">
                                            <div class="wl-card__top">
                                                <h3 class="wl-card__title">
                                                    @if ($isDebit)
                                                        {{ get_phrase('خصم من المحفظة') }}
                                                    @else
                                                        {{ get_phrase('شحن محفظة') }}
                                                        @if ($log->type)
                                                            <span style="font-weight:600;color:#64748b;font-size:.86rem;">
                                                                · {{ ucfirst($log->type) }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </h3>

                                                @if ($isDone && !$isDebit)
                                                    <span class="wl-badge wl-badge--paid">{{ get_phrase('تم الدفع') }}</span>
                                                @elseif ($isDone && $isDebit)
                                                    <span class="wl-badge wl-badge--debit">{{ get_phrase('تم الخصم') }}</span>
                                                @else
                                                    <span class="wl-badge wl-badge--pending">{{ get_phrase('قيد الانتظار') }}</span>
                                                @endif
                                            </div>

                                            <div class="wl-card__meta">
                                                <span class="wl-card__meta-item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" aria-hidden="true">
                                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                        <line x1="16" y1="2" x2="16" y2="6" />
                                                        <line x1="8" y1="2" x2="8" y2="6" />
                                                        <line x1="3" y1="10" x2="21" y2="10" />
                                                    </svg>
                                                    {{ date('Y-m-d', strtotime($log->created_at)) }}
                                                </span>
                                                @if ($log->uuid)
                                                    <span class="wl-card__meta-item" title="{{ $log->uuid }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" aria-hidden="true">
                                                            <path d="M4 7V4h16v3" />
                                                            <path d="M9 20h6" />
                                                            <path d="M12 4v16" />
                                                        </svg>
                                                        {{ \Illuminate\Support\Str::limit($log->uuid, 18) }}
                                                    </span>
                                                @endif
                                                @if ($log->payment_id)
                                                    <span class="wl-card__meta-item">
                                                        #{{ $log->payment_id }}
                                                    </span>
                                                @endif
                                                <span class="wl-card__meta-item">
                                                    {{ get_phrase('بواسطة') }}: {{ $byLabel }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="wl-card__side">
                                            <div class="wl-card__amount {{ $isDebit ? 'is-debit' : 'is-credit' }}">
                                                {{ $isDebit ? '-' : '+' }}{{ number_format($log->balance, 0) }}
                                                <small>{{ currency_symbol() }}</small>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="wl-empty">
                                <div class="wl-empty__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4" />
                                        <path d="M3 5v14a2 2 0 0 0 2 2h16v-5" />
                                        <path d="M18 12a2 2 0 0 0 0 4h4v-4Z" />
                                    </svg>
                                </div>
                                <h2 class="wl-empty__title">{{ get_phrase('لا توجد معاملات بعد') }}</h2>
                                <p class="wl-empty__text">
                                    {{ get_phrase('اشحن محفظتك لتظهر عملياتك هنا.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($user_wallets->count() > 0)
                        <div class="entry-pagination">
                            <nav aria-label="{{ get_phrase('التنقل بين الصفحات') }}">
                                {{ $user_wallets->links() }}
                            </nav>
                        </div>
                    @endif
                </div>

                {{-- شحن المحفظة --}}
                <div class="wallet-charge d-none">
                    <div class="my-panel wl-panel">
                        <h2 class="wl-section-title">{{ get_phrase('شحن المحفظة') }}</h2>

                        <div class="wl-charge">
                            <form class="form-inline" action="#" method="get">
                                @csrf
                                <div class="wl-charge__grid">
                                    <div id="amount-section" class="wl-field">
                                        <label for="balance">{{ get_phrase('المبلغ') }}</label>
                                        <input type="number" class="form-control ol-form-control wl-input"
                                            name="balance" id="balance"
                                            oninput="this.value = Math.max(1, Math.abs(this.value))" required
                                            placeholder="{{ get_phrase('ادخل المبلغ') }}" min="1">
                                        <div class="wl-quick" id="quick-amounts">
                                            @foreach ([50, 100, 200, 500, 1000] as $amount)
                                                <button type="button" class="wl-chip" data-amount="{{ $amount }}">
                                                    {{ number_format($amount) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div id="card-section" class="wl-card-box d-none">
                                        <h5 class="wl-card-box__title">
                                            <i class="fas fa-credit-card"></i>
                                            {{ get_phrase('شحن بواسطة الكارت') }}
                                        </h5>
                                        <div class="wl-field">
                                            <label for="card_code">{{ get_phrase('رقم الكارت') }}</label>
                                            <div class="wl-card-row">
                                                <input type="number" class="form-control ol-form-control wl-input"
                                                    name="card_code" id="card_code"
                                                    placeholder="{{ get_phrase('أدخل رقم الكارت') }}"
                                                    min="1"
                                                    oninput="this.value = Math.max(1, Math.abs(this.value))">
                                                <button type="button" class="wl-btn" id="verify_card_btn">
                                                    {{ get_phrase('تحقق') }}
                                                </button>
                                            </div>
                                        </div>
                                        <div class="alert wl-alert mt-3 d-none" id="card_result"></div>
                                    </div>

                                    <div>
                                        <label class="d-block mb-2" style="font-weight:700;color:#0f172a;font-size:.88rem;">
                                            {{ get_phrase('اختر وسيلة الدفع') }}
                                        </label>
                                        <div class="wl-gateways">
                                            @foreach ($payment_gateways as $payment_gateway)
                                                <div class="wl-gateway payment-option"
                                                    onclick="selectPaymentGateway('{{ $payment_gateway->identifier }}')"
                                                    id="{{ $payment_gateway->identifier }}-tab"
                                                    role="button"
                                                    tabindex="0"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selectPaymentGateway('{{ $payment_gateway->identifier }}');}">
                                                    <img src="{{ get_image('assets/payment/' . $payment_gateway->identifier . '.png') }}"
                                                        alt="{{ $payment_gateway->name ?? $payment_gateway->identifier }}">
                                                    <h6>{{ ucfirst($payment_gateway->name ?? $payment_gateway->identifier) }}</h6>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="wl-gateway-ajax">
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active pb-2" id="showPaymentGatewayByAjax"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script>
    function setWalletTab(mode) {
        const isCharge = mode === 'charge';
        $('.wallet-charge').toggleClass('d-none', !isCharge);
        $('.wallet-transaction').toggleClass('d-none', isCharge);
        $('#wallet_charging').toggleClass('is-active', isCharge).attr('aria-selected', isCharge ? 'true' : 'false');
        $('#wallet_transaction').toggleClass('is-active', !isCharge).attr('aria-selected', isCharge ? 'false' : 'true');
    }

    $('#wallet_charging, #wallet_charging_cta').on('click', function () {
        setWalletTab('charge');
    });

    $('#wallet_transaction').on('click', function () {
        setWalletTab('transactions');
    });

    $('#quick-amounts').on('click', '.wl-chip', function () {
        const amount = $(this).data('amount');
        $('#balance').val(amount);
        $('#quick-amounts .wl-chip').removeClass('is-active');
        $(this).addClass('is-active');
    });

    function selectPaymentGateway(identifier) {
        document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
        const tab = document.getElementById(identifier + '-tab');
        if (tab) tab.classList.add('active');

        if (identifier === 'card') {
            document.getElementById('amount-section').classList.add('d-none');
            document.getElementById('card-section').classList.remove('d-none');
            document.getElementById('showPaymentGatewayByAjax').innerHTML = '';
        } else {
            document.getElementById('amount-section').classList.remove('d-none');
            document.getElementById('card-section').classList.add('d-none');
            showPaymentGatewayByAjax(identifier);
        }
    }

    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'verify_card_btn') {
            let cardCode = document.getElementById('card_code').value.trim();
            if (cardCode === '') {
                Swal.fire('تنبيه', 'من فضلك أدخل رقم الكارت', 'warning');
                return;
            }

            fetch("{{ route('theme.wallet.verify_card') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ card_code: cardCode })
            })
            .then(res => res.json())
            .then(data => {
                let result = document.getElementById('card_result');
                result.classList.remove('d-none', 'alert-success', 'alert-danger');

                if (data.success) {
                    result.classList.add('alert-success');
                    result.innerText = "تم شحن المحفظة بمبلغ " + data.amount + " بنجاح!";
                    setTimeout(function () {
                        window.location.reload();
                    }, 1200);
                } else {
                    result.classList.add('alert-danger');
                    result.innerText = data.message;
                }
            })
            .catch(err => console.error(err));
        }
    });

    function showPaymentGatewayByAjax(identifier) {
        const balance = parseFloat(document.getElementById('balance').value);
        if (isNaN(balance) || balance <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'من فضلك ادخل قيمة الشحن',
                confirmButtonText: 'حسناً'
            });
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
            return;
        }

        $('#showPaymentGatewayByAjax').html(
            '<div class="w-50 mx-auto text-center my-5"><div class="spinner-border" role="status"><span class="visually-hidden"></span></div></div>'
        );

        let urlTemplate = "{{ route('theme.wallet.show_payment_gateway_by_ajax', ['identifier' => 'IDENTIFIER', 'balance' => 'BALANCE']) }}";
        let finalUrl = urlTemplate.replace('IDENTIFIER', identifier).replace('BALANCE', balance);
        $.ajax({
            url: finalUrl,
            success(response) {
                if (identifier == "fawrypay" || identifier == "paymob") {
                    window.open(response.url, "_blank");
                }
                $('#showPaymentGatewayByAjax').html(response);
            }
        });
    }
</script>
@endpush

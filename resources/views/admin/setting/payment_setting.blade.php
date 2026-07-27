@extends('layouts.admin')
@push('title', get_phrase('Payment setting'))
@push('meta')@endpush
@push('css')@endpush
@section('content')
    @php
        $System_currencies = App\Models\Currency::get();
        $allowedGateways = ['fawrypay', 'paymob', 'Wallet', 'paypal', 'stripe'];
        $gatewayMeta = [
            'fawrypay' => [
                'label' => 'Fawry',
                'logo'  => 'fawrypay.png',
            ],
            'paymob' => [
                'label' => 'Paymob',
                'logo'  => 'paymob.png',
            ],
            'Wallet' => [
                'label' => get_phrase('Paymob Wallet'),
                'logo'  => 'paymob.png',
            ],
            'paypal' => [
                'label' => 'PayPal',
                'logo'  => 'paypal.png',
            ],
            'stripe' => [
                'label' => 'Stripe',
                'logo'  => 'stripe.png',
            ],
        ];

        $payment_gateways = App\Models\Payment_gateway::whereIn('identifier', $allowedGateways)
            ->get()
            ->sortBy(function ($gateway) use ($allowedGateways) {
                return array_search($gateway->identifier, $allowedGateways, true);
            })
            ->values();

        $activeTab = request('tab');
        $showCurrency = $activeTab === 'currency' || (!$activeTab && $payment_gateways->isEmpty());
        if (!$activeTab && !$showCurrency && $payment_gateways->isNotEmpty()) {
            $activeTab = $payment_gateways->first()->identifier;
        }
    @endphp

    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-credit-card"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Payment Settings') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Configure payment gateways') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                <button type="button"
                    class="admin-btn admin-btn--ghost {{ $showCurrency ? 'is-active' : '' }}"
                    id="v-pills-currency-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#v-pills-currency"
                    role="tab"
                    aria-controls="v-pills-currency"
                    aria-selected="{{ $showCurrency ? 'true' : 'false' }}">
                    <span class="fi-rr-dollar"></span>
                    <span>{{ get_phrase('Currency Settings') }}</span>
                </button>
            </div>
        </div>

        <div class="ol-card payment-settings-card">
            <div class="ol-card-body p-4">
                <div class="d-flex gap-3 flex-wrap flex-md-nowrap">
                    <div class="ol-sidebar-tab">
                        <div class="nav flex-column nav-pills payment-gateway-nav" id="myv-pills-tab" role="tablist" aria-orientation="vertical">
                            @foreach ($payment_gateways as $payment_gateway)
                                @php
                                    $meta = $gatewayMeta[$payment_gateway->identifier] ?? null;
                                    $label = $meta['label'] ?? $payment_gateway->title;
                                    $logo = $meta['logo'] ?? ($payment_gateway->identifier . '.png');
                                    $isActive = !$showCurrency && $activeTab === $payment_gateway->identifier;
                                @endphp
                                <button
                                    class="nav-link payment-gateway-tab {{ $isActive ? 'active' : '' }}"
                                    id="v-pills-{{ $payment_gateway->identifier }}-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#v-pills-{{ $payment_gateway->identifier }}"
                                    type="button"
                                    role="tab"
                                    aria-controls="v-pills-{{ $payment_gateway->identifier }}"
                                    aria-selected="{{ $isActive ? 'true' : 'false' }}">
                                    <img
                                        class="payment-gateway-tab__logo"
                                        src="{{ asset('assets/payment/' . $logo) }}"
                                        alt="{{ $label }}"
                                        loading="lazy"
                                        onerror="this.style.display='none'">
                                    <span>{{ $label }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="tab-content w-100" id="myv-pills-tabContent">
                        <div class="tab-pane fade {{ $showCurrency ? 'show active' : '' }}" id="v-pills-currency" role="tabpanel" aria-labelledby="v-pills-currency-tab" tabindex="0">
                            <h3 class="title text-14px mb-3">{{ get_phrase('Currency settings') }}</h3>

                            <div class="alert alert-primary ol-alert-primary mb-3" role="alert">
                                <p class="sub-title2 fs-16px">
                                    <span class="title2">{{ get_phrase('Heads up !!') }}</span>
                                    {{ get_phrase('Ensure that the system currency and all active payment gateway currencies are same') }}
                                </p>
                            </div>

                            <div class="ol-card-body">
                                <form action="{{ route('admin.payment.settings.update') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="top_part" value="top_part">

                                    <div class="fpb-7 mb-3">
                                        <label class="form-label ol-form-label">{{ get_phrase('Select currency') }}</label>
                                        <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="system_currency" required>
                                            <option value="">{{ get_phrase('Select currency') }}</option>
                                            @foreach ($System_currencies as $row)
                                                <option value="{{ $row->code }}" @if (get_settings('system_currency') == $row->code) selected @endif>{{ $row->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="fpb-7 mb-3">
                                        <label class="form-label ol-form-label">{{ get_phrase('Currency position') }}</label>
                                        <select class="form-control ol-form-control ol-select2" data-toggle="select2" id="currency_position" name="currency_position" required>
                                            <option value="left" @if (get_settings('currency_position') == 'left') selected @endif>{{ get_phrase('Left') }}</option>
                                            <option value="right" @if (get_settings('currency_position') == 'right') selected @endif>{{ get_phrase('Right') }}</option>
                                            <option value="left-space" @if (get_settings('currency_position') == 'left-space') selected @endif>{{ get_phrase('Left with a space') }}</option>
                                            <option value="right-space" @if (get_settings('currency_position') == 'right-space') selected @endif>{{ get_phrase('Right with a space') }}</option>
                                        </select>
                                    </div>

                                    <div class="fpb-7 mb-3">
                                        <button type="submit" class="btn ol-btn-primary mt-3">{{ get_phrase('Update') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @foreach ($payment_gateways as $payment_gateway)
                            @php
                                $meta = $gatewayMeta[$payment_gateway->identifier] ?? null;
                                $label = $meta['label'] ?? $payment_gateway->title;
                                $isActive = !$showCurrency && $activeTab === $payment_gateway->identifier;
                            @endphp
                            <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}" id="v-pills-{{ $payment_gateway->identifier }}" role="tabpanel" aria-labelledby="v-pills-{{ $payment_gateway->identifier }}-tab" tabindex="0">
                                <div class="payment-gateway-pane__head">
                                    <img
                                        class="payment-gateway-pane__logo"
                                        src="{{ asset('assets/payment/' . ($meta['logo'] ?? ($payment_gateway->identifier . '.png'))) }}"
                                        alt="{{ $label }}"
                                        loading="lazy"
                                        onerror="this.style.display='none'">
                                    <div>
                                        <h3 class="title text-14px mb-0">{{ $label }} {{ get_phrase('settings') }}</h3>
                                        <p class="payment-gateway-pane__hint">{{ get_phrase('Update credentials and activation status') }}</p>
                                    </div>
                                </div>

                                <div class="ol-card-body">
                                    <form action="{{ route('admin.payment.settings.update') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="identifier" value="{{ $payment_gateway->identifier }}">

                                        @if ($payment_gateway->identifier != 'offline')
                                            <div class="payment-switch-grid mb-3">
                                                <div class="payment-switch-card">
                                                    <div class="payment-switch-card__text">
                                                        <strong>{{ get_phrase('Active') }}</strong>
                                                        <small>{{ get_phrase('Enable this payment gateway for customers') }}</small>
                                                    </div>
                                                    <div class="form-check form-switch payment-switch mb-0">
                                                        <input type="hidden" name="status" value="0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="status-{{ $payment_gateway->identifier }}"
                                                            name="status" value="1"
                                                            @checked((int) $payment_gateway->status === 1)>
                                                        <label class="form-check-label visually-hidden" for="status-{{ $payment_gateway->identifier }}">
                                                            {{ get_phrase('Active') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="payment-switch-card">
                                                    <div class="payment-switch-card__text">
                                                        <strong>{{ get_phrase('Test mode') }}</strong>
                                                        <small>{{ get_phrase('Use sandbox credentials while testing') }}</small>
                                                    </div>
                                                    <div class="form-check form-switch payment-switch mb-0">
                                                        <input type="hidden" name="test_mode" value="0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="test-mode-{{ $payment_gateway->identifier }}"
                                                            name="test_mode" value="1"
                                                            @checked((int) $payment_gateway->test_mode === 1)>
                                                        <label class="form-check-label visually-hidden" for="test-mode-{{ $payment_gateway->identifier }}">
                                                            {{ get_phrase('Test mode') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="fpb-7 mb-3">
                                                <label class="mb-2 text-capitalize">{{ get_phrase('Select currency') }}</label>
                                                <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="currency" required>
                                                    <option value="">{{ get_phrase('Select currency') }}</option>
                                                    @foreach ($System_currencies as $currency)
                                                        <option value="{{ $currency->code }}" @if ($payment_gateway->currency == $currency->code) selected @endif>{{ $currency->code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            @foreach (json_decode($payment_gateway['keys'], true) ?? [] as $key => $value)
                                                @if ($key == 'theme_color')
                                                    <label class="mb-2 text-capitalize">{{ get_phrase(str_replace('_', ' ', $key)) }}</label>
                                                    <input type="color" name="{{ $key }}" class="form-control ol-form-control" value="{{ $value }}" required />
                                                @else
                                                    <div class="fpb-7 mb-3">
                                                        <label class="mb-2 text-capitalize">{{ get_phrase(str_replace('_', ' ', $key)) }}</label>
                                                        <input type="text" name="{{ $key }}" class="form-control ol-form-control" value="{{ $value }}" required />
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="fpb-7 mb-3 col-md-12">
                                                <select name="status" id="status" class="form-control ol-form-control ol-select2" data-toggle="select2">
                                                    <option value="">{{ get_phrase('Choose an option') }}</option>
                                                    <option value="1" @if ($payment_gateway->status) selected @endif>{{ get_phrase('Active') }}</option>
                                                    <option value="0" @if (!$payment_gateway->status) selected @endif>{{ get_phrase('Inactive') }}</option>
                                                </select>
                                            </div>

                                            @foreach (json_decode($payment_gateway['keys'], true) ?? [] as $key => $value)
                                                <div class="fpb-7 mb-3">
                                                    <label class="mb-2 text-capitalize">{{ get_phrase(str_replace('_', ' ', $key)) }}</label>
                                                    <textarea name="{{ $key }}" rows="5" class="form-control ol-form-control" required>{{ $value }}</textarea>
                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="row">
                                            <div class="fpb-7 mb-3 col-md-6">
                                                <button class="btn btn-block ol-btn-primary" type="submit">
                                                    {{ get_phrase('Update') }} {{ $label }} {{ get_phrase('setting') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    "use strict";

    (function () {
        var currencyBtn = document.getElementById('v-pills-currency-tab');
        var gatewayTabs = document.querySelectorAll('.payment-gateway-tab');
        var currencyPane = document.getElementById('v-pills-currency');

        function setCurrencyActive(active) {
            if (!currencyBtn) return;
            currencyBtn.classList.toggle('is-active', active);
            currencyBtn.setAttribute('aria-selected', active ? 'true' : 'false');
        }

        function deactivateGatewayTabs() {
            gatewayTabs.forEach(function (tab) {
                tab.classList.remove('active');
                tab.setAttribute('aria-selected', 'false');
            });
            document.querySelectorAll('#myv-pills-tabContent > .tab-pane').forEach(function (pane) {
                if (pane.id === 'v-pills-currency') return;
                pane.classList.remove('show', 'active');
            });
        }

        if (currencyBtn && currencyPane) {
            currencyBtn.addEventListener('click', function (e) {
                e.preventDefault();
                deactivateGatewayTabs();
                currencyPane.classList.add('show', 'active');
                setCurrencyActive(true);
            });
        }

        gatewayTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                if (currencyPane) {
                    currencyPane.classList.remove('show', 'active');
                }
                setCurrencyActive(false);
            });
        });
    })();
</script>
@endpush

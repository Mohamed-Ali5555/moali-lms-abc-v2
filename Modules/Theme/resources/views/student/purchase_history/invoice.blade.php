@extends('theme::layouts.master')

@push('title', get_phrase('Invoice'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/purchase-history-modern.css') }}">
@endpush

@section('content')
    @php
        $invoiceNo = str_pad($report_historys->invoice ?? $report_historys->id, 5, '0', STR_PAD_LEFT);
        $isPaid = ($report_historys->status ?? '') === 'paid';
        $isFailed = ($report_historys->status ?? '') === 'failed';
        $subtotal = 0;
        $totalQuantity = 0;
    @endphp

    <section class="wishlist-content main_content ph-page" dir="rtl">
        <div class="profile-banner-area"></div>
        <div class="container profile-banner-area-container">
            <div class="row">
                @include('theme::student.left_sidebar')

                <div class="col-lg-9">
                    <div class="ph-header">
                        <div class="ph-header__intro">
                            <div class="ph-header__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="ph-header__title">{{ get_phrase('تفاصيل الفاتورة') }}</h1>
                                <p class="ph-header__sub">
                                    {{ get_phrase('فاتورة رقم') }} #{{ $invoiceNo }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="my-panel purchase-history-panel ph-panel">
                        <div class="ph-invoice-doc" id="invoice">
                            <div class="ph-invoice-doc__banner">
                                <div class="ph-invoice-doc__banner-row">
                                    <div>
                                        <span class="ph-invoice-doc__label">{{ get_phrase('Invoice') }}</span>
                                        <h2 class="ph-invoice-doc__number">#{{ $invoiceNo }}</h2>
                                        <p class="ph-invoice-doc__date">
                                            {{ get_phrase('Date') }}:
                                            {{ date('d-m-Y', strtotime($report_historys->created_at)) }}
                                        </p>
                                    </div>
                                    <div class="ph-invoice-doc__logo">
                                        <img src="{{ get_image(get_theme_settings('logo') ?? '') }}"
                                            class="logo light" alt="{{ get_phrase('system logo') }}" width="160">
                                        <img src="{{ get_image(get_theme_settings('dark_logo') ?? '') }}"
                                            class="logo dark" alt="{{ get_phrase('system logo') }}" width="160">
                                    </div>
                                </div>
                            </div>

                            <div class="ph-invoice-doc__body">
                                <div class="ph-invoice-meta">
                                    <div class="ph-invoice-meta__box">
                                        <h6>{{ get_phrase('Billed to') }}</h6>
                                        <p>{{ auth()->user()->name }}</p>
                                        @if (auth()->user()->email)
                                            <p>{{ auth()->user()->email }}</p>
                                        @endif
                                    </div>
                                    <div class="ph-invoice-meta__box">
                                        <h6>{{ get_phrase('الحالة') }}</h6>
                                        <p>
                                            @if ($isPaid)
                                                {{ get_phrase('تم الدفع') }}
                                            @elseif ($isFailed)
                                                {{ get_phrase('فشل الدفع') }}
                                            @else
                                                {{ get_phrase('قيد الانتظار') }}
                                            @endif
                                        </p>
                                        @if ($report_historys->payment_type)
                                            <p>{{ get_phrase('طريقة الدفع') }}: {{ $report_historys->payment_type }}</p>
                                        @endif
                                        @if ($report_historys->transaction_id)
                                            <p>{{ get_phrase('رقم العملية') }}: {{ $report_historys->transaction_id }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="ph-invoice-table">
                                        <thead>
                                            <tr>
                                                <th>{{ get_phrase('Item') }}</th>
                                                <th>{{ get_phrase('Qty') }}</th>
                                                <th>{{ get_phrase('Price') }}</th>
                                                <th>{{ get_phrase('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoice_items as $invoice)
                                                @php
                                                    $itemPrice =
                                                        ($invoice->item->discount_price ?? 0) > 0
                                                            ? $invoice->item->discount_price
                                                            : ($invoice->item->price ?? 0);
                                                    $itemTotal = $invoice->qty * $itemPrice;
                                                    $subtotal += $itemTotal;
                                                    $totalQuantity += $invoice->qty;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <p class="item-title">{{ $invoice->item->title ?? 'N/A' }}</p>
                                                    </td>
                                                    <td width="90">{{ $invoice->qty }}</td>
                                                    <td width="120">{{ currency($itemPrice, 2) }}</td>
                                                    <td width="120">{{ currency($itemTotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @php
                                    $couponDiscount = $subtotal - $report_historys->amount;
                                    $finalTotal = $report_historys->amount;
                                @endphp

                                <div class="ph-invoice-totals">
                                    <div class="ph-invoice-totals__row">
                                        <span>{{ get_phrase('Subtotal') }}</span>
                                        <strong>{{ currency($subtotal, 2) }}</strong>
                                    </div>
                                    @if ($couponDiscount > 0)
                                        <div class="ph-invoice-totals__row ph-invoice-totals__row--discount">
                                            <span>{{ get_phrase('Coupon Discount') }}</span>
                                            <strong>-{{ currency($couponDiscount, 2) }}</strong>
                                        </div>
                                    @endif
                                    <div class="ph-invoice-totals__row ph-invoice-totals__row--final">
                                        <span>{{ get_phrase('Final Total') }}</span>
                                        <strong>{{ currency($finalTotal, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ph-actions">
                        <a class="ph-btn ph-btn--ghost" href="{{ route('theme.purchase.history') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                            {{ get_phrase('Back') }}
                        </a>
                        <button type="button" class="ph-btn ph-btn--primary" id="print"
                            onclick="printableDiv('invoice')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <polyline points="6 9 6 2 18 2 18 9" />
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                <rect x="6" y="14" width="12" height="8" />
                            </svg>
                            {{ get_phrase('Print') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        function printableDiv(printableAreaDivId) {
            const originalContents = document.body.innerHTML;
            const printContents = document.getElementById(printableAreaDivId).innerHTML;

            document.body.innerHTML = `
                <html>
                    <head>
                        <title>{{ get_phrase('Invoice') }} #{{ $invoiceNo }}</title>
                        <style>
                            body { font-family: Tahoma, Arial, sans-serif; margin: 24px; color: #0f172a; direction: rtl; }
                            .ph-invoice-doc__banner {
                                padding: 20px;
                                color: #fff;
                                background: linear-gradient(120deg, #334155, #0d9488);
                                border-radius: 12px;
                                margin-bottom: 20px;
                            }
                            .ph-invoice-doc__banner-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; }
                            .ph-invoice-doc__number { margin: 4px 0; font-size: 28px; }
                            .ph-invoice-doc__date { margin: 0; opacity: .9; }
                            .ph-invoice-doc__logo img { max-width: 140px; }
                            .ph-invoice-doc__logo .logo.dark { display: none; }
                            .ph-invoice-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px; }
                            .ph-invoice-meta__box { border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; }
                            .ph-invoice-meta__box h6 { margin: 0 0 6px; color: #64748b; font-size: 12px; }
                            .ph-invoice-meta__box p { margin: 0; }
                            .ph-invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
                            .ph-invoice-table th, .ph-invoice-table td { border: 1px solid #e2e8f0; padding: 10px; text-align: right; }
                            .ph-invoice-table th { background: #f1f5f9; }
                            .ph-invoice-totals { width: 320px; margin-right: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
                            .ph-invoice-totals__row { display: flex; justify-content: space-between; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
                            .ph-invoice-totals__row:last-child { border-bottom: none; font-weight: 700; background: #f0fdfa; }
                        </style>
                    </head>
                    <body>${printContents}</body>
                </html>
            `;

            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
@endpush

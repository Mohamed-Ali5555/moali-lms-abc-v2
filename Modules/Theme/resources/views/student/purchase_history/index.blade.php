@extends('theme::layouts.master')

@push('title', get_phrase('Purchase History'))
@push('meta')@endpush
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/purchase-history-modern.css') }}">
@endpush

@section('content')
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
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="ph-header__title">{{ get_phrase('سجل المدفوعات') }}</h1>
                                <p class="ph-header__sub">
                                    {{ get_phrase('اطّلع على فواتيرك ومعاملاتك السابقة، وحمّل أو اطبع أي فاتورة بسهولة.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="my-panel purchase-history-panel ph-panel">
                        @if ($payments->count() > 0)
                            <div class="ph-stats">
                                <div class="ph-stat">
                                    <span class="ph-stat__label">{{ get_phrase('إجمالي الفواتير') }}</span>
                                    <span class="ph-stat__value">{{ $stats['total'] ?? $payments->total() }}</span>
                                </div>
                                <div class="ph-stat ph-stat--success">
                                    <span class="ph-stat__label">{{ get_phrase('مدفوعة') }}</span>
                                    <span class="ph-stat__value">{{ $stats['paid'] ?? 0 }}</span>
                                </div>
                                <div class="ph-stat ph-stat--warn">
                                    <span class="ph-stat__label">{{ get_phrase('قيد الانتظار') }}</span>
                                    <span class="ph-stat__value">{{ $stats['pending'] ?? 0 }}</span>
                                </div>
                                <div class="ph-stat ph-stat--accent">
                                    <span class="ph-stat__label">{{ get_phrase('إجمالي المدفوع') }}</span>
                                    <span class="ph-stat__value">{{ currency($stats['amount'] ?? 0) }}</span>
                                </div>
                            </div>

                            <div class="ph-list">
                                @foreach ($payments as $payment)
                                    @php
                                        $invoiceNo = str_pad($payment->invoice ?? $payment->id, 5, '0', STR_PAD_LEFT);
                                        $itemTitles = $payment->items
                                            ->map(fn($row) => $row->item->title ?? null)
                                            ->filter()
                                            ->take(3)
                                            ->implode(' · ');
                                        $moreItems = max(0, $payment->items->count() - 3);
                                        $isPaid = $payment->status === 'paid';
                                        $isFailed = $payment->status === 'failed';
                                    @endphp

                                    <article class="ph-card">
                                        <div class="ph-card__method">
                                            <img src="{{ get_image('assets/payment/' . $payment->payment_type . '.png') }}"
                                                alt="{{ $payment->payment_type }}"
                                                loading="lazy">
                                        </div>

                                        <div class="ph-card__body">
                                            <div class="ph-card__top">
                                                <span class="ph-invoice-no">#{{ $invoiceNo }}</span>
                                                @if ($isPaid)
                                                    <span class="ph-badge ph-badge--paid">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2.5"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            aria-hidden="true">
                                                            <polyline points="20 6 9 17 4 12" />
                                                        </svg>
                                                        {{ get_phrase('تم الدفع') }}
                                                    </span>
                                                @elseif ($isFailed)
                                                    <span class="ph-badge ph-badge--failed">
                                                        {{ get_phrase('فشل الدفع') }}
                                                    </span>
                                                @else
                                                    <span class="ph-badge ph-badge--pending">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2.5"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            aria-hidden="true">
                                                            <circle cx="12" cy="12" r="10" />
                                                            <polyline points="12 6 12 12 16 14" />
                                                        </svg>
                                                        {{ get_phrase('قيد الانتظار') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="ph-card__meta">
                                                <span class="ph-card__meta-item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                        <line x1="16" y1="2" x2="16" y2="6" />
                                                        <line x1="8" y1="2" x2="8" y2="6" />
                                                        <line x1="3" y1="10" x2="21" y2="10" />
                                                    </svg>
                                                    {{ date('Y-m-d', strtotime($payment->created_at)) }}
                                                </span>
                                                @if ($payment->transaction_id)
                                                    <span class="ph-card__meta-item">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            aria-hidden="true">
                                                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                                                        </svg>
                                                        {{ $payment->transaction_id }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($itemTitles)
                                                <p class="ph-card__items">
                                                    {{ $itemTitles }}{{ $moreItems > 0 ? ' +' . $moreItems : '' }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="ph-card__side">
                                            <div class="ph-card__amount">{{ currency($payment->amount) }}</div>
                                            <a href="{{ route('theme.invoice', $payment->id) }}"
                                                class="ph-card__action"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="{{ get_phrase('عرض الفاتورة') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                    <polyline points="14 2 14 8 20 8" />
                                                    <line x1="16" y1="13" x2="8" y2="13" />
                                                    <line x1="16" y1="17" x2="8" y2="17" />
                                                </svg>
                                                {{ get_phrase('الفاتورة') }}
                                            </a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="ph-empty">
                                <div class="ph-empty__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                    </svg>
                                </div>
                                <h2 class="ph-empty__title">{{ get_phrase('لا توجد فواتير بعد') }}</h2>
                                <p class="ph-empty__text">
                                    {{ get_phrase('عندما تقوم بعملية شراء ستظهر فواتيرك هنا.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($payments->count() > 0)
                        <div class="entry-pagination">
                            <nav aria-label="{{ get_phrase('التنقل بين الصفحات') }}">
                                {{ $payments->links() }}
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js')@endpush

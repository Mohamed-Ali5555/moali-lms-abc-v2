@extends('layouts.admin')

@push('title', get_phrase('مميزات الصفحة الرئيسية'))

@php
    $activeCount = $features->where('status', 1)->count();
    $inactiveCount = $features->count() - $activeCount;
@endphp

@section('content')
<style>
    .ft-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    @media (max-width: 767.98px) {
        .ft-stats { grid-template-columns: 1fr; }
    }

    .ft-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .ft-stat__icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
    }

    .ft-stat__icon--all {
        background: rgba(13, 148, 136, 0.12);
        color: #0f766e;
    }

    .ft-stat__icon--on {
        background: rgba(16, 185, 129, 0.14);
        color: #059669;
    }

    .ft-stat__icon--off {
        background: rgba(148, 163, 184, 0.18);
        color: #64748b;
    }

    .ft-stat__label {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }

    .ft-stat__value {
        margin: 2px 0 0;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .ft-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 16px;
    }

    @media (min-width: 576px) {
        .ft-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (min-width: 992px) {
        .ft-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (min-width: 1400px) {
        .ft-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    .ft-card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-height: 210px;
        padding: 22px 20px 16px;
        border-radius: 20px;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(13, 148, 136, 0.1), transparent 55%),
            linear-gradient(160deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .ft-card:hover {
        transform: translateY(-3px);
        border-color: #cbd5e1;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .ft-card.is-off {
        opacity: 0.78;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(148, 163, 184, 0.12), transparent 55%),
            linear-gradient(160deg, #fff 0%, #f1f5f9 100%);
    }

    .ft-card__orb {
        position: absolute;
        inset-inline-end: -18px;
        top: -18px;
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(13, 148, 136, 0.16), transparent 70%);
        pointer-events: none;
    }

    .ft-card__top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        position: relative;
        z-index: 1;
    }

    .ft-card__num {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 800;
        color: #0f766e;
        background: rgba(13, 148, 136, 0.12);
        border: 1px solid rgba(13, 148, 136, 0.18);
    }

    .ft-card__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ft-card__badge--on {
        background: #dcfce7;
        color: #166534;
    }

    .ft-card__badge--off {
        background: #f1f5f9;
        color: #64748b;
    }

    .ft-card__title {
        margin: 0 0 auto;
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.55;
        position: relative;
        z-index: 1;
    }

    .ft-card__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 20px;
        padding-top: 14px;
        border-top: 1px dashed #e2e8f0;
        position: relative;
        z-index: 1;
    }

    .ft-card__hint {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
    }

    .ft-actions {
        display: inline-flex;
        gap: 8px;
    }

    .ft-action {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        text-decoration: none !important;
        transition: .18s ease;
    }

    .ft-action--toggle {
        background: rgba(13, 148, 136, 0.12);
        color: #0f766e;
    }

    .ft-action--toggle:hover {
        background: rgba(13, 148, 136, 0.2);
        color: #115e59;
    }

    .ft-action--delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .ft-action--delete:hover {
        background: #fecaca;
        color: #b91c1c;
    }

    .ft-empty {
        padding: 48px 24px;
        text-align: center;
        border-radius: 20px;
        background: #fff;
        border: 1px dashed #cbd5e1;
    }

    .ft-empty__icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 14px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(13, 148, 136, 0.1);
        color: #0f766e;
        font-size: 26px;
    }

    .ft-empty h3 {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .ft-empty p {
        margin: 0 0 18px;
        color: #64748b;
        font-size: 14px;
    }
</style>

<div class="admin-page" dir="rtl">
    <div class="admin-toolbar">
        <div class="admin-toolbar__meta">
            <span class="admin-toolbar__icon">
                <i class="fi-rr-star"></i>
            </span>
            <div>
                <h1 class="admin-toolbar__title">
                    {{ get_phrase('مميزات الصفحة الرئيسية') }}
                    <span class="admin-toolbar__count">{{ $features->count() }}</span>
                </h1>
                <p class="admin-toolbar__desc">
                    {{ get_phrase('إدارة بطاقات المميزات التي تظهر في سكشن «رحلتك نحو التفوق»') }}
                </p>
            </div>
        </div>
        <div class="admin-toolbar__actions">
            @if (has_permission('theme.feature.create') || has_permission('admin.theme.feature.create'))
                <a href="{{ route('admin.theme.feature.create') }}" class="admin-btn admin-btn--primary">
                    <span class="fi-rr-plus"></span>
                    <span>{{ get_phrase('إضافة ميزة') }}</span>
                </a>
            @endif
        </div>
    </div>

    <div class="ft-stats">
        <div class="ft-stat">
            <span class="ft-stat__icon ft-stat__icon--all"><i class="fi-rr-apps"></i></span>
            <div>
                <p class="ft-stat__label">{{ get_phrase('الإجمالي') }}</p>
                <p class="ft-stat__value">{{ $features->count() }}</p>
            </div>
        </div>
        <div class="ft-stat">
            <span class="ft-stat__icon ft-stat__icon--on"><i class="fi-rr-eye"></i></span>
            <div>
                <p class="ft-stat__label">{{ get_phrase('مفعّلة') }}</p>
                <p class="ft-stat__value">{{ $activeCount }}</p>
            </div>
        </div>
        <div class="ft-stat">
            <span class="ft-stat__icon ft-stat__icon--off"><i class="fi-rr-eye-crossed"></i></span>
            <div>
                <p class="ft-stat__label">{{ get_phrase('موقوفة') }}</p>
                <p class="ft-stat__value">{{ $inactiveCount }}</p>
            </div>
        </div>
    </div>

    @if ($features->count() > 0)
        <div class="ft-grid">
            @foreach ($features as $key => $row)
                @php $isActive = (int) $row->status === 1; @endphp
                <article class="ft-card {{ $isActive ? '' : 'is-off' }}">
                    <span class="ft-card__orb" aria-hidden="true"></span>

                    <div class="ft-card__top">
                        <span class="ft-card__num">{{ str_pad((string) ($key + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        @if ($isActive)
                            <span class="ft-card__badge ft-card__badge--on">
                                <span class="admin-status-dot admin-status-dot--on"></span>
                                {{ get_phrase('مفعّلة') }}
                            </span>
                        @else
                            <span class="ft-card__badge ft-card__badge--off">
                                <span class="admin-status-dot admin-status-dot--off"></span>
                                {{ get_phrase('موقوفة') }}
                            </span>
                        @endif
                    </div>

                    <h3 class="ft-card__title">{{ $row->title ?? '' }}</h3>

                    <div class="ft-card__footer">
                        <span class="ft-card__hint">{{ get_phrase('تظهر في الصفحة الرئيسية') }}</span>
                        <div class="ft-actions">
                            @if (has_permission('admin.theme.feature.status') || has_permission('theme.feature.status'))
                                <a href="#"
                                    onclick="confirmModal('{{ route('admin.theme.feature.activeFeature', $row->id) }}'); return false;"
                                    class="ft-action ft-action--toggle"
                                    data-bs-toggle="tooltip"
                                    title="{{ $isActive ? get_phrase('إيقاف') : get_phrase('تفعيل') }}">
                                    <i class="{{ $isActive ? 'fi-rr-eye-crossed' : 'fi-rr-eye' }}"></i>
                                </a>
                            @endif

                            @if (has_permission('admin.theme.feature.delete') || has_permission('theme.feature.delete'))
                                <a href="javascript:void(0)"
                                    onclick="confirmModal('{{ route('admin.theme.feature.delete', $row->id) }}')"
                                    class="ft-action ft-action--delete"
                                    data-bs-toggle="tooltip"
                                    title="{{ get_phrase('حذف') }}">
                                    <i class="fi-rr-trash"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="ft-empty">
            <div class="ft-empty__icon"><i class="fi-rr-star"></i></div>
            <h3>{{ get_phrase('لا توجد مميزات بعد') }}</h3>
            <p>{{ get_phrase('أضف أول ميزة لتظهر في الصفحة الرئيسية') }}</p>
            @if (has_permission('theme.feature.create') || has_permission('admin.theme.feature.create'))
                <a href="{{ route('admin.theme.feature.create') }}" class="admin-btn admin-btn--primary">
                    <span class="fi-rr-plus"></span>
                    <span>{{ get_phrase('إضافة ميزة') }}</span>
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

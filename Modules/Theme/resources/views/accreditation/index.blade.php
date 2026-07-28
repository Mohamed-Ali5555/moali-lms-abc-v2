@extends('theme::layouts.master')

@push('title', get_phrase('الاعتمادية'))

@push('css')
<style>
/* ===== Accreditation Page ===== */
.accred-banner {
    position: relative;
    padding: 80px 0 60px;
    overflow: hidden;
    background: linear-gradient(135deg, rgb(var(--c-primary-rgb, 30 58 138)) 0%, rgb(var(--c-accent-rgb, 14 165 233)) 100%);
    color: #fff;
    text-align: center;
    direction: rtl;
}
.accred-banner__bg {
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.accred-banner__breadcrumb {
    display: flex; align-items: center; justify-content: center;
    gap: 8px; font-size: .875rem; opacity: .85; margin-bottom: 18px;
}
.accred-banner__breadcrumb a { color: #fff; text-decoration: none; }
.accred-banner__breadcrumb a:hover { text-decoration: underline; }
.accred-banner__title {
    font-size: clamp(1.8rem, 5vw, 2.8rem);
    font-weight: 800; margin: 0 0 12px; line-height: 1.2;
}
.accred-banner__sub { font-size: 1.1rem; opacity: .85; margin: 0; }

/* ===== Main Section ===== */
.accred-section { padding: 60px 0 80px; direction: rtl; }

/* ===== TVTC Logo Card ===== */
.accred-logo-card {
    background: #fff;
    border-radius: 20px;
    padding: 40px 32px;
    text-align: center;
    box-shadow: 0 8px 40px rgba(0,0,0,.10);
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;
}
.accred-logo-card::before {
    content: '';
    position: absolute; top: 0; right: 0; left: 0;
    height: 5px;
    background: linear-gradient(90deg, rgb(var(--c-primary-rgb, 30 58 138)), rgb(var(--c-accent-rgb, 14 165 233)));
}
.accred-logo-wrap {
    display: flex; align-items: center; justify-content: center;
    gap: 24px; flex-wrap: wrap; margin-bottom: 20px;
}
.accred-logo-img {
    max-height: 110px; max-width: 260px;
    object-fit: contain;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,.12));
}
.accred-logo-placeholder {
    display: inline-flex; flex-direction: column;
    align-items: center; justify-content: center;
    width: 130px; height: 130px; border-radius: 50%;
    background: linear-gradient(135deg, #0d3b8e 0%, #1a6fc4 100%);
    color: #fff; font-weight: 900; font-size: 1.5rem;
    letter-spacing: 1px; line-height: 1.2;
    box-shadow: 0 4px 20px rgba(13,59,142,.35);
}
.accred-logo-placeholder span { font-size: .7rem; font-weight: 600; margin-top: 4px; letter-spacing: .5px; }
.accred-logo-card__title {
    font-size: 1.25rem; font-weight: 700; color: #1a1a2e; margin: 0 0 6px;
}
.accred-logo-card__subtitle { color: #666; font-size: .95rem; margin: 0; }

/* ===== Info Cards Grid ===== */
.accred-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px; margin-bottom: 40px;
}
.accred-info-item {
    background: #fff; border-radius: 16px;
    padding: 28px 22px; text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    border-bottom: 3px solid rgb(var(--c-accent-rgb, 14 165 233));
    transition: transform .25s, box-shadow .25s;
}
.accred-info-item:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(0,0,0,.12); }
.accred-info-item__icon {
    width: 54px; height: 54px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.3rem; margin-bottom: 14px;
    background: rgba(var(--c-accent-rgb, 14 165 233), .1);
    color: rgb(var(--c-accent-rgb, 14 165 233));
}
.accred-info-item__label { font-size: .8rem; color: #888; margin: 0 0 6px; }
.accred-info-item__value { font-size: 1.05rem; font-weight: 700; color: #1a1a2e; margin: 0; word-break: break-word; }

/* ===== Status Badge ===== */
.accred-status-badge {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 6px 18px; border-radius: 999px;
    background: rgba(16 185 129 / .12);
    color: #059669; font-weight: 700; font-size: .95rem;
}
.accred-status-badge__dot {
    width: 9px; height: 9px; border-radius: 50%;
    background: #10b981; animation: pulse-dot 1.5s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: .5; transform: scale(.7); }
}

/* ===== Description Card ===== */
.accred-desc-card {
    background: #fff; border-radius: 20px;
    padding: 36px 32px; margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
}
.accred-desc-card__header {
    display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
}
.accred-desc-card__icon {
    width: 46px; height: 46px; flex-shrink: 0; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    background: rgba(var(--c-accent-rgb, 14 165 233), .1);
    color: rgb(var(--c-accent-rgb, 14 165 233));
}
.accred-desc-card h2 { font-size: 1.2rem; font-weight: 700; margin: 0; color: #1a1a2e; }
.accred-desc-card__body { color: #555; line-height: 1.85; font-size: .97rem; }

/* ===== Document Section ===== */
.accred-doc-card {
    background: linear-gradient(135deg, rgba(var(--c-primary-rgb, 30 58 138),.04) 0%, rgba(var(--c-accent-rgb, 14 165 233),.06) 100%);
    border: 2px dashed rgba(var(--c-accent-rgb, 14 165 233),.35);
    border-radius: 20px; padding: 40px 24px; text-align: center;
}
.accred-doc-card i { font-size: 3rem; margin-bottom: 14px; color: rgb(var(--c-accent-rgb, 14 165 233)); }
.accred-doc-card h3 { font-size: 1.1rem; font-weight: 700; margin: 0 0 6px; color: #1a1a2e; }
.accred-doc-card p { color: #888; font-size: .9rem; margin: 0 0 18px; }
.accred-doc-card img {
    max-width: 100%; border-radius: 12px;
    box-shadow: 0 6px 24px rgba(0,0,0,.12);
}

/* ===== Map Section ===== */
.accred-map-card {
    background: #fff; border-radius: 20px; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.08); margin-top: 40px;
}
.accred-map-card__header {
    padding: 22px 28px; display: flex; align-items: center; gap: 12px;
    border-bottom: 1px solid #f0f0f0;
}
.accred-map-card__header i { font-size: 1.4rem; color: rgb(var(--c-accent-rgb, 14 165 233)); }
.accred-map-card__header h2 { font-size: 1.1rem; font-weight: 700; margin: 0; color: #1a1a2e; }
.accred-map-card__frame iframe {
    width: 100%; height: 340px; border: none; display: block;
}
.accred-map-card__footer {
    padding: 16px 28px; text-align: center;
}
.accred-map-card__footer a {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 24px; border-radius: 999px; text-decoration: none;
    background: rgb(var(--c-accent-rgb, 14 165 233));
    color: #fff; font-weight: 600; font-size: .9rem;
    transition: opacity .2s;
}
.accred-map-card__footer a:hover { opacity: .85; }

@media (max-width: 576px) {
    .accred-info-grid { grid-template-columns: 1fr 1fr; }
    .accred-logo-card { padding: 28px 16px; }
    .accred-desc-card { padding: 24px 18px; }
}
</style>
@endpush

@section('content')

<section class="accred-banner">
    <div class="accred-banner__bg" aria-hidden="true"></div>
    <div class="container position-relative">
        <nav class="accred-banner__breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('theme.home') }}">{{ get_phrase('الرئيسية') }}</a>
            <span>/</span>
            <span>{{ get_phrase('الاعتمادية') }}</span>
        </nav>
        <h1 class="accred-banner__title">
            <i class="fa-solid fa-award me-2"></i>
            {{ get_phrase('الاعتمادية') }}
        </h1>
        <p class="accred-banner__sub">{{ get_theme_settings('accreditation_subtitle') ?: get_phrase('اعتماد وترخيص المنصة التعليمية') }}</p>
    </div>
</section>

<section class="accred-section">
    <div class="container">

        {{-- TVTC Logo Card --}}
        <div class="accred-logo-card">
            <div class="accred-logo-wrap">
                @if (get_theme_settings('tvtc_logo') && file_exists(public_path(get_theme_settings('tvtc_logo'))))
                    <img src="{{ asset(get_theme_settings('tvtc_logo')) }}" alt="شعار المؤسسة العامة للتدريب التقني والمهني" class="accred-logo-img">
                @else
                    <div class="accred-logo-placeholder">
                        TVTC
                        <span>المؤسسة العامة</span>
                    </div>
                @endif

                @if (get_theme_settings('logo'))
                    <img src="{{ asset(get_theme_settings('logo')) }}" alt="{{ get_theme_settings('name') }}" class="accred-logo-img" style="max-height:80px;">
                @endif
            </div>
            <p class="accred-logo-card__title">{{ get_theme_settings('accreditation_body_title') ?: 'المؤسسة العامة للتدريب التقني والمهني' }}</p>
            <p class="accred-logo-card__subtitle">{{ get_theme_settings('accreditation_body_subtitle') ?: 'Technical and Vocational Training Corporation — TVTC' }}</p>
        </div>

        {{-- Description Card --}}
        <div class="accred-desc-card">
            <div class="accred-desc-card__header">
                <div class="accred-desc-card__icon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <h2>{{ get_phrase('نبذة عن الاعتماد') }}</h2>
            </div>
            <div class="accred-desc-card__body">
                @if (get_theme_settings('accreditation_description'))
                    {!! get_theme_settings('accreditation_description') !!}
                @else
                    <p>
                        تعمل هذه المنصة التعليمية تحت إشراف ومتابعة المؤسسة العامة للتدريب التقني والمهني (TVTC)،
                        وهي الجهة الحكومية المعنية بالإشراف على التدريب التقني والمهني في المملكة العربية السعودية.
                        يُجسّد هذا الاعتماد التزام المنصة بتقديم تدريب مهني معتمد يرتقي إلى أعلى معايير الجودة،
                        ويُسهم في تأهيل الكوادر البشرية وتطوير مهاراتها بما يتوافق مع متطلبات سوق العمل ورؤية المملكة 2030.
                    </p>
                @endif
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="accred-info-grid">
            <div class="accred-info-item">
                <div class="accred-info-item__icon">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <p class="accred-info-item__label">الجهة المانحة للاعتماد</p>
                <p class="accred-info-item__value">
                    {{ get_theme_settings('accreditation_authority') ?: 'المؤسسة العامة للتدريب التقني والمهني' }}
                </p>
            </div>

            <div class="accred-info-item">
                <div class="accred-info-item__icon">
                    <i class="fa-solid fa-hashtag"></i>
                </div>
                <p class="accred-info-item__label">رقم الاعتماد / الترخيص</p>
                <p class="accred-info-item__value" dir="ltr">
                    {{ get_theme_settings('accreditation_number') ?: '—' }}
                </p>
            </div>

            <div class="accred-info-item">
                <div class="accred-info-item__icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <p class="accred-info-item__label">تاريخ الاعتماد</p>
                <p class="accred-info-item__value">
                    {{ get_theme_settings('accreditation_date') ?: '—' }}
                </p>
            </div>

            <div class="accred-info-item">
                <div class="accred-info-item__icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="accred-info-item__label">حالة الاعتماد</p>
                <p class="accred-info-item__value">
                    <span class="accred-status-badge">
                        <span class="accred-status-badge__dot"></span>
                        {{ get_theme_settings('accreditation_status_label') ?: 'ساري / معتمد' }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Accreditation Document --}}
        <div class="accred-doc-card">
            @if (get_theme_settings('accreditation_document') && file_exists(public_path(get_theme_settings('accreditation_document'))))
                <img src="{{ asset(get_theme_settings('accreditation_document')) }}" alt="وثيقة الاعتماد">
            @else
                <i class="fa-solid fa-file-certificate"></i>
                <h3>{{ get_phrase('وثيقة / شهادة الاعتماد') }}</h3>
                <p>{{ get_phrase('سيتم رفع صورة وثيقة الاعتماد هنا قريباً.') }}</p>
            @endif
        </div>

        {{-- Map Section --}}
        @php
            $mapEmbed = get_theme_settings('map_embed_url') ?: '';
            $mapLink  = get_theme_settings('map_link') ?: '#';
        @endphp
        @if($mapEmbed || $mapLink !== '#')
        <div class="accred-map-card">
            <div class="accred-map-card__header">
                <i class="fa-solid fa-location-dot"></i>
                <h2>{{ get_phrase('موقعنا الجغرافي') }}</h2>
            </div>
            @if($mapEmbed)
            <div class="accred-map-card__frame">
                <iframe
                    src="{{ $mapEmbed }}"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="{{ get_phrase('موقعنا على الخريطة') }}">
                </iframe>
            </div>
            @endif
            @if($mapLink !== '#')
            <div class="accred-map-card__footer">
                <a href="{{ $mapLink }}" target="_blank" rel="noopener">
                    <i class="fa-solid fa-map-location-dot"></i>
                    {{ get_phrase('فتح في خرائط جوجل') }}
                </a>
            </div>
            @endif
        </div>
        @endif

    </div>
</section>
@endsection

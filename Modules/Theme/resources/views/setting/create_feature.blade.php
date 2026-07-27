@extends('layouts.admin')

@push('title', get_phrase('إضافة ميزة'))

@section('content')
<style>
    .ft-create {
        max-width: 920px;
    }

    .ft-preview {
        padding: 22px;
        border-radius: 18px;
        background:
            radial-gradient(ellipse at 0% 0%, rgba(13, 148, 136, 0.12), transparent 50%),
            linear-gradient(160deg, #0b1220 0%, #132033 100%);
        border: 1px solid rgba(148, 163, 184, 0.18);
        color: #e2e8f0;
    }

    .ft-preview__label {
        display: block;
        margin-bottom: 14px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #5eead4;
    }

    .ft-preview__card {
        position: relative;
        min-height: 140px;
        padding: 22px 20px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(148, 163, 184, 0.16);
        overflow: hidden;
    }

    .ft-preview__num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        margin-bottom: 14px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 800;
        color: #5eead4;
        background: rgba(94, 234, 212, 0.12);
        border: 1px solid rgba(94, 234, 212, 0.22);
    }

    .ft-preview__title {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #f8fafc;
        line-height: 1.5;
        word-break: break-word;
    }

    .ft-preview__hint {
        margin: 10px 0 0;
        font-size: 12px;
        color: #94a3b8;
    }

    .ft-status-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    @media (max-width: 575.98px) {
        .ft-status-cards { grid-template-columns: 1fr; }
    }

    .ft-status-option {
        margin: 0;
        cursor: pointer;
    }

    .ft-status-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .ft-status-option__card {
        display: flex;
        flex-direction: column;
        gap: 4px;
        height: 100%;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        transition: .18s ease;
    }

    .ft-status-option__card strong {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }

    .ft-status-option__card small {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }

    .ft-status-option input:checked + .ft-status-option__card {
        border-color: #0d9488;
        background: rgba(13, 148, 136, 0.08);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .ft-status-option:hover .ft-status-option__card {
        border-color: #94a3b8;
    }
</style>

<div class="admin-page ft-create" dir="rtl">
    <div class="tf-hero mb-3">
        <div>
            <div class="tf-hero__kicker">
                <i class="fi-rr-star"></i>
                <span>{{ get_phrase('الثيم') }}</span>
            </div>
            <h1 class="tf-hero__title">{{ get_phrase('إضافة ميزة') }}</h1>
            <p class="tf-hero__desc">
                {{ get_phrase('أضف نص الميزة ليظهر ضمن بطاقات الصفحة الرئيسية') }}
            </p>
        </div>
        <div class="tf-hero__actions">
            <a href="{{ route('admin.theme.feature') }}" class="admin-btn admin-btn--ghost">
                <i class="fi-rr-arrow-right"></i>
                {{ get_phrase('رجوع') }}
            </a>
        </div>
    </div>

    <div class="ol-card">
        <div class="ol-card-body p-4">
            <form class="admin-form required-form" action="{{ route('admin.theme.feature.store') }}" method="post">
                @csrf

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="lesson-form-section mb-3">
                            <h6 class="lesson-form-section__title">{{ get_phrase('بيانات الميزة') }}</h6>

                            <div class="mb-3">
                                <label class="form-label ol-form-label" for="title">
                                    {{ get_phrase('عنوان الميزة') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="title"
                                    id="title"
                                    class="form-control ol-form-control"
                                    value="{{ old('title') }}"
                                    placeholder="{{ get_phrase('مثال: محتوى تفاعلي منظم') }}"
                                    maxlength="255"
                                    required>
                                <div class="tf-help mt-1">
                                    {{ get_phrase('يفضّل جملة قصيرة وواضحة تظهر على البطاقة') }}
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label ol-form-label">{{ get_phrase('الحالة') }}</label>
                                <div class="ft-status-cards">
                                    <label class="ft-status-option" for="status_active">
                                        <input type="radio" name="status" id="status_active" value="1"
                                            @checked(old('status', '1') == '1')>
                                        <span class="ft-status-option__card">
                                            <strong>{{ get_phrase('مفعّلة') }}</strong>
                                            <small>{{ get_phrase('تظهر مباشرة في الموقع') }}</small>
                                        </span>
                                    </label>
                                    <label class="ft-status-option" for="status_inactive">
                                        <input type="radio" name="status" id="status_inactive" value="0"
                                            @checked(old('status') === '0')>
                                        <span class="ft-status-option__card">
                                            <strong>{{ get_phrase('موقوفة') }}</strong>
                                            <small>{{ get_phrase('مخفية حتى تفعيلها') }}</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="ft-preview">
                            <span class="ft-preview__label">{{ get_phrase('معاينة البطاقة') }}</span>
                            <div class="ft-preview__card">
                                <span class="ft-preview__num">01</span>
                                <h3 class="ft-preview__title" id="ftPreviewTitle">
                                    {{ old('title') ?: get_phrase('عنوان الميزة سيظهر هنا') }}
                                </h3>
                                <p class="ft-preview__hint">{{ get_phrase('شكل تقريبي لعرضها في الصفحة الرئيسية') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('admin.theme.feature') }}" class="admin-btn admin-btn--ghost">
                        {{ get_phrase('إلغاء') }}
                    </a>
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fi-rr-check"></i>
                        {{ get_phrase('حفظ الميزة') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    (function () {
        var input = document.getElementById('title');
        var preview = document.getElementById('ftPreviewTitle');
        var fallback = @json(get_phrase('عنوان الميزة سيظهر هنا'));

        if (!input || !preview) return;

        input.addEventListener('input', function () {
            var value = (input.value || '').trim();
            preview.textContent = value || fallback;
        });
    })();
</script>
@endpush

@extends('layouts.admin')
@push('title', get_phrase('إضافة حساب تواصل'))

@section('content')
@php
    $socialIcons = [
        ['slug' => 'facebook', 'label' => 'Facebook', 'ar' => 'فيسبوك'],
        ['slug' => 'instagram', 'label' => 'Instagram', 'ar' => 'إنستجرام'],
        ['slug' => 'youtube', 'label' => 'YouTube', 'ar' => 'يوتيوب'],
        ['slug' => 'x-twitter', 'label' => 'X (Twitter)', 'ar' => 'إكس / تويتر'],
        ['slug' => 'linkedin-in', 'label' => 'LinkedIn', 'ar' => 'لينكدإن'],
        ['slug' => 'whatsapp', 'label' => 'WhatsApp', 'ar' => 'واتساب'],
        ['slug' => 'telegram', 'label' => 'Telegram', 'ar' => 'تيليجرام'],
        ['slug' => 'tiktok', 'label' => 'TikTok', 'ar' => 'تيك توك'],
        ['slug' => 'snapchat', 'label' => 'Snapchat', 'ar' => 'سناب شات'],
        ['slug' => 'pinterest', 'label' => 'Pinterest', 'ar' => 'بينتريست'],
        ['slug' => 'discord', 'label' => 'Discord', 'ar' => 'ديسكورد'],
        ['slug' => 'github', 'label' => 'GitHub', 'ar' => 'جيت هب'],
        ['slug' => 'threads', 'label' => 'Threads', 'ar' => 'ثريدز'],
        ['slug' => 'reddit', 'label' => 'Reddit', 'ar' => 'ريديت'],
        ['slug' => 'vimeo', 'label' => 'Vimeo', 'ar' => 'فيميو'],
        ['slug' => 'twitch', 'label' => 'Twitch', 'ar' => 'تويتش'],
        ['slug' => 'spotify', 'label' => 'Spotify', 'ar' => 'سبوتيفاي'],
        ['slug' => 'soundcloud', 'label' => 'SoundCloud', 'ar' => 'ساوند كلاود'],
        ['slug' => 'behance', 'label' => 'Behance', 'ar' => 'بيهانس'],
        ['slug' => 'dribbble', 'label' => 'Dribbble', 'ar' => 'دريبل'],
        ['slug' => 'medium', 'label' => 'Medium', 'ar' => 'ميديوم'],
        ['slug' => 'google', 'label' => 'Google', 'ar' => 'جوجل'],
        ['slug' => 'skype', 'label' => 'Skype', 'ar' => 'سكايب'],
        ['slug' => 'apple', 'label' => 'Apple', 'ar' => 'آبل'],
    ];
    $oldIcon = old('thumbnail', 'facebook');
@endphp

<style>
    .ts-create {
        max-width: 920px;
    }

    .ts-icon-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        max-height: 320px;
        overflow: auto;
        padding: 4px;
    }

    @media (min-width: 576px) {
        .ts-icon-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    @media (min-width: 768px) {
        .ts-icon-grid { grid-template-columns: repeat(6, minmax(0, 1fr)); }
    }

    .ts-icon-option {
        margin: 0;
        cursor: pointer;
    }

    .ts-icon-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .ts-icon-option__card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 78px;
        padding: 10px 8px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #334155;
        transition: .18s ease;
        text-align: center;
    }

    .ts-icon-option__card i {
        font-size: 22px;
        line-height: 1;
    }

    .ts-icon-option__card small {
        font-size: 10px;
        font-weight: 700;
        line-height: 1.2;
        color: #64748b;
    }

    .ts-icon-option input:checked + .ts-icon-option__card {
        border-color: #0d9488;
        background: rgba(13, 148, 136, 0.08);
        color: #0f766e;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .ts-icon-option:hover .ts-icon-option__card {
        border-color: #94a3b8;
    }

    .ts-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .ts-preview__icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #0f766e;
        color: #fff;
        font-size: 22px;
    }
</style>

<div class="admin-page ts-create" dir="rtl">
    <div class="tf-hero mb-3">
        <div>
            <div class="tf-hero__kicker">
                <i class="fi-rr-following"></i>
                <span>{{ get_phrase('الثيم') }}</span>
            </div>
            <h1 class="tf-hero__title">{{ get_phrase('إضافة حساب تواصل') }}</h1>
            <p class="tf-hero__desc">{{ get_phrase('اختر أيقونة السوشيال، ثم أضف الاسم والرابط') }}</p>
        </div>
        <div class="tf-hero__actions">
            <a href="{{ route('admin.theme.social') }}" class="admin-btn admin-btn--ghost">
                <i class="fi-rr-arrow-right"></i>
                {{ get_phrase('رجوع') }}
            </a>
        </div>
    </div>

    <div class="ol-card">
        <div class="ol-card-body p-4">
            <form action="{{ route('admin.theme.social.store') }}" method="post" class="admin-form">
                @csrf

                <div class="lesson-form-section mb-3">
                    <h6 class="lesson-form-section__title">{{ get_phrase('أيقونة السوشيال ميديا') }}</h6>
                    <p class="tf-help mb-3">{{ get_phrase('اختر الأيقونة المناسبة من القائمة') }}</p>

                    <div class="mb-3">
                        <label class="form-label ol-form-label" for="thumbnail">{{ get_phrase('اختيار سريع') }}</label>
                        <select name="thumbnail" id="thumbnail" class="form-control ol-form-control ol-select2" required>
                            @foreach ($socialIcons as $icon)
                                <option value="{{ $icon['slug'] }}"
                                    data-label="{{ $icon['ar'] }}"
                                    @selected($oldIcon === $icon['slug'])>
                                    {{ $icon['ar'] }} — {{ $icon['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ts-icon-grid" id="tsIconGrid">
                        @foreach ($socialIcons as $icon)
                            <label class="ts-icon-option" for="icon_{{ $icon['slug'] }}">
                                <input type="radio" name="icon_picker" id="icon_{{ $icon['slug'] }}"
                                    value="{{ $icon['slug'] }}"
                                    @checked($oldIcon === $icon['slug'])>
                                <span class="ts-icon-option__card">
                                    <i class="fa-brands fa-{{ $icon['slug'] }}"></i>
                                    <small>{{ $icon['ar'] }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="ts-preview mt-3">
                        <div class="ts-preview__icon" id="tsPreviewIcon">
                            <i class="fa-brands fa-{{ $oldIcon }}"></i>
                        </div>
                        <div>
                            <strong id="tsPreviewLabel">{{ collect($socialIcons)->firstWhere('slug', $oldIcon)['ar'] ?? 'فيسبوك' }}</strong>
                            <div class="tf-help mb-0">{{ get_phrase('معاينة الأيقونة المختارة') }}</div>
                        </div>
                    </div>
                </div>

                <div class="lesson-form-section mb-3">
                    <h6 class="lesson-form-section__title">{{ get_phrase('بيانات الحساب') }}</h6>

                    <div class="mb-3">
                        <label class="form-label ol-form-label" for="title">
                            {{ get_phrase('الاسم الظاهر') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="title" class="form-control ol-form-control"
                            value="{{ old('title') }}"
                            placeholder="{{ get_phrase('مثال: فيسبوك') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label ol-form-label" for="url">
                            {{ get_phrase('الرابط') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="url" name="url" id="url" class="form-control ol-form-control"
                            value="{{ old('url') }}"
                            placeholder="https://..." required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label ol-form-label" for="status">{{ get_phrase('الحالة') }}</label>
                        <select class="form-control ol-form-control ol-select2" name="status" id="status" required>
                            <option value="1" @selected(old('status', '1') == '1')>{{ get_phrase('نشط') }}</option>
                            <option value="0" @selected(old('status') === '0')>{{ get_phrase('موقوف') }}</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.theme.social') }}" class="admin-btn admin-btn--ghost">
                        {{ get_phrase('إلغاء') }}
                    </a>
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fi-rr-check"></i>
                        {{ get_phrase('حفظ الحساب') }}
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
        var select = document.getElementById('thumbnail');
        var titleInput = document.getElementById('title');
        var previewIcon = document.querySelector('#tsPreviewIcon i');
        var previewLabel = document.getElementById('tsPreviewLabel');
        var labels = {};

        @foreach ($socialIcons as $icon)
            labels[@json($icon['slug'])] = @json($icon['ar']);
        @endforeach

        function applyIcon(slug, fillTitle) {
            if (!slug) return;

            if (select && select.value !== slug) {
                select.value = slug;
                if (window.jQuery && $(select).hasClass('select2-hidden-accessible')) {
                    $(select).trigger('change.select2');
                }
            }

            var radio = document.getElementById('icon_' + slug);
            if (radio) radio.checked = true;

            if (previewIcon) {
                previewIcon.className = 'fa-brands fa-' + slug;
            }
            if (previewLabel) {
                previewLabel.textContent = labels[slug] || slug;
            }
            if (fillTitle && titleInput && !titleInput.dataset.touched) {
                titleInput.value = labels[slug] || slug;
            }
        }

        if (titleInput) {
            titleInput.addEventListener('input', function () {
                titleInput.dataset.touched = '1';
            });
        }

        document.querySelectorAll('input[name="icon_picker"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                applyIcon(this.value, true);
            });
        });

        if (select) {
            $(select).on('change', function () {
                applyIcon(this.value, true);
            });
        }

        applyIcon(select ? select.value : 'facebook', !titleInput || !titleInput.value);
    })();
</script>
@endpush

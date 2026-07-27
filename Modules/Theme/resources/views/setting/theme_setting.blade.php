@extends('layouts.admin')
@push('title', get_phrase('Theme settings'))

@php
    $activeTab = session('tab', request('tab', 'general'));
    $palettes = theme_color_palettes();
    $activeColors = get_active_theme_colors();
    $activeTheme = $activeColors['theme'];
@endphp

@section('content')
<style>
    .ts-page { direction: rtl; }

    .ts-hero {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        gap: 20px;
        padding: 22px 24px;
        border-radius: 20px;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(13, 148, 136, 0.18), transparent 55%),
            linear-gradient(135deg, #0b1220 0%, #132033 100%);
        color: #e2e8f0;
        margin-bottom: 18px;
        overflow: hidden;
        position: relative;
    }

    .ts-hero__eyebrow {
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: #5eead4;
        text-transform: uppercase;
    }

    .ts-hero__title {
        margin: 0 0 8px;
        font-size: 24px;
        font-weight: 800;
        color: #f8fafc;
    }

    .ts-hero__desc {
        margin: 0;
        max-width: 540px;
        font-size: 14px;
        line-height: 1.7;
        color: #94a3b8;
    }

    .ts-hero__preview {
        min-width: 220px;
        padding: 16px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(8px);
    }

    .ts-hero__preview-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    .ts-swatch-row {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }

    .ts-swatch {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        border: 2px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .ts-hero__preview-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 10px 14px;
        border-radius: 12px;
        border: 0;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        background: var(--preview-accent, #009CCC);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.25);
    }

    .ts-shell {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .ts-tabs {
        display: flex;
        gap: 8px;
        padding: 14px 16px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        overflow-x: auto;
    }

    .ts-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 12px;
        border: 1px solid transparent;
        background: transparent;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        cursor: pointer;
        transition: .2s ease;
    }

    .ts-tab i { font-size: 15px; }

    .ts-tab:hover {
        background: #fff;
        color: #0f172a;
        border-color: #e2e8f0;
    }

    .ts-tab.active,
    .ts-tab.is-active {
        background: #fff;
        color: #0f766e;
        border-color: #99f6e4;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.12);
    }

    .ts-content { padding: 20px; }

    .ts-panel {
        background: #fff;
        border-radius: 16px;
    }

    .ts-panel__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .ts-panel__head h2 {
        margin: 0 0 4px;
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
    }

    .ts-panel__head p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }

    .ts-section {
        margin-bottom: 22px;
        padding: 18px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .ts-section__title {
        margin: 0 0 14px;
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }

    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 10px;
    }

    .image-preview {
        position: relative;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px;
        background: #fff;
        text-align: center;
    }

    .image-preview img {
        max-width: 140px;
        max-height: 90px;
        object-fit: contain;
    }

    .image-preview .current-image-text {
        font-size: 11px;
        color: #64748b;
        margin-top: 6px;
        font-weight: 600;
    }

    .ts-size-hint {
        font-size: 12px;
        color: #334155;
        line-height: 1.6;
        font-weight: 600;
    }

    .ts-size-hint i {
        color: #0d9488;
        margin-inline-end: 4px;
    }

    .ts-size-hint strong {
        color: #0f766e;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    }

    .ts-palette-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    @media (min-width: 576px) {
        .ts-palette-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (min-width: 992px) {
        .ts-palette-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    @media (min-width: 1200px) {
        .ts-palette-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); }
    }

    .ts-palette {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 12px;
        border-radius: 16px;
        border: 2px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        transition: .2s ease;
        text-align: start;
        width: 100%;
    }

    .ts-palette:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
    }

    .ts-palette.is-active {
        border-color: #14b8a6;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
    }

    .ts-palette__colors {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 4px;
        height: 36px;
        border-radius: 10px;
        overflow: hidden;
    }

    .ts-palette__colors span { display: block; height: 100%; }

    .ts-palette__meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .ts-palette__name {
        font-size: 12px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .ts-palette__check {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ccfbf1;
        color: #0f766e;
        font-size: 11px;
        opacity: 0;
    }

    .ts-palette.is-active .ts-palette__check { opacity: 1; }

    .ts-custom-card {
        margin-top: 18px;
        padding: 18px;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
        background: #fff;
    }

    .ts-custom-card.is-locked {
        opacity: 0.55;
        pointer-events: none;
    }

    .ts-color-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .ts-color-field label {
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        margin: 0;
    }

    .ts-color-input {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .ts-color-input input[type="color"] {
        width: 42px;
        height: 36px;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: pointer;
    }

    .ts-color-input input[type="text"] {
        flex: 1;
        border: 0;
        background: transparent;
        font-weight: 700;
        color: #0f172a;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        text-transform: uppercase;
    }

    .ts-color-input input[type="text"]:focus {
        outline: none;
    }

    .ts-live-preview {
        margin-top: 18px;
        padding: 18px;
        border-radius: 18px;
        background: linear-gradient(180deg, #f8fafc, #fff);
        border: 1px solid #e2e8f0;
    }

    .ts-live-preview__bar {
        height: 10px;
        border-radius: 999px;
        margin-bottom: 14px;
        background: linear-gradient(90deg, var(--preview-secondary), var(--preview-accent), var(--preview-hover));
    }

    .ts-live-preview__card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .ts-live-preview__card h4 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 800;
        color: var(--preview-primary, #1C2B3D);
    }

    .ts-live-preview__card p {
        margin: 0;
        font-size: 12px;
        color: var(--preview-gray, #78828C);
    }

    .ts-live-preview__cta {
        padding: 10px 16px;
        border-radius: 12px;
        border: 0;
        color: #fff;
        font-weight: 800;
        font-size: 12px;
        background: var(--preview-accent, #009CCC);
        white-space: nowrap;
    }

    .ts-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px dashed #e2e8f0;
    }

    @media (max-width: 768px) {
        .ts-hero {
            flex-direction: column;
        }
        .ts-hero__preview {
            min-width: 0;
            width: 100%;
        }
    }
</style>

<div class="admin-page ts-page">
    <div class="ts-hero">
        <div>
            <p class="ts-hero__eyebrow">{{ get_phrase('Frontend Theme') }}</p>
            <h1 class="ts-hero__title">{{ get_phrase('إعدادات الثيم') }}</h1>
            <p class="ts-hero__desc">
                {{ get_phrase('إدارة الهوية البصرية للموقع: الشعارات، المعلومات، والألوان الأساسية التي تظهر ديناميكياً في الواجهة') }}
            </p>
        </div>
        <div class="ts-hero__preview" id="tsHeroPreview"
             style="--preview-accent: {{ $activeColors['accent'] }}; --preview-hover: {{ $activeColors['accent_hover'] }}; --preview-secondary: {{ $activeColors['secondary'] }}; --preview-primary: {{ $activeColors['primary'] }}; --preview-gray: {{ $activeColors['gray'] }};">
            <span class="ts-hero__preview-label">{{ get_phrase('معاينة الألوان الحالية') }}</span>
            <div class="ts-swatch-row">
                <span class="ts-swatch" data-swatch="accent" style="background: {{ $activeColors['accent'] }}"></span>
                <span class="ts-swatch" data-swatch="hover" style="background: {{ $activeColors['accent_hover'] }}"></span>
                <span class="ts-swatch" data-swatch="secondary" style="background: {{ $activeColors['secondary'] }}"></span>
                <span class="ts-swatch" data-swatch="primary" style="background: {{ $activeColors['primary'] }}"></span>
                <span class="ts-swatch" data-swatch="gray" style="background: {{ $activeColors['gray'] }}"></span>
            </div>
            <button type="button" class="ts-hero__preview-btn">{{ get_phrase('زر تجريبي') }}</button>
        </div>
    </div>

    <div class="ts-shell">
        <nav class="ts-tabs nav" role="tablist">
            <button type="button"
                class="ts-tab nav-link {{ $activeTab === 'general' ? 'active is-active' : '' }}"
                data-ts-target="#theme-general" role="tab"
                aria-controls="theme-general"
                aria-selected="{{ $activeTab === 'general' ? 'true' : 'false' }}">
                <i class="fi-rr-settings"></i>
                <span>{{ get_phrase('الإعدادات العامة') }}</span>
            </button>
            <button type="button"
                class="ts-tab nav-link {{ $activeTab === 'colors' ? 'active is-active' : '' }}"
                data-ts-target="#theme-colors" role="tab"
                aria-controls="theme-colors"
                aria-selected="{{ $activeTab === 'colors' ? 'true' : 'false' }}">
                <i class="fi-rr-palette"></i>
                <span>{{ get_phrase('ألوان الموقع') }}</span>
            </button>
        </nav>

        <div class="tab-content ts-content">
            {{-- General Settings --}}
            <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" id="theme-general" role="tabpanel">
                <form class="required-form ts-panel" action="{{ route('admin.theme.settings.store', 'theme_settings') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="ts-panel__head">
                        <div>
                            <h2>{{ get_phrase('الهوية والمحتوى') }}</h2>
                            <p>{{ get_phrase('الشعارات، بيانات المدرس، وإعدادات الظهور في الواجهة') }}</p>
                        </div>
                    </div>

                    <div class="ts-section">
                        <h3 class="ts-section__title">{{ get_phrase('Images & Logos') }}</h3>
                        <div class="alert alert-info border-0 mb-3" style="background:#ecfeff;color:#0e7490;border-radius:12px;font-size:13px;">
                            <i class="fi-rr-info"></i>
                            {{ get_phrase('الصورة تُرفع كما هي بدون تصغير أو ضغط — ارفع المقاس المناسب بنفسك') }}
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="logo" class="form-label ol-form-label">{{ get_phrase('Logo') }}</label>
                                <input type="file" name="logo" class="form-control ol-form-control" id="logo" accept="image/*" />
                                <small class="ts-size-hint d-block mt-2">
                                    <i class="fi-rr-picture"></i>
                                    {{ get_phrase('المقاس الصحيح:') }}
                                    <strong>400 × 200 px</strong>
                                    <span class="text-muted">({{ get_phrase('نسبة 16:9 أو أفقي — PNG شفاف مفضّل') }})</span>
                                </small>
                                @if(get_theme_settings('logo'))
                                    <div class="image-preview-container">
                                        <div class="image-preview">
                                            <img src="{{ asset(get_theme_settings('logo')) }}" alt="Current Logo">
                                            <div class="current-image-text">{{ get_phrase('Current Logo') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="dark_logo" class="form-label ol-form-label">{{ get_phrase('Dark Logo') }}</label>
                                <input type="file" name="dark_logo" class="form-control ol-form-control" id="dark_logo" accept="image/*" />
                                <small class="ts-size-hint d-block mt-2">
                                    <i class="fi-rr-picture"></i>
                                    {{ get_phrase('المقاس الصحيح:') }}
                                    <strong>400 × 200 px</strong>
                                    <span class="text-muted">({{ get_phrase('نسخة الوضع الداكن — نفس مقاس اللوجو') }})</span>
                                </small>
                                @if(get_theme_settings('dark_logo'))
                                    <div class="image-preview-container">
                                        <div class="image-preview">
                                            <img src="{{ asset(get_theme_settings('dark_logo')) }}" alt="Current Dark Logo">
                                            <div class="current-image-text">{{ get_phrase('Current Dark Logo') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="thumbnail" class="form-label ol-form-label">{{ get_phrase('Thumbnail') }}</label>
                                <input type="file" name="thumbnail" class="form-control ol-form-control" id="thumbnail" accept="image/*" />
                                <small class="ts-size-hint d-block mt-2">
                                    <i class="fi-rr-picture"></i>
                                    {{ get_phrase('المقاس الصحيح:') }}
                                    <strong>900 × 900 px</strong>
                                    <span class="text-muted">({{ get_phrase('مربع 1:1 — صورة الهيرو في الصفحة الرئيسية') }})</span>
                                </small>
                                @if(get_theme_settings('thumbnail'))
                                    <div class="image-preview-container">
                                        <div class="image-preview">
                                            <img src="{{ asset(get_theme_settings('thumbnail')) }}" alt="Current Thumbnail">
                                            <div class="current-image-text">{{ get_phrase('Current Thumbnail') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="dark_thumbnail" class="form-label ol-form-label">{{ get_phrase('Dark Thumbnail') }}</label>
                                <input type="file" name="dark_thumbnail" class="form-control ol-form-control" id="dark_thumbnail" accept="image/*" />
                                <small class="ts-size-hint d-block mt-2">
                                    <i class="fi-rr-picture"></i>
                                    {{ get_phrase('المقاس الصحيح:') }}
                                    <strong>900 × 900 px</strong>
                                    <span class="text-muted">({{ get_phrase('نسخة الوضع الداكن — نفس مقاس الصورة') }})</span>
                                </small>
                                @if(get_theme_settings('dark_thumbnail'))
                                    <div class="image-preview-container">
                                        <div class="image-preview">
                                            <img src="{{ asset(get_theme_settings('dark_thumbnail')) }}" alt="Current Dark Thumbnail">
                                            <div class="current-image-text">{{ get_phrase('Current Dark Thumbnail') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="ts-section">
                        <h3 class="ts-section__title">{{ get_phrase('Basic Information') }}</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="jop_title">{{ get_phrase('Job Title') }}<span class="required">*</span></label>
                                <input type="text" name="jop_title" id="jop_title" class="form-control ol-form-control" value="{{ get_theme_settings('jop_title') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="name">{{ get_phrase('Owner Name') }}<span class="required">*</span></label>
                                <input type="text" name="name" id="name" class="form-control ol-form-control" value="{{ get_theme_settings('name') }}" required>
                            </div>
                            <div class="col-12">
                                <label for="instructor_description" class="form-label ol-form-label">{{ get_phrase('Instructor Description') }}<span class="required">*</span></label>
                                <textarea name="instructor_description" rows="4" class="form-control ol-form-control text_editor" id="instructor_description" required>{{ get_theme_settings('instructor_description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="ts-section">
                        <h3 class="ts-section__title">{{ get_phrase('إحصائيات الهيرو') }}</h3>
                        <p class="mb-3 text-muted" style="font-size: 13px; line-height: 1.7;">
                            {{ get_phrase('تظهر أسفل وصف المدرس في الصفحة الرئيسية مع عدّاد متحرك.') }}
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="hero_stats_status">{{ get_phrase('حالة الإحصائيات') }}</label>
                                <select class="form-control ol-form-control ol-select2" name="hero_stats_status" id="hero_stats_status" required>
                                    <option value="1" @if (get_theme_settings('hero_stats_status') === false || get_theme_settings('hero_stats_status') == 1) selected @endif>{{ get_phrase('Active') }}</option>
                                    <option value="0" @if (get_theme_settings('hero_stats_status') !== false && get_theme_settings('hero_stats_status') == 0) selected @endif>{{ get_phrase('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="hero_stats_students">{{ get_phrase('عدد الطلاب المشتركين') }}</label>
                                <input type="number" min="0" step="1" name="hero_stats_students" id="hero_stats_students" class="form-control ol-form-control" value="{{ get_theme_settings('hero_stats_students') ?: 0 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="hero_stats_students_label">{{ get_phrase('نص الطلاب') }}</label>
                                <input type="text" name="hero_stats_students_label" id="hero_stats_students_label" class="form-control ol-form-control" value="{{ get_theme_settings('hero_stats_students_label') ?: 'طالب مشترك' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="hero_stats_youtube">{{ get_phrase('عدد مشتركي يوتيوب') }}</label>
                                <input type="number" min="0" step="1" name="hero_stats_youtube" id="hero_stats_youtube" class="form-control ol-form-control" value="{{ get_theme_settings('hero_stats_youtube') ?: 0 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="hero_stats_youtube_label">{{ get_phrase('نص يوتيوب') }}</label>
                                <input type="text" name="hero_stats_youtube_label" id="hero_stats_youtube_label" class="form-control ol-form-control" value="{{ get_theme_settings('hero_stats_youtube_label') ?: 'مشترك يوتيوب' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="hero_stats_facebook">{{ get_phrase('عدد متابعي فيسبوك') }}</label>
                                <input type="number" min="0" step="1" name="hero_stats_facebook" id="hero_stats_facebook" class="form-control ol-form-control" value="{{ get_theme_settings('hero_stats_facebook') ?: 0 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="hero_stats_facebook_label">{{ get_phrase('نص فيسبوك') }}</label>
                                <input type="text" name="hero_stats_facebook_label" id="hero_stats_facebook_label" class="form-control ol-form-control" value="{{ get_theme_settings('hero_stats_facebook_label') ?: 'متابع فيسبوك' }}">
                            </div>
                        </div>
                    </div>

                    <div class="ts-section">
                        <h3 class="ts-section__title">{{ get_phrase('Feature Settings') }}</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="book_status">{{ get_phrase('Book Status') }}</label>
                                <select class="form-control ol-form-control ol-select2" name="book_status" id="book_status" required>
                                    <option value="">{{ get_phrase('Choose status ...') }}</option>
                                    <option value="1" @if (get_theme_settings('book_status') == 1) selected @endif>{{ get_phrase('Active') }}</option>
                                    <option value="0" @if (get_theme_settings('book_status') == 0) selected @endif>{{ get_phrase('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="course_status">{{ get_phrase('Course Description Status') }}</label>
                                <select class="form-control ol-form-control ol-select2" name="course_status" id="course_status" required>
                                    <option value="">{{ get_phrase('Choose status ...') }}</option>
                                    <option value="1" @if (get_theme_settings('course_status') == 1) selected @endif>{{ get_phrase('Active') }}</option>
                                    <option value="0" @if (get_theme_settings('course_status') == 0) selected @endif>{{ get_phrase('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="sub_status">{{ get_phrase('Subscription Button Status') }}</label>
                                <select class="form-control ol-form-control ol-select2" name="sub_status" id="sub_status" required>
                                    <option value="">{{ get_phrase('Choose status ...') }}</option>
                                    <option value="1" @if (get_theme_settings('sub_status') == 1) selected @endif>{{ get_phrase('Active') }}</option>
                                    <option value="0" @if (get_theme_settings('sub_status') == 0) selected @endif>{{ get_phrase('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="terms_status">{{ get_phrase('Terms Status') }}</label>
                                <select class="form-control ol-form-control ol-select2" name="terms_status" id="terms_status" required>
                                    <option value="">{{ get_phrase('Choose status ...') }}</option>
                                    <option value="1" @if (get_theme_settings('terms_status') == 1) selected @endif>{{ get_phrase('Active') }}</option>
                                    <option value="0" @if (get_theme_settings('terms_status') == 0) selected @endif>{{ get_phrase('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="email_required">
                                    {{ get_phrase('البريد الإلكتروني') }}
                                </label>
                                <select class="form-control ol-form-control ol-select2" name="email_required" id="email_required" required>
                                    <option value="1" @if (get_theme_settings('email_required') === false || get_theme_settings('email_required') == 1) selected @endif>
                                        {{ get_phrase('إجباري') }}
                                    </option>
                                    <option value="0" @if (get_theme_settings('email_required') !== false && get_theme_settings('email_required') == 0) selected @endif>
                                        {{ get_phrase('اختياري') }}
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-1">
                                    {{ get_phrase('يتحكم في إلزام البريد الإلكتروني عند التسجيل وتحديث الحساب') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="national_id_required">
                                    {{ get_phrase('الرقم القومي') }}
                                </label>
                                <select class="form-control ol-form-control ol-select2" name="national_id_required" id="national_id_required" required>
                                    <option value="1" @if (get_theme_settings('national_id_required') === false || get_theme_settings('national_id_required') == 1) selected @endif>
                                        {{ get_phrase('إجباري') }}
                                    </option>
                                    <option value="0" @if (get_theme_settings('national_id_required') !== false && get_theme_settings('national_id_required') == 0) selected @endif>
                                        {{ get_phrase('اختياري') }}
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-1">
                                    {{ get_phrase('يتحكم في إلزام الرقم القومي عند التسجيل وتحديث الحساب') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="national_image_required">
                                    {{ get_phrase('شهادة الميلاد / البطاقة') }}
                                </label>
                                <select class="form-control ol-form-control ol-select2" name="national_image_required" id="national_image_required" required>
                                    <option value="1" @if (get_theme_settings('national_image_required') === false || get_theme_settings('national_image_required') == 1) selected @endif>
                                        {{ get_phrase('إجباري') }}
                                    </option>
                                    <option value="0" @if (get_theme_settings('national_image_required') !== false && get_theme_settings('national_image_required') == 0) selected @endif>
                                        {{ get_phrase('اختياري') }}
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-1">
                                    {{ get_phrase('يتحكم في إلزام رفع شهادة الميلاد عند التسجيل وتحديث الحساب') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="student_grade_source">
                                    {{ get_phrase('مصدر صفوف الطلاب') }}
                                </label>
                                <select class="form-control ol-form-control ol-select2" name="student_grade_source" id="student_grade_source" required>
                                    <option value="category" @if (student_grade_source() === 'category') selected @endif>
                                        {{ get_phrase('من الكاتيجوري') }}
                                    </option>
                                    <option value="subcategory" @if (student_grade_source() === 'subcategory') selected @endif>
                                        {{ get_phrase('من السب كاتيجوري') }}
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-1">
                                    {{ get_phrase('يحدد ما يظهر للطالب كصف دراسي عند التسجيل وتحديث الحساب') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="ts-section">
                        <h3 class="ts-section__title">{{ get_phrase('Technical Support') }}</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="technical_status">{{ get_phrase('Technical Support Status') }}</label>
                                <select class="form-control ol-form-control ol-select2" name="technical_status" id="technical_status" required>
                                    <option value="">{{ get_phrase('Choose status ...') }}</option>
                                    <option value="1" @if (get_theme_settings('technical_status') == 1) selected @endif>{{ get_phrase('Active') }}</option>
                                    <option value="0" @if (get_theme_settings('technical_status') == 0) selected @endif>{{ get_phrase('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ol-form-label" for="technical">{{ get_phrase('Technical Support') }}<span class="required">*</span></label>
                                <input type="text" name="technical" id="technical" class="form-control ol-form-control" value="{{ get_theme_settings('technical') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="ts-section">
                        <h3 class="ts-section__title">{{ get_phrase('Footer Settings') }}</h3>
                        <label for="footer_description" class="form-label ol-form-label">{{ get_phrase('Footer Description') }}<span class="required">*</span></label>
                        <textarea name="footer_description" rows="4" class="form-control ol-form-control text_editor" id="footer_description" required>{{ get_theme_settings('footer_description') }}</textarea>
                    </div>

                    <div class="ts-section">
                        <h3 class="ts-section__title">{{ get_phrase('Terms & conditions Settings') }}</h3>
                        <p class="mb-2 text-muted">
                            {{ get_phrase('تمت إدارة الشروط وسياسة الخصوصية من صفحة مستقلة بإضافة بنود ديناميكية.') }}
                        </p>
                        <a href="{{ route('admin.theme.legal') }}" class="admin-btn admin-btn--primary">
                            <i class="fi-rr-document"></i>
                            <span>{{ get_phrase('إدارة الشروط والخصوصية') }}</span>
                        </a>
                    </div>

                    <div class="ts-actions">
                        <button type="submit" class="admin-btn admin-btn--primary">
                            <i class="fi-rr-disk"></i>
                            <span>{{ get_phrase('Save Settings') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Colors --}}
            <div class="tab-pane fade {{ $activeTab === 'colors' ? 'show active' : '' }}" id="theme-colors" role="tabpanel">
                <form class="ts-panel" id="themeColorsForm" action="{{ route('admin.theme.settings.store', 'theme_colors') }}" method="post">
                    @csrf
                    <input type="hidden" name="color_theme" id="color_theme" value="{{ $activeTheme }}">

                    <div class="ts-panel__head">
                        <div>
                            <h2>{{ get_phrase('ألوان الموقع') }}</h2>
                            <p>{{ get_phrase('اختر من 30 ثيم متناسق أو خصّص ألوانك — التغيير يطبّق فوراً على الواجهة بعد الحفظ') }}</p>
                        </div>
                    </div>

                    <div class="ts-section" style="background:#fff;">
                        <h3 class="ts-section__title">{{ get_phrase('الثيمات الجاهزة') }} ({{ count($palettes) }})</h3>
                        <div class="ts-palette-grid" id="paletteGrid">
                            @foreach ($palettes as $key => $palette)
                                <button type="button"
                                    class="ts-palette {{ $activeTheme === $key ? 'is-active' : '' }}"
                                    data-theme="{{ $key }}"
                                    data-accent="{{ $palette['accent'] }}"
                                    data-hover="{{ $palette['accent_hover'] }}"
                                    data-primary="{{ $palette['primary'] }}"
                                    data-secondary="{{ $palette['secondary'] }}"
                                    data-gray="{{ $palette['gray'] }}">
                                    <div class="ts-palette__colors">
                                        <span style="background: {{ $palette['accent'] }}"></span>
                                        <span style="background: {{ $palette['accent_hover'] }}"></span>
                                        <span style="background: {{ $palette['secondary'] }}"></span>
                                        <span style="background: {{ $palette['primary'] }}"></span>
                                        <span style="background: {{ $palette['gray'] }}"></span>
                                    </div>
                                    <div class="ts-palette__meta">
                                        <p class="ts-palette__name">{{ $palette['name_ar'] }}</p>
                                        <span class="ts-palette__check"><i class="fi-rr-check"></i></span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="ts-custom-card {{ $activeTheme !== 'custom' ? 'is-locked' : '' }}" id="customColorsCard">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <h3 class="ts-section__title mb-1">{{ get_phrase('ألوان مخصصة') }}</h3>
                                <p class="mb-0" style="font-size:12px;color:#64748b;">{{ get_phrase('فعّل الوضع المخصص لتعديل كل لون يدوياً') }}</p>
                            </div>
                            <button type="button" class="admin-btn {{ $activeTheme === 'custom' ? 'admin-btn--primary' : '' }}" id="enableCustomBtn">
                                <i class="fi-rr-palette"></i>
                                <span>{{ get_phrase('تخصيص الألوان') }}</span>
                            </button>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <div class="ts-color-field">
                                    <label>{{ get_phrase('اللون الأساسي (Accent)') }}</label>
                                    <div class="ts-color-input">
                                        <input type="color" id="pick_accent" value="{{ $activeColors['accent'] }}">
                                        <input type="text" name="color_accent" id="color_accent" value="{{ $activeColors['accent'] }}" maxlength="7">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="ts-color-field">
                                    <label>{{ get_phrase('لون التحويم (Hover)') }}</label>
                                    <div class="ts-color-input">
                                        <input type="color" id="pick_hover" value="{{ $activeColors['accent_hover'] }}">
                                        <input type="text" name="color_accent_hover" id="color_accent_hover" value="{{ $activeColors['accent_hover'] }}" maxlength="7">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="ts-color-field">
                                    <label>{{ get_phrase('اللون الثانوي') }}</label>
                                    <div class="ts-color-input">
                                        <input type="color" id="pick_secondary" value="{{ $activeColors['secondary'] }}">
                                        <input type="text" name="color_secondary" id="color_secondary" value="{{ $activeColors['secondary'] }}" maxlength="7">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="ts-color-field">
                                    <label>{{ get_phrase('اللون الداكن (Primary)') }}</label>
                                    <div class="ts-color-input">
                                        <input type="color" id="pick_primary" value="{{ $activeColors['primary'] }}">
                                        <input type="text" name="color_primary" id="color_primary" value="{{ $activeColors['primary'] }}" maxlength="7">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="ts-color-field">
                                    <label>{{ get_phrase('الرمادي') }}</label>
                                    <div class="ts-color-input">
                                        <input type="color" id="pick_gray" value="{{ $activeColors['gray'] }}">
                                        <input type="text" name="color_gray" id="color_gray" value="{{ $activeColors['gray'] }}" maxlength="7">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ts-live-preview" id="livePreview"
                         style="--preview-accent: {{ $activeColors['accent'] }}; --preview-hover: {{ $activeColors['accent_hover'] }}; --preview-secondary: {{ $activeColors['secondary'] }}; --preview-primary: {{ $activeColors['primary'] }}; --preview-gray: {{ $activeColors['gray'] }};">
                        <div class="ts-live-preview__bar"></div>
                        <div class="ts-live-preview__card">
                            <div>
                                <h4>{{ get_phrase('معاينة حيّة') }}</h4>
                                <p>{{ get_phrase('هكذا ستظهر الأزرار والعناوين على الموقع') }}</p>
                            </div>
                            <button type="button" class="ts-live-preview__cta">{{ get_phrase('اشترك الآن') }}</button>
                        </div>
                    </div>

                    <div class="ts-actions">
                        <button type="submit" class="admin-btn admin-btn--primary">
                            <i class="fi-rr-disk"></i>
                            <span>{{ get_phrase('حفظ الألوان') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
    "use strict";

    (function () {
        const tabRoot = document.querySelector('.ts-shell');
        if (!tabRoot) return;

        const tabs = tabRoot.querySelectorAll('[data-ts-target]');
        const panes = tabRoot.querySelectorAll('.ts-content > .tab-pane');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                const target = tab.getAttribute('data-ts-target');
                tabs.forEach(function (t) {
                    t.classList.remove('active', 'is-active');
                    t.setAttribute('aria-selected', 'false');
                });
                tab.classList.add('active', 'is-active');
                tab.setAttribute('aria-selected', 'true');
                panes.forEach(function (pane) {
                    pane.classList.remove('show', 'active');
                    if ('#' + pane.id === target) {
                        pane.classList.add('show', 'active');
                    }
                });
                try {
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', target === '#theme-colors' ? 'colors' : 'general');
                    window.history.replaceState({}, '', url);
                } catch (e) {}
            });
        });

        $('input[type="file"]').on('change', function () {
            const input = this;
            let container = $(this).siblings('.image-preview-container');
            if (!container.length) {
                container = $('<div class="image-preview-container"></div>').insertAfter(this);
            }
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    container.html(
                        '<div class="image-preview"><img src="' + e.target.result + '" alt="Preview">' +
                        '<div class="current-image-text">{{ get_phrase('New Image Preview') }}</div></div>'
                    );
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        const themeInput = document.getElementById('color_theme');
        const customCard = document.getElementById('customColorsCard');
        const enableCustomBtn = document.getElementById('enableCustomBtn');
        const paletteButtons = document.querySelectorAll('.ts-palette');
        const fields = {
            accent: { pick: 'pick_accent', text: 'color_accent' },
            hover: { pick: 'pick_hover', text: 'color_accent_hover' },
            secondary: { pick: 'pick_secondary', text: 'color_secondary' },
            primary: { pick: 'pick_primary', text: 'color_primary' },
            gray: { pick: 'pick_gray', text: 'color_gray' },
        };

        function normalizeHex(value) {
            if (!value) return '#009CCC';
            value = String(value).trim();
            if (value[0] !== '#') value = '#' + value;
            if (!/^#[0-9A-Fa-f]{6}$/.test(value)) return null;
            return value.toUpperCase();
        }

        function applyPreview(colors) {
            const targets = [document.getElementById('livePreview'), document.getElementById('tsHeroPreview')];
            targets.forEach(function (el) {
                if (!el) return;
                el.style.setProperty('--preview-accent', colors.accent);
                el.style.setProperty('--preview-hover', colors.hover);
                el.style.setProperty('--preview-secondary', colors.secondary);
                el.style.setProperty('--preview-primary', colors.primary);
                el.style.setProperty('--preview-gray', colors.gray);
            });

            const map = {
                accent: colors.accent,
                hover: colors.hover,
                secondary: colors.secondary,
                primary: colors.primary,
                gray: colors.gray,
            };
            document.querySelectorAll('[data-swatch]').forEach(function (sw) {
                const key = sw.getAttribute('data-swatch');
                if (map[key]) sw.style.background = map[key];
            });
        }

        function setFieldValues(colors, lockCustom) {
            Object.keys(fields).forEach(function (key) {
                const hex = normalizeHex(colors[key]) || '#009CCC';
                const pick = document.getElementById(fields[key].pick);
                const text = document.getElementById(fields[key].text);
                if (pick) pick.value = hex;
                if (text) text.value = hex;
            });
            applyPreview({
                accent: colors.accent,
                hover: colors.hover,
                secondary: colors.secondary,
                primary: colors.primary,
                gray: colors.gray,
            });
            if (customCard) {
                customCard.classList.toggle('is-locked', !!lockCustom);
            }
            if (enableCustomBtn) {
                enableCustomBtn.classList.toggle('admin-btn--primary', !lockCustom);
            }
        }

        function readCurrentColors() {
            return {
                accent: normalizeHex(document.getElementById('color_accent').value) || '#009CCC',
                hover: normalizeHex(document.getElementById('color_accent_hover').value) || '#008CB8',
                secondary: normalizeHex(document.getElementById('color_secondary').value) || '#32B4DC',
                primary: normalizeHex(document.getElementById('color_primary').value) || '#1C2B3D',
                gray: normalizeHex(document.getElementById('color_gray').value) || '#78828C',
            };
        }

        paletteButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                paletteButtons.forEach(function (b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');
                themeInput.value = btn.getAttribute('data-theme');
                setFieldValues({
                    accent: btn.getAttribute('data-accent'),
                    hover: btn.getAttribute('data-hover'),
                    secondary: btn.getAttribute('data-secondary'),
                    primary: btn.getAttribute('data-primary'),
                    gray: btn.getAttribute('data-gray'),
                }, true);
            });
        });

        if (enableCustomBtn) {
            enableCustomBtn.addEventListener('click', function () {
                themeInput.value = 'custom';
                paletteButtons.forEach(function (b) { b.classList.remove('is-active'); });
                setFieldValues(readCurrentColors(), false);
            });
        }

        Object.keys(fields).forEach(function (key) {
            const pick = document.getElementById(fields[key].pick);
            const text = document.getElementById(fields[key].text);
            if (!pick || !text) return;

            pick.addEventListener('input', function () {
                themeInput.value = 'custom';
                paletteButtons.forEach(function (b) { b.classList.remove('is-active'); });
                if (customCard) customCard.classList.remove('is-locked');
                text.value = pick.value.toUpperCase();
                applyPreview(readCurrentColors());
            });

            text.addEventListener('input', function () {
                const hex = normalizeHex(text.value);
                if (!hex) return;
                themeInput.value = 'custom';
                paletteButtons.forEach(function (b) { b.classList.remove('is-active'); });
                if (customCard) customCard.classList.remove('is-locked');
                text.value = hex;
                pick.value = hex;
                applyPreview(readCurrentColors());
            });
        });
    })();
</script>
@endpush

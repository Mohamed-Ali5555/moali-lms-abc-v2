@extends('layouts.admin')
@push('title', get_phrase('ورقة الامتحان') . ' — ' . ($quiz->title ?? ''))

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --exam-ink: #0f172a;
        --exam-muted: #475569;
        --exam-line: #cbd5e1;
        --exam-accent: #0f766e;
        --exam-soft: #f0fdfa;
        --exam-paper: #ffffff;
    }

    .exam-screen {
        max-width: 920px;
        margin: 0 auto;
    }

    .exam-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .exam-toolbar__meta h1 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }

    .exam-toolbar__meta p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }

    .exam-toolbar__actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .exam-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #0f172a;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none !important;
        cursor: pointer;
    }

    .exam-btn:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    .exam-btn--primary {
        border-color: #0f766e;
        background: #0f766e;
        color: #fff !important;
    }

    .exam-btn--primary:hover {
        background: #115e59;
        border-color: #115e59;
        color: #fff !important;
    }

    .exam-sheet-wrap {
        padding: 18px;
        border-radius: 18px;
        background:
            radial-gradient(circle at 10% 0%, rgba(15, 118, 110, 0.08), transparent 40%),
            #e2e8f0;
    }

    .exam-sheet {
        width: 210mm;
        max-width: 100%;
        min-height: 297mm;
        margin: 0 auto;
        padding: 14mm 14mm 16mm;
        background: var(--exam-paper);
        color: var(--exam-ink);
        font-family: "Cairo", "Segoe UI", Tahoma, sans-serif;
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
        border-radius: 4px;
        position: relative;
        direction: rtl;
    }

    .exam-sheet::before {
        content: "";
        position: absolute;
        inset: 8px;
        border: 1px solid #e2e8f0;
        border-radius: 2px;
        pointer-events: none;
    }

    .exam-head {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: 90px 1fr 90px;
        gap: 12px;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 2.5px solid var(--exam-accent);
        margin-bottom: 14px;
    }

    .exam-logo {
        width: 78px;
        height: 78px;
        object-fit: contain;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 6px;
    }

    .exam-logo-fallback {
        width: 78px;
        height: 78px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--exam-soft);
        border: 1px solid #99f6e4;
        color: var(--exam-accent);
        font-weight: 800;
        font-size: 22px;
    }

    .exam-brand {
        text-align: center;
    }

    .exam-brand__center {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: var(--exam-accent);
        letter-spacing: 0.02em;
    }

    .exam-brand__title {
        margin: 4px 0 0;
        font-size: 22px;
        font-weight: 800;
        color: var(--exam-ink);
        line-height: 1.35;
    }

    .exam-brand__sub {
        margin: 4px 0 0;
        font-size: 12px;
        color: var(--exam-muted);
        font-weight: 600;
    }

    .exam-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin: 12px 0 14px;
        position: relative;
        z-index: 1;
    }

    .exam-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        background: var(--exam-soft);
        border: 1px solid #99f6e4;
        color: #115e59;
        font-size: 12px;
        font-weight: 700;
    }

    .exam-student {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr;
        gap: 10px 14px;
        padding: 12px 14px;
        margin-bottom: 16px;
        border: 1.5px solid var(--exam-line);
        border-radius: 12px;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }

    .exam-field {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        min-height: 34px;
        font-size: 13px;
        font-weight: 700;
        color: var(--exam-ink);
    }

    .exam-field span {
        white-space: nowrap;
    }

    .exam-field__line {
        flex: 1;
        border-bottom: 1.5px dotted #94a3b8;
        min-height: 22px;
    }

    .exam-field--score .exam-field__line {
        max-width: 48px;
        flex: 0 0 48px;
    }

    .exam-note {
        position: relative;
        z-index: 1;
        margin: 0 0 16px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.6;
    }

    .exam-q {
        position: relative;
        z-index: 1;
        padding: 12px 0 14px;
        border-bottom: 1px dashed #e2e8f0;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .exam-q:last-of-type {
        border-bottom: 0;
    }

    .exam-q__head {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .exam-q__num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--exam-accent);
        color: #fff;
        font-size: 13px;
        font-weight: 800;
    }

    .exam-q__title {
        flex: 1;
        font-size: 14.5px;
        font-weight: 700;
        color: var(--exam-ink);
        line-height: 1.7;
    }

    .exam-q__title img,
    .exam-q__title p {
        margin-bottom: 0.35rem;
    }

    .exam-q__title img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .exam-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 16px;
        padding-inline-start: 38px;
    }

    .exam-options--stack {
        grid-template-columns: 1fr;
    }

    .exam-opt {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        min-height: 28px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.55;
    }

    .exam-bubble {
        width: 16px;
        height: 16px;
        border: 1.6px solid #334155;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 3px;
        background: #fff;
    }

    .exam-opt__letter {
        font-weight: 800;
        color: var(--exam-accent);
        min-width: 1.2em;
    }

    .exam-opt img {
        max-width: 120px;
        max-height: 80px;
        object-fit: contain;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #fff;
    }

    .exam-tf {
        display: flex;
        gap: 28px;
        padding-inline-start: 38px;
    }

    .exam-blank {
        padding-inline-start: 38px;
        display: grid;
        gap: 10px;
    }

    .exam-blank__line {
        height: 0;
        border-bottom: 1.5px solid #94a3b8;
        margin-top: 18px;
    }

    .exam-end {
        position: relative;
        z-index: 1;
        margin-top: 22px;
        padding-top: 12px;
        border-top: 2px solid var(--exam-accent);
        text-align: center;
        color: var(--exam-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .exam-end strong {
        display: block;
        color: var(--exam-accent);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .exam-empty {
        text-align: center;
        padding: 48px 20px;
        color: #64748b;
    }

    @media (max-width: 767.98px) {
        .exam-sheet {
            padding: 18px 14px 24px;
        }

        .exam-head {
            grid-template-columns: 1fr;
            text-align: center;
            justify-items: center;
        }

        .exam-student {
            grid-template-columns: 1fr;
        }

        .exam-options {
            grid-template-columns: 1fr;
            padding-inline-start: 0;
        }

        .exam-tf,
        .exam-blank {
            padding-inline-start: 0;
        }
    }

    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }

        html, body {
            background: #fff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .ol-aside,
        .ol-header,
        .ol-sidebar,
        .sidebar,
        .navbar,
        .admin-toolbar,
        .exam-toolbar,
        .exam-sheet-wrap,
        .print-d-none,
        .toast,
        .modal,
        footer {
            display: none !important;
        }

        .ol-body-content,
        .container-fluid,
        .admin-page,
        .content,
        main {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: none !important;
            background: #fff !important;
        }

        .exam-sheet-wrap {
            display: block !important;
            padding: 0 !important;
            background: transparent !important;
            border: 0 !important;
            border-radius: 0 !important;
        }

        .exam-sheet {
            width: 100% !important;
            max-width: none !important;
            min-height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .exam-sheet::before {
            display: none !important;
        }

        .exam-q {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .exam-q__num {
            background: #0f766e !important;
            color: #fff !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $letters = ['أ', 'ب', 'ج', 'د', 'هـ', 'و', 'ز', 'ح', 'ط', 'ي'];
    $systemName = get_settings('system_name') ?: get_settings('system_title') ?: config('app.name');
    $logo = get_settings('light_logo') ?: get_settings('dark_logo') ?: get_frontend_settings('light_logo');
@endphp

<div class="exam-screen admin-page">
    <div class="exam-toolbar print-d-none">
        <div class="exam-toolbar__meta">
            <h1>{{ get_phrase('ورقة امتحان للطباعة') }}</h1>
            <p>{{ get_phrase('معاينة بحجم A4 — جاهزة للتوزيع على الطلاب') }}</p>
        </div>
        <div class="exam-toolbar__actions">
            <a href="{{ route('admin.bank.quizs.index') }}" class="exam-btn">
                <i class="fi-rr-arrow-right"></i>
                {{ get_phrase('رجوع') }}
            </a>
            @if ($questions->count() > 0)
                <button type="button" class="exam-btn exam-btn--primary" id="examPrintBtn">
                    <i class="fi-rr-print"></i>
                    {{ get_phrase('طباعة A4') }}
                </button>
            @endif
        </div>
    </div>

    @if ($questions->count() > 0)
        <div class="exam-sheet-wrap">
            <article class="exam-sheet" id="examSheet">
                <header class="exam-head">
                    <div>
                        @if ($logo)
                            <img src="{{ get_image($logo) }}" alt="logo" class="exam-logo">
                        @else
                            <div class="exam-logo-fallback">{{ mb_substr($systemName, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="exam-brand">
                        <p class="exam-brand__center">{{ $systemName }}</p>
                        <h2 class="exam-brand__title">{{ $quiz->title }}</h2>
                        <p class="exam-brand__sub">{{ get_phrase('امتحان تحريري') }} — {{ now()->format('Y/m/d') }}</p>
                    </div>
                    <div></div>
                </header>

                <div class="exam-meta">
                    <span class="exam-chip">
                        <i class="fi-rr-clock"></i>
                        {{ get_phrase('المدة') }}: {{ $quiz->duration }}
                    </span>
                    <span class="exam-chip">
                        <i class="fi-rr-diploma"></i>
                        {{ get_phrase('الدرجة') }}: {{ $quiz->total_mark }}
                    </span>
                    <span class="exam-chip">
                        <i class="fi-rr-list-check"></i>
                        {{ get_phrase('عدد الأسئلة') }}: {{ $questions->count() }}
                    </span>
                    @if ($quiz->pass_mark)
                        <span class="exam-chip">
                            <i class="fi-rr-check"></i>
                            {{ get_phrase('درجة النجاح') }}: {{ $quiz->pass_mark }}
                        </span>
                    @endif
                </div>

                <section class="exam-student">
                    <div class="exam-field">
                        <span>{{ get_phrase('اسم الطالب') }}:</span>
                        <div class="exam-field__line"></div>
                    </div>
                    <div class="exam-field">
                        <span>{{ get_phrase('رقم الجلوس') }}:</span>
                        <div class="exam-field__line"></div>
                    </div>
                    <div class="exam-field">
                        <span>{{ get_phrase('التاريخ') }}:</span>
                        <div class="exam-field__line"></div>
                    </div>
                    <div class="exam-field exam-field--score">
                        <span>{{ get_phrase('الدرجة') }}:</span>
                        <div class="exam-field__line"></div>
                        <span>/ {{ $quiz->total_mark }}</span>
                    </div>
                    <div class="exam-field">
                        <span>{{ get_phrase('توقيع المصحح') }}:</span>
                        <div class="exam-field__line"></div>
                    </div>
                    <div class="exam-field">
                        <span>{{ get_phrase('المجموعة') }}:</span>
                        <div class="exam-field__line"></div>
                    </div>
                </section>

                <p class="exam-note">
                    {{ get_phrase('تعليمات') }}:
                    {{ get_phrase('اقرأ كل سؤال بعناية، ظلل دائرة الإجابة الصحيحة بقلم أزرق أو أسود، ولا تكتب خارج الأماكن المخصصة.') }}
                </p>

                @foreach ($questions as $key => $question)
                    <section class="exam-q">
                        <div class="exam-q__head">
                            <span class="exam-q__num">{{ $key + 1 }}</span>
                            <div class="exam-q__title">{!! $question->title !!}</div>
                        </div>

                        @if ($question->type === 'mcq')
                            @php
                                $options = json_decode($question->options, true) ?? [];
                                $hasImageOption = collect($options)->contains(function ($option) {
                                    return is_string($option) && preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $option);
                                });
                            @endphp
                            <div class="exam-options {{ $hasImageOption || count($options) > 4 ? 'exam-options--stack' : '' }}">
                                @foreach ($options as $index => $option)
                                    @php
                                        $isImage = is_string($option) && preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $option);
                                        $letter = $letters[$index] ?? ($index + 1);
                                    @endphp
                                    <div class="exam-opt">
                                        <span class="exam-bubble"></span>
                                        <span class="exam-opt__letter">{{ $letter }})</span>
                                        @if ($isImage)
                                            <img src="{{ asset($option) }}" alt="option">
                                        @else
                                            <span>{!! $option !!}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif ($question->type === 'true_false')
                            <div class="exam-tf">
                                <div class="exam-opt">
                                    <span class="exam-bubble"></span>
                                    <span>{{ get_phrase('صح') }}</span>
                                </div>
                                <div class="exam-opt">
                                    <span class="exam-bubble"></span>
                                    <span>{{ get_phrase('خطأ') }}</span>
                                </div>
                            </div>
                        @elseif ($question->type === 'fill_blanks')
                            <div class="exam-blank">
                                <div class="exam-blank__line"></div>
                                <div class="exam-blank__line"></div>
                                <div class="exam-blank__line"></div>
                            </div>
                        @endif
                    </section>
                @endforeach

                <footer class="exam-end">
                    <strong>{{ get_phrase('انتهت الأسئلة — بالتوفيق') }}</strong>
                    <span>{{ $systemName }} · {{ $quiz->title }}</span>
                </footer>
            </article>
        </div>
    @else
        <div class="ol-card">
            <div class="ol-card-body exam-empty">
                <p class="mb-3">{{ get_phrase('لا توجد أسئلة في هذا الامتحان بعد') }}</p>
                <a href="{{ route('admin.bank.quizs.index') }}" class="exam-btn exam-btn--primary">
                    {{ get_phrase('رجوع لقائمة الامتحانات') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('examPrintBtn');
        if (!btn) return;
        btn.addEventListener('click', function () {
            window.print();
        });
    });
</script>
@endpush

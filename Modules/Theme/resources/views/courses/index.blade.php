@extends('theme::layouts.master')

@section('content')
@php
    use Carbon\Carbon;
    Carbon::setLocale('ar');

    $course = $data['course'];
    $isFree = $course->is_paid == 0 && ($course->price == 0 || $course->price < 0 || $course->price === null);
    $isEnrolled = auth()->check() && \App\Models\Enrollments::where('user_id', auth()->id())->where('course_id', $course->id)->exists();
    $categoryTitle = $course->category->parent == null
        ? $course->category->title
        : $course->category->parent->title;

    $totalMinutes = $course->sections->flatMap->allLesson->sum(function ($lesson) {
        if (!$lesson->duration) {
            return 0;
        }
        $time = Carbon::parse($lesson->duration);
        return $time->hour * 60 + $time->minute;
    });
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    $totalQuestions = (int) $data['question'] + (int) $data['question_number_count'];
    $thumbnail = get_image($course->thumbnail ?? '');

    $watchUrl = null;
    if ($isEnrolled) {
        $watch_history = \App\Models\Watch_history::where('course_id', $course->id)
            ->where('student_id', auth()->id())
            ->first();
        $lesson = \App\Models\Lesson::where('course_id', $course->id)->orderBy('sort', 'asc')->first();

        if (!$watch_history && !$lesson) {
            $watchUrl = route('course.player', ['slug' => $course->slug]);
        } else {
            $lesson_id = $watch_history ? $watch_history->watching_lesson_id : ($lesson->id ?? null);
            $watchUrl = $lesson_id
                ? route('course.player', ['slug' => $course->slug, 'id' => $lesson_id])
                : route('course.player', ['slug' => $course->slug]);
        }
    }
@endphp

<style>
    .cd-page { direction: rtl; padding-bottom: 4rem; }

    .cd-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        padding: 4.5rem 0 5.5rem;
        color: #f8fafc;
        background: #0b1220;
    }

    .cd-hero__bg {
        position: absolute;
        inset: 0;
        z-index: -2;
        background-size: cover;
        background-position: center;
        transform: scale(1.04);
        filter: saturate(1.05);
    }

    .cd-hero__overlay {
        position: absolute;
        inset: 0;
        z-index: -1;
        background:
            linear-gradient(120deg, rgba(11, 18, 32, 0.94) 18%, rgba(11, 18, 32, 0.72) 58%, rgba(11, 18, 32, 0.55) 100%),
            radial-gradient(ellipse at 85% 20%, rgba(var(--c-accent-rgb), 0.35), transparent 55%);
    }

    .cd-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1.25rem;
        font-size: 13px;
        font-weight: 700;
        color: rgba(248, 250, 252, 0.72);
    }

    .cd-breadcrumb a {
        color: rgba(248, 250, 252, 0.72);
        text-decoration: none;
        transition: color .2s ease;
    }

    .cd-breadcrumb a:hover { color: #fff; }

    .cd-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        border-radius: 999px;
        background: rgba(var(--c-accent-rgb), 0.18);
        border: 1px solid rgba(var(--c-accent-rgb), 0.35);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .cd-hero__title {
        margin: 0 0 1rem;
        font-size: clamp(1.8rem, 4vw, 3rem);
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: -0.02em;
        max-width: 18ch;
    }

    .cd-hero__desc {
        max-width: 62ch;
        font-size: 1.05rem;
        line-height: 1.85;
        color: rgba(248, 250, 252, 0.82);
        margin-bottom: 1.75rem;
    }

    .cd-hero__desc p { margin-bottom: .5rem; }

    .cd-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    .cd-stat {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-width: 140px;
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .cd-stat__icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--c-accent-rgb), 0.22);
        color: #fff;
        font-size: 15px;
    }

    .cd-stat strong {
        display: block;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .cd-stat span {
        font-size: 11px;
        font-weight: 700;
        color: rgba(248, 250, 252, 0.65);
    }

    .cd-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .cd-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        font-size: 12px;
        font-weight: 700;
    }

    .cd-chip em {
        font-style: normal;
        padding: 3px 8px;
        border-radius: 999px;
        background: rgb(var(--c-accent-rgb));
        color: #fff;
    }

    .cd-chip.is-update em {
        background: rgb(var(--c-secondary-rgb));
    }

    .cd-layout {
        margin-top: -3rem;
        position: relative;
        z-index: 2;
    }

    .cd-card {
        background: rgb(var(--c-card-bg-rgb));
        border: 1px solid rgba(var(--c-border-rgb), 0.9);
        border-radius: 24px;
        box-shadow: 0 18px 50px -28px rgba(15, 23, 42, 0.35);
        overflow: hidden;
    }

    .cd-buy {
        position: sticky;
        top: 100px;
        padding: 0;
    }

    .cd-buy__media {
        position: relative;
        aspect-ratio: 16 / 10;
        overflow: hidden;
    }

    .cd-buy__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .cd-buy__body { padding: 1.25rem 1.35rem 1.5rem; }

    .cd-price {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 1rem;
    }

    .cd-price__main {
        font-size: 2rem;
        font-weight: 800;
        color: rgb(var(--c-text-rgb));
        line-height: 1;
    }

    .cd-price__main small {
        font-size: .95rem;
        font-weight: 700;
        color: rgb(var(--c-gray-rgb));
        margin-inline-start: 4px;
    }

    .cd-price__old {
        font-size: 1rem;
        color: rgb(var(--c-gray-rgb));
        text-decoration: line-through;
        font-weight: 700;
    }

    .cd-price__free {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 12px 14px;
        border-radius: 14px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        font-weight: 800;
        font-size: 1rem;
    }

    .cd-buy__list {
        list-style: none;
        margin: 0 0 1.15rem;
        padding: 0;
    }

    .cd-buy__list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px dashed rgba(var(--c-border-rgb), 1);
        font-size: 13px;
        font-weight: 700;
        color: rgb(var(--c-text-rgb));
    }

    .cd-buy__list li:last-child { border-bottom: 0; }

    .cd-buy__list span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgb(var(--c-gray-rgb));
    }

    .cd-buy__list strong { color: rgb(var(--c-text-rgb)); }

    .cd-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 52px;
        border: 0;
        border-radius: 14px;
        background: var(--main-gradient);
        color: #fff !important;
        font-size: 15px;
        font-weight: 800;
        text-decoration: none !important;
        box-shadow: 0 14px 28px -12px rgba(var(--c-accent-rgb), 0.65);
        transition: transform .2s ease, box-shadow .2s ease;
        cursor: pointer;
    }

    .cd-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px -12px rgba(var(--c-accent-rgb), 0.75);
        color: #fff !important;
    }

    .cd-section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .cd-section-head h2 {
        margin: 0;
        font-size: clamp(1.35rem, 2.5vw, 1.85rem);
        font-weight: 800;
        color: rgb(var(--c-text-rgb));
    }

    .cd-section-head p {
        margin: 6px 0 0;
        color: rgb(var(--c-gray-rgb));
        font-size: 14px;
        font-weight: 600;
    }

    .cd-curriculum {
        padding: 1.5rem;
    }

    .cd-acc { display: flex; flex-direction: column; gap: 14px; }

    .cd-acc__item {
        border: 1px solid rgba(var(--c-border-rgb), 1);
        border-radius: 18px;
        background: rgba(var(--c-bg-rgb), 0.45);
        overflow: hidden;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .cd-acc__item:hover,
    .cd-acc__item.is-open {
        border-color: rgba(var(--c-accent-rgb), 0.35);
        box-shadow: 0 12px 28px -20px rgba(var(--c-accent-rgb), 0.55);
    }

    .cd-acc__btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 18px;
        background: transparent;
        border: 0;
        text-align: start;
        color: rgb(var(--c-text-rgb));
    }

    .cd-acc__btn:focus { outline: none; box-shadow: none; }

    .cd-acc__btn::after { display: none !important; }

    .cd-acc__num {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--c-accent-rgb), 0.12);
        color: rgb(var(--c-accent-rgb));
        font-weight: 800;
        flex-shrink: 0;
    }

    .cd-acc__meta { flex: 1; min-width: 0; }

    .cd-acc__meta h3 {
        margin: 0 0 4px;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .cd-acc__meta span {
        font-size: 12px;
        font-weight: 700;
        color: rgb(var(--c-gray-rgb));
    }

    .cd-acc__chev {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--c-border-rgb), 0.65);
        color: rgb(var(--c-text-rgb));
        transition: transform .25s ease, background .2s ease;
        flex-shrink: 0;
    }

    .cd-acc__btn:not(.collapsed) .cd-acc__chev {
        transform: rotate(180deg);
        background: rgba(var(--c-accent-rgb), 0.15);
        color: rgb(var(--c-accent-rgb));
    }

    .cd-lessons {
        padding: 0 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cd-lesson {
        border: 1px solid rgba(var(--c-border-rgb), 1);
        border-radius: 16px;
        background: rgb(var(--c-card-bg-rgb));
        overflow: hidden;
    }

    .cd-lesson__btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 14px;
        background: transparent;
        border: 0;
        text-align: start;
        color: rgb(var(--c-text-rgb));
    }

    .cd-lesson__btn:focus { outline: none; box-shadow: none; }
    .cd-lesson__btn::after { display: none !important; }

    .cd-lesson__type {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 15px;
        color: #fff;
    }

    .cd-lesson__type.is-quiz { background: linear-gradient(135deg, #f43f5e, #e11d48); }
    .cd-lesson__type.is-hw { background: linear-gradient(135deg, #10b981, #059669); }
    .cd-lesson__type.is-doc { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .cd-lesson__type.is-video { background: linear-gradient(135deg, rgb(var(--c-accent-rgb)), rgb(var(--c-accent-hover-rgb))); }

    .cd-lesson__title {
        flex: 1;
        min-width: 0;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.45;
    }

    .cd-lesson__body {
        padding: 0 16px 16px 16px;
        border-top: 1px dashed rgba(var(--c-border-rgb), 1);
        margin-top: 0;
    }

    .cd-lesson__info {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-top: 14px;
        margin-bottom: 14px;
    }

    .cd-lesson__info-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
        color: rgb(var(--c-text-rgb));
    }

    .cd-lesson__info-row span:first-child {
        color: rgb(var(--c-accent-rgb));
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .cd-lesson__info-row .muted {
        color: rgb(var(--c-gray-rgb));
        font-weight: 600;
    }

    .cd-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 12px;
        border: 1.5px solid transparent;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none !important;
        transition: .2s ease;
    }

    .cd-action.is-quiz {
        color: #e11d48;
        border-color: rgba(225, 29, 72, 0.25);
        background: rgba(225, 29, 72, 0.08);
    }
    .cd-action.is-hw {
        color: #059669;
        border-color: rgba(5, 150, 105, 0.25);
        background: rgba(5, 150, 105, 0.08);
    }
    .cd-action.is-doc {
        color: #2563eb;
        border-color: rgba(37, 99, 235, 0.25);
        background: rgba(37, 99, 235, 0.08);
    }
    .cd-action.is-video {
        color: rgb(var(--c-accent-rgb));
        border-color: rgba(var(--c-accent-rgb), 0.28);
        background: rgba(var(--c-accent-rgb), 0.08);
    }
    .cd-action:hover { filter: brightness(0.95); transform: translateY(-1px); }

    #notification {
        position: fixed;
        top: 90px;
        left: 20px;
        z-index: 1050;
        width: min(320px, calc(100vw - 40px));
        border: 1px solid rgba(var(--c-border-rgb), 1);
        padding: 16px;
        border-radius: 16px;
        background: rgb(var(--c-card-bg-rgb));
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        display: none;
    }

    #close-btn {
        position: absolute;
        top: -10px;
        left: -10px;
        background: #ef4444;
        color: #fff;
        border: none;
        font-size: 18px;
        cursor: pointer;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 991px) {
        .cd-hero { padding: 3.25rem 0 4.5rem; }
        .cd-hero__title { max-width: none; }
        .cd-buy { position: static; margin-bottom: 1.25rem; }
        .cd-layout { margin-top: -2rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        .cd-cta, .cd-action, .cd-acc__chev { transition: none; }
    }
</style>

@if (get_theme_settings('subscriptions_view') == 1)
    <div id="notification">
        <button id="close-btn" type="button">&times;</button>
        <p style="font-weight:800;display:flex;font-size:16px;align-items:center;justify-content:center;gap:6px;margin-bottom:8px;">
            عملية اشتراك جديدة
        </p>
        <p>قام <span style="font-size:16px;color:#ef4444;font-weight:800;" id="user-name"></span> بالاشتراك</p>
        <p>في <span style="font-size:16px;color:rgb(var(--c-accent-rgb));font-weight:800;" id="course-name">{{ $course->title }}</span></p>
        <hr style="border:1px solid rgba(var(--c-accent-rgb),.35);margin:12px 0;">
        <p><span id="purchase-time"></span></p>
    </div>
@endif

<div class="cd-page main_content">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <section class="cd-hero">
        <div class="cd-hero__bg" style="background-image:url('{{ $thumbnail }}')"></div>
        <div class="cd-hero__overlay"></div>

        <div class="container">
            <div class="cd-breadcrumb">
                <a href="{{ route('theme.home') }}">الرئيسية</a>
                <i class="fa-solid fa-chevron-left" style="font-size:10px;opacity:.7"></i>
                <span>{{ $categoryTitle }}</span>
            </div>

            <div class="cd-badge">
                <i class="fa-solid fa-graduation-cap"></i>
                {{ $categoryTitle }}
            </div>

            <h1 class="cd-hero__title">{{ $course->title }}</h1>

            <div class="cd-hero__desc">
                {!! $course->description !!}
            </div>
<!-- 
            <div class="cd-stats">
                <div class="cd-stat">
                    <span class="cd-stat__icon"><i class="fa-solid fa-play"></i></span>
                    <div>
                        <strong>+{{ $data['lessonCount'] }}</strong>
                        <span>فيديوهات</span>
                    </div>
                </div>
                <div class="cd-stat">
                    <span class="cd-stat__icon"><i class="fa-solid fa-file-circle-question"></i></span>
                    <div>
                        <strong>+{{ $data['quizeCount'] }}</strong>
                        <span>امتحانات</span>
                    </div>
                </div>
                <div class="cd-stat">
                    <span class="cd-stat__icon"><i class="fa-solid fa-clipboard-list"></i></span>
                    <div>
                        <strong>+{{ $data['assinmentCount'] }}</strong>
                        <span>واجبات</span>
                    </div>
                </div>
                <div class="cd-stat">
                    <span class="cd-stat__icon"><i class="fa-solid fa-file-lines"></i></span>
                    <div>
                        <strong>+{{ $data['documentCount'] }}</strong>
                        <span>ملفات</span>
                    </div>
                </div>
            </div> -->

            <div class="cd-meta">
                <div class="cd-chip">
                    <i class="fa-regular fa-folder-open"></i>
                    تاريخ إنشاء الكورس
                    <em>{{ Carbon::parse($course->created_at)->isoFormat('dddd، D MMMM YYYY') }}</em>
                </div>
                <div class="cd-chip is-update">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    آخر تحديث
                    <em>{{ Carbon::parse(lastUpdate($course->id))->isoFormat('dddd، D MMMM YYYY') }}</em>
                </div>
            </div>
        </div>
    </section>

    <div class="container cd-layout">
        <div class="row g-4">
            {{-- Curriculum --}}
            <div class="col-lg-8 order-2 order-lg-1">
                <div class="cd-card cd-curriculum">
                    <div class="cd-section-head">
                        <div>
                            <h2>محتوى الكورس</h2>
                            <p>{{ $course->sections->count() }} أقسام · {{ $data['lessonCount'] + $data['quizeCount'] + $data['assinmentCount'] + $data['documentCount'] }} عنصر تعليمي</p>
                        </div>
                    </div>

                    <div class="col-md-12" id="quiz_load" data-url="#"></div>
                    <div class="col-md-12 mt-2" id="video-container" style="display:none;position:relative;">
                        <button id="close-video-btn" class="btn btn-secondary"
                            style="position:absolute;top:0;left:25px;z-index:1000;">Close</button>
                    </div>

                    <div class="accordion cd-acc" id="courseCurriculum">
                        @foreach ($course->sections as $sectionIndex => $section)
                            <div class="cd-acc__item accordion-item">
                                <h2 class="accordion-header">
                                    <button class="cd-acc__btn accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#section_{{ $section->id }}"
                                        aria-expanded="false"
                                        aria-controls="section_{{ $section->id }}">
                                        <span class="cd-acc__num">{{ str_pad($sectionIndex + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="cd-acc__meta">
                                            <h3>{{ $section->title }}</h3>
                                            <span>{{ $section->allLesson->count() }} دروس · {{ $categoryTitle }}</span>
                                        </span>
                                        <span class="cd-acc__chev"><i class="fa-solid fa-chevron-down"></i></span>
                                    </button>
                                </h2>

                                <div id="section_{{ $section->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#courseCurriculum">
                                    <div class="cd-lessons accordion" id="video_{{ $section->id }}">
                                        @foreach ($section->allLesson as $lesson)
                                            @php
                                                $url = route('course.player', [
                                                    'slug' => $course->slug,
                                                    'id' => $lesson->id,
                                                ]);
                                                $canView = viewLesson($course->id, $lesson->id);
                                                $typeClass = 'is-video';
                                                $typeIcon = 'fa-solid fa-play';
                                                $actionClass = 'is-video';
                                                $actionLabel = 'شاهد الآن';

                                                if ($lesson->lesson_type == 'quiz' && $lesson->type == 1) {
                                                    $typeClass = 'is-quiz';
                                                    $typeIcon = 'fa-solid fa-file-circle-question';
                                                    $actionClass = 'is-quiz';
                                                    $actionLabel = 'حل الامتحان';
                                                } elseif ($lesson->lesson_type == 'quiz' && $lesson->type == 2) {
                                                    $typeClass = 'is-hw';
                                                    $typeIcon = 'fa-solid fa-clipboard-check';
                                                    $actionClass = 'is-hw';
                                                    $actionLabel = 'حل الواجب';
                                                } elseif ($lesson->lesson_type == 'document_type' || $lesson->lesson_type == 'text') {
                                                    $typeClass = 'is-doc';
                                                    $typeIcon = 'fa-solid fa-file-lines';
                                                    $actionClass = 'is-doc';
                                                    $actionLabel = 'فتح الملف';
                                                }
                                            @endphp

                                            <div class="cd-lesson accordion-item">
                                                <h3 class="accordion-header">
                                                    <button class="cd-lesson__btn accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#lesson_{{ $lesson->id }}"
                                                        aria-expanded="false"
                                                        aria-controls="lesson_{{ $lesson->id }}">
                                                        <span class="cd-lesson__type {{ $typeClass }}">
                                                            <i class="{{ $typeIcon }}"></i>
                                                        </span>
                                                        <span class="cd-lesson__title">{{ $lesson->title }}</span>
                                                        <span class="cd-acc__chev" style="width:28px;height:28px;font-size:11px;">
                                                            <i class="fa-solid fa-chevron-down"></i>
                                                        </span>
                                                    </button>
                                                </h3>

                                                <div id="lesson_{{ $lesson->id }}" class="accordion-collapse collapse"
                                                    data-bs-parent="#video_{{ $section->id }}">
                                                    <div class="cd-lesson__body">
                                                        <div class="cd-lesson__info">
                                                            <div class="cd-lesson__info-row">
                                                                <span><i class="fa-regular fa-clock"></i> المدة:</span>
                                                                <span class="muted">{{ lesson_durations($lesson->id) }}</span>
                                                            </div>
                                                            @if (!empty($lesson->description))
                                                                <div class="cd-lesson__info-row">
                                                                    <span><i class="fa-solid fa-circle-info"></i> الوصف:</span>
                                                                    <span class="muted">{!! $lesson->description !!}</span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if ($isEnrolled)
                                                            <a style="cursor:pointer;"
                                                                @if (!$canView) onclick="showLessonError(event)" @endif
                                                                class="cd-action {{ $actionClass }} show-video-btn"
                                                                href="{{ $canView ? $url : 'javascript:void(0);' }}">
                                                                <i class="{{ $typeIcon }}"></i>
                                                                {{ $actionLabel }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Purchase card --}}
            <div class="col-lg-4 order-1 order-lg-2">
                <aside class="cd-card cd-buy">
                    <div class="cd-buy__media">
                        <img src="{{ $thumbnail }}" alt="{{ $course->title }}">
                    </div>
                    <div class="cd-buy__body">
                        @if ($isFree)
                            <div class="cd-price">
                                <div class="cd-price__free">كورس مجاني</div>
                            </div>
                        @else
                            <div class="cd-price">
                                <div>
                                    @if ($course->discount_flag == 1)
                                        <div class="cd-price__main">
                                            {{ $course->discount_price }}
                                            <small>{{ currency_symbol() }}</small>
                                        </div>
                                        <div class="cd-price__old">{{ currency($course->price) }}</div>
                                    @else
                                        <div class="cd-price__main">
                                            {{ number_format((float) $course->price, 2) }}
                                            <small>{{ currency_symbol() }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <ul class="cd-buy__list">
                            <li>
                                <span><i class="fa-regular fa-clock"></i> المحتوى</span>
                                <strong>+{{ $hours }}س {{ $minutes }}د</strong>
                            </li>
                            <li>
                                <span><i class="fa-regular fa-circle-question"></i> إجمالي الأسئلة</span>
                                <strong>+{{ $totalQuestions }} سؤال</strong>
                            </li>
                            <li>
                                <span><i class="fa-solid fa-layer-group"></i> الأقسام</span>
                                <strong>{{ $course->sections->count() }} قسم</strong>
                            </li>
                        </ul>

                        @if ($isEnrolled)
                            <button type="button" class="cd-cta" onclick="window.location='{{ $watchUrl }}'">
                                <i class="fa-solid fa-circle-play"></i>
                                @if (progress_bar($course->id) > 0)
                                    مشاهدة الكورس
                                @else
                                    البدء في مشاهدة الكورس
                                @endif
                            </button>
                        @elseif ($isFree)
                            <button type="button" class="cd-cta"
                                onclick="window.location.href='{{ route('payment.successFree', $course->id) }}'">
                                <i class="fa-solid fa-gift"></i>
                                ابدأ مجانًا
                            </button>
                        @else
                            <button type="button" class="cd-cta add-to-cart"
                                element-type="course" id-element="{{ $course->id }}">
                                <i class="fa-solid fa-cart-shopping"></i>
                                اشترك الآن
                            </button>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('frontend/js/student/main.js') }}" type="module"></script>
    <script src="{{ asset('frontend/js/student/quiz.js') }}" type="module"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: "نجاح!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "حسناً"
            });
        </script>
    @endif

    <script>
        function showLessonError(event) {
            event.preventDefault();
            Swal.fire({
                title: "خطأ!",
                text: "لا يمكن الدخول إلى الدرس، برجاء الرجوع إلى الدرس السابق",
                icon: "error",
                confirmButtonText: "حسناً"
            });
        }

        document.querySelectorAll('#courseCurriculum > .cd-acc__item').forEach(function (item) {
            const collapse = item.querySelector(':scope > .accordion-collapse');
            if (!collapse) return;
            collapse.addEventListener('show.bs.collapse', function () {
                item.classList.add('is-open');
            });
            collapse.addEventListener('hide.bs.collapse', function () {
                item.classList.remove('is-open');
            });
        });
    </script>
    @include('theme::includes.addCart')
@endsection

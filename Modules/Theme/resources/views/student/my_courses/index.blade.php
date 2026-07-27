@extends('theme::layouts.master')

@push('title', get_phrase('كورساتي'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/my-courses-modern.css') }}">
@endpush

@section('content')
@php
    \Carbon\Carbon::setLocale('ar');
    $totalCourses = $my_courses->total();
@endphp

<section class="myCourses main_content mc-page" dir="rtl">
    <div class="profile-banner-area"></div>
    <div class="container profile-banner-area-container">
        <div class="row">
            @include('theme::student.left_sidebar')

            <div class="col-lg-9">
                <div class="mc-header">
                    <div class="mc-header__intro">
                        <div class="mc-header__icon" aria-hidden="true">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h1 class="mc-header__title">{{ get_phrase('كورساتي') }}</h1>
                            <p class="mc-header__sub">
                                {{ get_phrase('تابع تقدّمك واستكمل التعلم من حيث توقفت في أي كورس.') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if ($totalCourses > 0)
                    <div class="mc-stats">
                        <div class="mc-stat">
                            <span class="mc-stat__icon"><i class="fa-solid fa-layer-group"></i></span>
                            <div>
                                <span class="mc-stat__label">{{ get_phrase('إجمالي الكورسات') }}</span>
                                <span class="mc-stat__value">{{ $totalCourses }}</span>
                            </div>
                        </div>
                        <div class="mc-stat mc-stat--accent">
                            <span class="mc-stat__icon"><i class="fa-solid fa-play"></i></span>
                            <div>
                                <span class="mc-stat__label">{{ get_phrase('في هذه الصفحة') }}</span>
                                <span class="mc-stat__value">{{ $my_courses->count() }}</span>
                            </div>
                        </div>
                        <div class="mc-stat mc-stat--soft">
                            <span class="mc-stat__icon"><i class="fa-solid fa-chart-line"></i></span>
                            <div>
                                <span class="mc-stat__label">{{ get_phrase('استمر في التعلم') }}</span>
                                <span class="mc-stat__value mc-stat__value--sm">{{ get_phrase('كل يوم خطوة') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mc-grid">
                        @foreach ($my_courses as $course)
                            @php
                                $course_progress = (float) progress_bar($course->course_id);
                                $progressPct = max(0, min(100, $course_progress));
                                $progressLabel = rtrim(rtrim(number_format($progressPct, 1, '.', ''), '0'), '.');

                                if ($progressPct >= 100) {
                                    $progressState = 'done';
                                    $progressText = get_phrase('مكتمل');
                                } elseif ($progressPct > 0) {
                                    $progressState = 'active';
                                    $progressText = get_phrase('جاري التعلم');
                                } else {
                                    $progressState = 'idle';
                                    $progressText = get_phrase('لم يبدأ');
                                }

                                $watch_history = App\Models\Watch_history::where('course_id', $course->course_id)
                                    ->where('student_id', auth()->user()->id)
                                    ->first();
                                $lesson = App\Models\Lesson::where('course_id', $course->course_id)
                                    ->orderBy('sort', 'asc')
                                    ->first();
                                $lesson_id = null;

                                if ($watch_history && $watch_history->watching_lesson_id) {
                                    $lesson_id = $watch_history->watching_lesson_id;
                                } elseif ($lesson) {
                                    $lesson_id = $lesson->id;
                                }

                                $url = $lesson_id
                                    ? route('course.player', ['slug' => $course->slug, 'id' => $lesson_id])
                                    : route('course.player', ['slug' => $course->slug]);

                                $enroll_status = enroll_status($course->course_id, auth()->user()->id);
                                $createdAt = \Carbon\Carbon::parse($course->created_at)->isoFormat('D MMMM YYYY');
                                $updatedAt = \Carbon\Carbon::parse(lastUpdate($course->course_id))->isoFormat('D MMMM YYYY');
                            @endphp

                            <article class="mc-card mc-card--{{ $progressState }}">
                                <a href="{{ route('theme.course.details', $course->course_id) }}" class="mc-card__media">
                                    <img
                                        src="{{ get_image($course->thumbnail ?? '') }}"
                                        alt="{{ $course->title }}"
                                        loading="lazy"
                                    >
                                    <span class="mc-card__badge mc-card__badge--{{ $progressState }}">
                                        {{ $progressText }}
                                    </span>
                                    <span class="mc-card__progress-ring" aria-hidden="true">
                                        {{ $progressLabel }}%
                                    </span>
                                </a>

                                <div class="mc-card__body">
                                    <div class="mc-card__meta">
                                        <span>
                                            <i class="fa-solid fa-user"></i>
                                            {{ $course->user_name }}
                                        </span>
                                    </div>

                                    <h2 class="mc-card__title">
                                        <a href="{{ route('theme.course.details', $course->course_id) }}">
                                            {{ $course->title }}
                                        </a>
                                    </h2>

                                    <div class="mc-progress" role="progressbar"
                                        aria-valuenow="{{ $progressLabel }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-label="{{ get_phrase('نسبة الإنجاز') }}">
                                        <div class="mc-progress__head">
                                            <span>{{ get_phrase('التقدّم') }}</span>
                                            <strong>{{ $progressLabel }}%</strong>
                                        </div>
                                        <div class="mc-progress__track">
                                            <div class="mc-progress__bar" style="width: {{ $progressPct }}%"></div>
                                        </div>
                                    </div>

                                    <div class="mc-card__dates">
                                        <span>
                                            <i class="fa-regular fa-calendar"></i>
                                            {{ get_phrase('الإنشاء') }}: {{ $createdAt }}
                                        </span>
                                        <span>
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                            {{ get_phrase('آخر تحديث') }}: {{ $updatedAt }}
                                        </span>
                                    </div>

                                    <div class="mc-card__actions">
                                        @if ($enroll_status == 'free_locked')
                                            <a class="mc-btn mc-btn--primary" href="{{ route('theme.course.details', $course->course_id) }}">
                                                <i class="fa-solid fa-lock"></i>
                                                {{ get_phrase('اشترِ للوصول') }}
                                            </a>
                                        @elseif ($enroll_status == 'expired')
                                            <a class="mc-btn mc-btn--warn" href="{{ route('theme.purchase.course', ['course_id' => $course->course_id]) }}">
                                                <i class="fa-solid fa-rotate"></i>
                                                {{ get_phrase('تجديد الاشتراك') }}
                                            </a>
                                        @elseif ($enroll_status == 'valid')
                                            <a class="mc-btn mc-btn--primary" href="{{ $url }}">
                                                <i class="fa-solid fa-{{ $progressPct > 0 ? 'play' : 'bolt' }}"></i>
                                                {{ $progressPct > 0 ? get_phrase('استكمال التعلم') : get_phrase('ابدأ الآن') }}
                                            </a>
                                        @endif

                                        <a class="mc-btn mc-btn--ghost" href="{{ route('theme.course.details', $course->course_id) }}">
                                            {{ get_phrase('التفاصيل') }}
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($my_courses->hasPages())
                        <div class="mc-pagination">
                            {{ $my_courses->links() }}
                        </div>
                    @endif
                @else
                    <div class="mc-empty">
                        <div class="mc-empty__icon" aria-hidden="true">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <h2>{{ get_phrase('لا توجد كورسات بعد') }}</h2>
                        <p>{{ get_phrase('لما تشترك في كورس هيظهر هنا عشان تقدر تتابع تقدّمك بسهولة.') }}</p>
                        <a href="{{ route('theme.home') }}" class="mc-btn mc-btn--primary">
                            <i class="fa-solid fa-compass"></i>
                            {{ get_phrase('استكشف الكورسات') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

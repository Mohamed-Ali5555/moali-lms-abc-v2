@extends('layouts.admin')

@push('title', get_phrase('Enrolled Courses') . ' - ' . ($student->name ?? ''))

@push('meta')
@endpush

@push('css')
@endpush

@section('content')
    @php
        $coursesCount = $courses->count();
    @endphp

    <div class="admin-page student-courses">
        <section class="student-courses__hero">
            <div class="student-courses__hero-main">
                <div class="student-courses__avatar">
                    <img src="{{ get_image($student->photo) }}" alt="{{ $student->name }}">
                </div>
                <div class="student-courses__identity">
                    <p class="student-courses__eyebrow">
                        <i class="fi-rr-graduation-cap"></i>
                        {{ get_phrase('Student enrollments') }}
                    </p>
                    <h1 class="student-courses__name">{{ $student->name }}</h1>
                    <div class="student-courses__meta">
                        @if (!empty($student->email))
                            <span><i class="fi-rr-envelope"></i>{{ $student->email }}</span>
                        @endif
                        @if (!empty($student->phone))
                            <span><i class="fi-rr-phone-call"></i>{{ $student->phone }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="student-courses__hero-actions">
                <a href="{{ route('admin.student.index') }}" class="admin-btn admin-btn--ghost">
                    <i class="fi-rr-arrow-left"></i>
                    <span>{{ get_phrase('Back to students') }}</span>
                </a>
                @if (has_permission('admin.student.edit'))
                    <a href="{{ route('admin.student.edit', $student->id) }}" class="admin-btn admin-btn--primary">
                        <i class="fi-rr-edit"></i>
                        <span>{{ get_phrase('Edit student') }}</span>
                    </a>
                @endif
            </div>
        </section>

        <div class="student-courses__stats">
            <article class="student-courses__stat">
                <span class="student-courses__stat-icon student-courses__stat-icon--courses">
                    <i class="fi-rr-e-learning"></i>
                </span>
                <div>
                    <p class="student-courses__stat-value">{{ $coursesCount }}</p>
                    <p class="student-courses__stat-label">{{ get_phrase('Enrolled courses') }}</p>
                </div>
            </article>
            <article class="student-courses__stat">
                <span class="student-courses__stat-icon student-courses__stat-icon--progress">
                    <i class="fi-rr-chart-histogram"></i>
                </span>
                <div>
                    <p class="student-courses__stat-value">{{ $avgProgress }}%</p>
                    <p class="student-courses__stat-label">{{ get_phrase('Average progress') }}</p>
                </div>
            </article>
            <article class="student-courses__stat">
                <span class="student-courses__stat-icon student-courses__stat-icon--lessons">
                    <i class="fi-rr-checkbox"></i>
                </span>
                <div>
                    <p class="student-courses__stat-value">{{ $completedLessons }}/{{ $totalLessons }}</p>
                    <p class="student-courses__stat-label">{{ get_phrase('Lessons completed') }}</p>
                </div>
            </article>
        </div>

        @if ($coursesCount > 0)
            <div class="student-courses__grid">
                @foreach ($courses as $index => $enrol)
                    @php
                        $course = $enrol->course;
                        $progress = (float) progress_bar_admin($course->id, $enrol->user_id);
                        $progressRounded = min(100, max(0, round($progress)));
                        $completedIds = $completedByCourse[$course->id] ?? [];
                        $lessons = $course->lessons ?? collect();
                        $doneCount = $lessons->filter(fn ($lesson) => in_array((string) $lesson->id, $completedIds, true))->count();
                        $lessonTotal = $lessons->count();

                        if ($progressRounded >= 100) {
                            $statusKey = 'completed';
                            $statusLabel = get_phrase('Completed');
                        } elseif ($progressRounded > 0) {
                            $statusKey = 'progress';
                            $statusLabel = get_phrase('In progress');
                        } else {
                            $statusKey = 'new';
                            $statusLabel = get_phrase('Not started');
                        }

                        $hasDiscount = (float) ($course->discount_price ?? 0) > 0;
                        $enrolledAt = optional($enrol->created_at)->format('Y-m-d');
                    @endphp

                    <article class="student-course-card" style="--delay: {{ min($index, 8) * 0.05 }}s">
                        <div class="student-course-card__media">
                            <img src="{{ get_image($course->thumbnail ?? '') }}" alt="{{ $course->title }}">
                            <span class="student-course-card__status student-course-card__status--{{ $statusKey }}">
                                {{ $statusLabel }}
                            </span>
                            <div class="student-course-card__progress-ring" aria-label="{{ $progressRounded }}%">
                                <svg viewBox="0 0 36 36" aria-hidden="true">
                                    <path class="student-course-card__ring-bg"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path class="student-course-card__ring-fill"
                                        stroke-dasharray="{{ $progressRounded }}, 100"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <span>{{ $progressRounded }}%</span>
                            </div>
                        </div>

                        <div class="student-course-card__body">
                            <div class="student-course-card__topline">
                                @if ($course->category)
                                    <span class="student-course-card__category">{{ $course->category->title }}</span>
                                @endif
                                @if ($enrolledAt)
                                    <span class="student-course-card__date">
                                        <i class="fi-rr-calendar"></i>
                                        {{ $enrolledAt }}
                                    </span>
                                @endif
                            </div>

                            <h2 class="student-course-card__title">{{ $course->title }}</h2>

                            <div class="student-course-card__price">
                                @if ($hasDiscount)
                                    <strong>{{ number_format((float) $course->discount_price, 2) }} L.E</strong>
                                    <del>{{ number_format((float) $course->price, 2) }} L.E</del>
                                @elseif ((float) ($course->price ?? 0) > 0)
                                    <strong>{{ number_format((float) $course->price, 2) }} L.E</strong>
                                @else
                                    <strong class="is-free">{{ get_phrase('Free') }}</strong>
                                @endif
                            </div>

                            <div class="student-course-card__progress">
                                <div class="student-course-card__progress-meta">
                                    <span>{{ get_phrase('Progress') }}</span>
                                    <span>{{ $doneCount }}/{{ $lessonTotal }} {{ get_phrase('lessons') }}</span>
                                </div>
                                <div class="student-course-card__bar" role="progressbar"
                                    aria-valuenow="{{ $progressRounded }}" aria-valuemin="0" aria-valuemax="100">
                                    <span style="width: {{ $progressRounded }}%"></span>
                                </div>
                            </div>

                            <details class="student-course-card__lessons" @if ($index === 0) open @endif>
                                <summary>
                                    <span>
                                        <i class="fi-rr-list"></i>
                                        {{ get_phrase('Course lessons') }}
                                        <em>({{ $lessonTotal }})</em>
                                    </span>
                                    <i class="fi-rr-angle-small-down student-course-card__chevron"></i>
                                </summary>

                                <ul class="student-course-card__lesson-list">
                                    @forelse ($lessons as $lesson)
                                        @php
                                            $isDone = in_array((string) $lesson->id, $completedIds, true);
                                        @endphp
                                        <li class="{{ $isDone ? 'is-done' : '' }}">
                                            <span class="student-course-card__lesson-icon">
                                                @if ($isDone)
                                                    <i class="fi-rr-check"></i>
                                                @else
                                                    <i class="fi-rr-play-alt"></i>
                                                @endif
                                            </span>
                                            <span class="student-course-card__lesson-title">{{ $lesson->title }}</span>
                                            @if (!empty($lesson->duration))
                                                <span class="student-course-card__lesson-duration">{{ $lesson->duration }}</span>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="is-empty">{{ get_phrase('No lessons yet') }}</li>
                                    @endforelse
                                </ul>
                            </details>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="student-courses__empty">
                @include('admin.no_data')
            </div>
        @endif
    </div>
@endsection

@push('js')
@endpush

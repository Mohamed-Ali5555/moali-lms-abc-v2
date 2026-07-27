@php
    $sections = App\Models\Section::where('course_id', $course_details->id)
        ->orderBy('sort')
        ->get();

    $completed_lesson = json_decode(
        App\Models\Watch_history::where('course_id', $course_details->id)
            ->where('student_id', Auth()->user()->id)
            ->value('completed_lesson'),
        true,
    ) ?? [];

    $active_section = App\Models\Lesson::where('id', $history->watching_lesson_id ?? '')->value('section_id');

    $lesson_history = App\Models\Watch_history::where('course_id', $course_details->id)
        ->where('student_id', auth()->user()->id)
        ->firstOrNew();
    $completed_lesson_arr = json_decode($lesson_history->completed_lesson, true);
    $completed_lesson_arr = is_array($completed_lesson_arr) ? $completed_lesson_arr : [];
    $complated_lesson = count($completed_lesson_arr);
    $total_lessons = lesson_count($course_details->id);
    $course_progress_out_of_100 = progress_bar($course_details->id);

    $user_id = Auth()->user()->id;
    $is_course_instructor = is_course_instructor($course_details->id, $user_id);

    $is_locked = 0;
    $locked_lesson_ids = [];
    $progress_offset = 106.81 - (106.81 * ($course_progress_out_of_100 / 100));
@endphp

<div class="course-content-playlist psb-sidebar">
    <div class="psb-header">
        <div class="psb-header__top">
            <div class="psb-header__titles">
                <span class="psb-header__eyebrow">{{ get_phrase('محتوى الكورس') }}</span>
                <h3 class="psb-header__title heading">{{ ucfirst($course_details->title) }}</h3>
            </div>

            <div class="psb-progress course-progress" aria-label="{{ $course_progress_out_of_100 }}%">
                <svg width="52" height="52" viewBox="0 0 40 40" aria-hidden="true">
                    <circle cx="20" cy="20" r="17" class="psb-progress__track" fill="none"
                        stroke-width="3.5" />
                    <circle cx="20" cy="20" r="17" class="psb-progress__bar" fill="none"
                        stroke-width="3.5" stroke-dasharray="106.81"
                        stroke-dashoffset="{{ $progress_offset }}" transform="rotate(-90 20 20)"
                        stroke-linecap="round" />
                    <text x="50%" y="50%" text-anchor="middle" dy=".35em" class="psb-progress__pct">
                        {{ (int) $course_progress_out_of_100 }}%
                    </text>
                </svg>
            </div>
        </div>

        <div class="psb-header__meta">
            <span class="psb-chip">
                <strong>{{ $complated_lesson }}</strong>
                {{ get_phrase('من') }}
                <strong>{{ $total_lessons }}</strong>
                {{ get_phrase('درس') }}
            </span>
            <div class="psb-bar" role="progressbar" aria-valuenow="{{ (int) $course_progress_out_of_100 }}"
                aria-valuemin="0" aria-valuemax="100">
                <span style="width: {{ min(100, max(0, $course_progress_out_of_100)) }}%"></span>
            </div>
        </div>
    </div>

    <div class="course-playlist-accordion psb-accordion" dir="rtl">
        <div class="accordion course-accordion" id="coursePlay">
            @foreach ($sections as $section)
                @php
                    $lessons = App\Models\Lesson::where(['section_id' => $section->id])
                        ->Active()
                        ->orderBy('sort')
                        ->get();
                    $section_done = $lessons->filter(fn($l) => in_array($l->id, $completed_lesson_arr))->count();
                    $section_total = $lessons->count();
                    $is_open = $active_section == $section->id;
                @endphp

                <div class="accordion-item psb-section my-2 p-1 shadow rounded-1 border-0">
                    <h2 class="accordion-header">
                        <button
                            class="accordion-button psb-section__btn gap-2 @if (!$is_open) collapsed @endif"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse_{{ $section->id }}"
                            aria-expanded="{{ $is_open ? 'true' : 'false' }}"
                            aria-controls="collapse_{{ $section->id }}">
                            <span class="psb-section__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                            </span>
                            <span class="psb-section__text">
                                <span class="psb-section__title">{{ ucfirst($section->title) }}</span>
                                <span class="psb-section__count">{{ $section_done }}/{{ $section_total }}</span>
                            </span>
                        </button>
                    </h2>

                    <div id="collapse_{{ $section->id }}"
                        class="accordion-collapse collapse @if ($is_open) show @endif"
                        data-bs-parent="#coursePlay">
                        <div class="accordion-body psb-section__body">
                            <ul class="coourse-playlist-list psb-list p-0 m-0">
                                @foreach ($lessons as $key => $lesson)
                                    @php
                                        $type = $lesson->lesson_type;
                                        $is_watching = isset($history->watching_lesson_id) && $lesson->id == $history->watching_lesson_id;
                                        $is_done = in_array($lesson->id, $completed_lesson_arr);
                                        $duration_label = lesson_durations($lesson->id);
                                        $has_duration = $duration_label !== '0 دقيقة' && $duration_label !== '00:00:00';
                                        $is_video = in_array($type, ['video-url', 'system-video', 'vimeo-url', 'google_drive']);
                                        $is_doc = in_array($type, ['text', 'document_type', 'iframe', 'DocumentFile']);
                                    @endphp

                                    <li
                                        class="coourse-playlist-item psb-lesson @if ($is_watching) active @else lock @endif @if ($is_done) is-done @endif @if ($is_locked) is-locked @endif">
                                        <div class="d-flex flex-grow-1 align-items-center psb-lesson__main">
                                            <a href="{{ route('course.player', ['slug' => $course_details->slug, 'id' => $lesson->id]) }}"
                                                class="d-flex flex-grow-1 align-items-center psb-lesson__link"
                                                @if ($is_locked) style="pointer-events:none;opacity:.55" @endif>
                                                <div class="play-lock-number psb-lesson__type">
                                                    <span>
                                                        @if ($is_doc)
                                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <polyline points="14 2 14 8 20 8"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        @elseif ($is_video)
                                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"
                                                                class="video_icon">
                                                                <rect x="2" y="5" width="14" height="14"
                                                                    rx="2" stroke="currentColor" stroke-width="2" />
                                                                <path d="M16 10l6-3v10l-6-3V10z" stroke="currentColor"
                                                                    stroke-width="2" stroke-linejoin="round" />
                                                            </svg>
                                                        @elseif ($type == 'Link')
                                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        @elseif ($type == 'image')
                                                            <i class="fa-solid fa-image"></i>
                                                        @else
                                                            @if ($lesson->type == 1)
                                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"
                                                                    class="quiz_icon">
                                                                    <path d="M9 11l3 3L22 4" stroke="currentColor"
                                                                        stroke-width="2" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            @else
                                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"
                                                                    class="homework_icon">
                                                                    <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>

                                                <div class="flex-grow-1 psb-lesson__content">
                                                    <div class="check-title-area align-items-center">
                                                        <p class="d-none">{{ $lesson->lesson_type }}</p>
                                                        <span class="video-title">{{ $lesson->title }}</span>
                                                    </div>

                                                    <div class="psb-lesson__meta">
                                                        @if ($is_watching)
                                                            <span class="psb-lesson__now">{{ get_phrase('قيد المشاهدة') }}</span>
                                                        @endif
                                                        @if ($has_duration)
                                                            <small class="duration">{{ $duration_label }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="me-auto psb-lesson__status">
                                            @if ($is_locked)
                                                <i class="fas fa-lock" title="{{ get_phrase('Complete previous lesson to unlock it') }}"
                                                    data-bs-toggle="tooltip"></i>
                                            @elseif ($is_done)
                                                <i class="fas fa-check-circle checkbox-icon"
                                                    title="{{ get_phrase('Lesson completed') }}"></i>
                                            @elseif ($is_video)
                                                <i class="form-check-input flexCheckChecked mt-0"
                                                    title="{{ get_phrase('Play Now') }}"></i>
                                            @elseif (!$course_details->enable_drip_content)
                                                <input class="form-check-input flexCheckChecked mt-0"
                                                    @if (in_array($lesson->id, $completed_lesson)) checked @endif
                                                    type="checkbox" id="{{ $lesson->id }}">
                                            @else
                                                <span class="psb-lesson__dot" aria-hidden="true"></span>
                                            @endif
                                        </div>
                                    </li>

                                    @php
                                        if ($is_locked) {
                                            $locked_lesson_ids[] = $lesson->id;
                                        }

                                        if (
                                            !in_array($lesson->id, $completed_lesson_arr) &&
                                            !$is_locked &&
                                            $course_details->enable_drip_content == 1 &&
                                            auth()->user() &&
                                            !$is_course_instructor
                                        ) {
                                            $is_locked = 1;
                                        }
                                    @endphp
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<form class="ajaxForm" action="{{ route('set.watch.history') }}" method="post" id="watch_history_form">
    @csrf
    <input type="hidden" class="course_id" name="course_id" value="{{ $course_details->id }}">
    <input type="hidden" class="lesson_id" name="lesson_id">
</form>

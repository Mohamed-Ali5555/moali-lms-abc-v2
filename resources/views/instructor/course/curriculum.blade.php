<div class="curriculum-board w-100">
    <div class="curriculum-toolbar">
        <div class="curriculum-toolbar__group">
            <span class="curriculum-toolbar__label">{{ get_phrase('Build') }}</span>
            <div class="curriculum-toolbar__actions">
                <a href="#"
                    onclick="ajaxModal('{{ route('modal', ['instructor.course.create_section', 'id' => $course_details->id]) }}', '{{ get_phrase('Add new section') }}', 'modal-md', '')"
                    class="cur-btn cur-btn--ghost">
                    <i class="fi-rr-apps"></i>
                    <span>{{ get_phrase('Add section') }}</span>
                </a>

                @if ($sections->count() > 0)
                    <a href="#"
                        onclick="ajaxModal('{{ route('modal', ['instructor.course.lesson_type', 'id' => $course_details->id]) }}', '{{ get_phrase('Add new lesson') }}', 'modal-lg', '')"
                        class="cur-btn cur-btn--primary">
                        <i class="fi-rr-plus"></i>
                        <span>{{ get_phrase('Add lesson') }}</span>
                    </a>

                    <a href="#"
                        onclick="ajaxModal('{{ route('modal', ['instructor.quiz.create', 'id' => $course_details->id]) }}', '{{ get_phrase('Add new quiz') }}', 'modal-lg', '')"
                        class="cur-btn cur-btn--ghost">
                        <i class="fi-rr-clipboard-list-check"></i>
                        <span>{{ get_phrase('Add quiz') }}</span>
                    </a>
                @endif
            </div>
        </div>

        @if ($sections->count() > 0)
            <div class="curriculum-toolbar__group">
                <span class="curriculum-toolbar__label">{{ get_phrase('Organize') }}</span>
                <div class="curriculum-toolbar__actions">
                    <a href="#"
                        onclick="ajaxModal('{{ route('modal', ['instructor.course.section_sort', 'id' => $course_details->id]) }}', '{{ get_phrase('Sort sections') }}', 'modal-md', '')"
                        class="cur-btn cur-btn--soft">
                        <i class="fi-rr-sort-alt"></i>
                        <span>{{ get_phrase('Sort Section') }}</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <ul class="ol-my-accordion curriculum-sections">
        @forelse ($sections as $key => $section)
            @php
                $lessons = DB::table('lessons')
                    ->join('sections', 'lessons.section_id', 'sections.id')
                    ->select('lessons.*', 'sections.title as section_title')
                    ->where('lessons.section_id', $section->id)
                    ->orderBy('sort')
                    ->get();
                $sectionNum = $key + 1;
            @endphp

            <li class="cur-section single-accor-item">
                <div class="cur-section__head accordion-btn-wrap">
                    <div class="cur-section__title-wrap accordion-btn-title">
                        <span class="cur-section__num">{{ $sectionNum }}</span>
                        <h4 class="cur-section__title title">{{ $section->title }}</h4>
                        <span class="cur-section__count">{{ $lessons->count() }}</span>
                    </div>
                    <div class="cur-section__actions accordion-button-buttons" onclick="event.stopPropagation();">
                        @if ($lessons->count() > 0)
                            <a href="#"
                                onclick="ajaxModal('{{ route('modal', ['instructor.course.lesson_sort', 'id' => $section->id]) }}', '{{ get_phrase('Sort lessons') }}', 'modal-md', ''); event.stopPropagation();"
                                class="cur-chip">
                                <i class="fi-rr-sort-alt"></i>
                                {{ get_phrase('Sort Lessons') }}
                            </a>
                        @endif
                        <a href="#" data-bs-toggle="tooltip" title="{{ get_phrase('Edit section') }}"
                            onclick="ajaxModal('{{ route('modal', ['instructor.course.section_edit', 'id' => $section->id]) }}', '{{ get_phrase('Edit section') }}', 'modal-md', ''); event.stopPropagation();"
                            class="cur-icon-btn">
                            <span class="fi-rr-pencil"></span>
                        </a>
                        <a href="#" data-bs-toggle="tooltip" title="{{ get_phrase('Delete section') }}"
                            onclick="confirmModal('{{ route('instructor.section.delete', $section->id) }}'); event.stopPropagation();"
                            class="cur-icon-btn cur-icon-btn--danger">
                            <span class="fi-rr-trash"></span>
                        </a>
                    </div>
                    <span class="cur-section__chevron fi-rr-angle-small-down"></span>
                </div>

                <div class="cur-section__body accoritem-body d-hidden">
                    @if ($lessons->count() > 0)
                        <ul class="cur-lessons list-group-3">
                            @foreach ($lessons as $lesson)
                                @php
                                    $isQuiz = $lesson->lesson_type == 'quiz' && ($lesson->type ?? 1) == 1;
                                    $isAssignment = $lesson->lesson_type == 'quiz' && ($lesson->type ?? 1) == 2;
                                    $itemType = $isQuiz ? 'quiz' : ($isAssignment ? 'assignment' : 'lesson');
                                @endphp
                                <li class="cur-lesson cur-lesson--{{ $itemType }}">
                                    <div class="cur-lesson__main">
                                        <span class="cur-lesson__icon cur-lesson__icon--{{ $itemType }}">
                                            @if ($isQuiz)
                                                <i class="fi-rr-clipboard-list-check"></i>
                                            @elseif ($isAssignment)
                                                <i class="fi-rr-document-signed"></i>
                                            @else
                                                <i class="fi-rr-play-alt"></i>
                                            @endif
                                        </span>
                                        <div class="cur-lesson__meta">
                                            <h4 class="cur-lesson__title title">{{ $lesson->title }}</h4>
                                            <span class="cur-lesson__type">
                                                @if ($isQuiz)
                                                    {{ get_phrase('Quiz') }}
                                                @elseif ($isAssignment)
                                                    {{ get_phrase('Assignment') }}
                                                @else
                                                    {{ get_phrase('Lesson') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <div class="cur-lesson__actions buttons">
                                        @if ($lesson->lesson_type == 'quiz')
                                            <a href="#" data-bs-toggle="tooltip" title="{{ get_phrase('Result') }}"
                                                onclick="ajaxModal('{{ route('modal', ['instructor.quiz_result.index', 'id' => $lesson->id]) }}', '{{ get_phrase('Result') }}', 'modal-xl', '')"
                                                class="cur-icon-btn">
                                                <span class="fi fi-rr-clipboard-list-check"></span>
                                            </a>
                                            <a href="#" data-bs-toggle="tooltip" title="{{ get_phrase('Questions') }}"
                                                onclick="ajaxModal('{{ route('modal', ['instructor.questions.index', 'id' => $lesson->id]) }}', '{{ get_phrase('Questions') }}', 'modal-xl', '')"
                                                class="cur-icon-btn">
                                                <span class="fi fi-rr-poll-h"></span>
                                            </a>
                                            <a href="#" data-bs-toggle="tooltip" title="{{ get_phrase('Edit quiz') }}"
                                                onclick="ajaxModal('{{ route('modal', ['instructor.quiz.edit', 'id' => $lesson->id]) }}', '{{ get_phrase('Edit quiz') }}', 'modal-lg', '')"
                                                class="cur-icon-btn">
                                                <span class="fi-rr-pencil"></span>
                                            </a>
                                        @endif

                                        @if ($lesson->lesson_type != 'quiz')
                                            <a href="#" data-bs-toggle="tooltip" title="{{ get_phrase('Edit lesson') }}"
                                                onclick="ajaxModal('{{ route('modal', ['instructor.course.lesson_edit', 'id' => $lesson->id]) }}', '{{ get_phrase('Edit lesson') }}', 'modal-lg', '')"
                                                class="cur-icon-btn">
                                                <span class="fi-rr-pencil"></span>
                                            </a>
                                        @endif

                                        <a href="#" data-bs-toggle="tooltip" title="{{ get_phrase('Delete lesson') }}"
                                            onclick="confirmModal('{{ route('instructor.lesson.delete', $lesson->id) }}')"
                                            class="cur-icon-btn cur-icon-btn--danger">
                                            <span class="fi-rr-trash"></span>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="cur-empty-lessons">
                            <i class="fi-rr-inbox"></i>
                            <p>{{ get_phrase('No lessons are available.') }}</p>
                        </div>
                    @endif
                </div>
            </li>
        @empty
            <li class="cur-empty-state">
                <a onclick="ajaxModal('{{ route('modal', ['instructor.course.create_section', 'id' => $course_details->id]) }}', '{{ get_phrase('Add new section') }}', 'modal-md', '')"
                    href="#" class="cur-empty-state__card">
                    <span class="cur-empty-state__icon"><i class="fi-rr-add"></i></span>
                    <h3>{{ get_phrase('Add a new Section') }}</h3>
                    <p>{{ get_phrase('Start building your course curriculum') }}</p>
                </a>
            </li>
        @endforelse
    </ul>
</div>

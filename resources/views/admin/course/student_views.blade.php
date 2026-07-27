@php
    $enrolledCount = $enrolledStudents->count();
    $sectionsCount = count($sectionsData);
@endphp

<div class="sv-modal tf-modal-form">
    <div class="sv-modal__banner">
        <div>
            <p class="sv-modal__eyebrow">{{ get_phrase('تتبع التقدم') }}</p>
            <h5 class="sv-modal__title">{{ get_phrase('مشاهدات الطلبة') }}</h5>
            <p class="sv-modal__desc">{{ get_phrase('اختر درساً أو اختباراً لعرض من شاهده ومن لم يشاهده') }}</p>
        </div>
        <div class="sv-modal__meta">
            <div class="sv-modal__course">
                <span>{{ get_phrase('الكورس') }}</span>
                <strong>{{ $course->title }}</strong>
            </div>
            <div class="sv-modal__stats">
                <div class="sv-modal__stat">
                    <strong>{{ $enrolledCount }}</strong>
                    <span>{{ get_phrase('مشترك') }}</span>
                </div>
                <div class="sv-modal__stat">
                    <strong>{{ $sectionsCount }}</strong>
                    <span>{{ get_phrase('قسم') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="sv-modal__toolbar">
        <div class="sv-seg" role="group" aria-label="{{ get_phrase('نوع العرض') }}">
            <button type="button" class="sv-seg__btn is-active" id="svToggleViewed" data-viewed="1">
                <i class="fi-rr-eye"></i>
                <span>{{ get_phrase('المشاهدين') }}</span>
            </button>
            <button type="button" class="sv-seg__btn" id="svToggleNotViewed" data-viewed="0">
                <i class="fi-rr-eye-crossed"></i>
                <span>{{ get_phrase('غير المشاهدين') }}</span>
            </button>
        </div>
        <p class="sv-modal__hint" id="svHint">
            {{ get_phrase('اضغط على عنصر من المنهج لعرض القائمة') }}
        </p>
    </div>

    <div class="sv-modal__grid">
        <aside class="sv-curriculum">
            @if (count($sectionsData) > 0)
                @foreach ($sectionsData as $sectionIndex => $sectionData)
                    @php
                        $itemsCount = $sectionData['lessons']->count()
                            + $sectionData['quizzes']->count()
                            + $sectionData['assignments']->count();
                    @endphp
                    <details class="sv-section" @if ($sectionIndex === 0) open @endif>
                        <summary class="sv-section__head">
                            <span class="sv-section__icon"><i class="fi-rr-folder"></i></span>
                            <span class="sv-section__info">
                                <strong>{{ $sectionData['section']->title }}</strong>
                                <small>{{ $itemsCount }} {{ get_phrase('عنصر') }}</small>
                            </span>
                            <i class="fi-rr-angle-small-down sv-section__chevron"></i>
                        </summary>

                        <div class="sv-section__body">
                            @foreach ($sectionData['lessons'] as $lesson)
                                <button type="button"
                                    class="sv-item sv-item--lesson"
                                    data-item-id="{{ $lesson->id }}"
                                    data-type="lesson"
                                    data-title="{{ $lesson->title }}">
                                    <span class="sv-item__icon"><i class="fi-rr-play"></i></span>
                                    <span class="sv-item__text">
                                        <strong>{{ $lesson->title }}</strong>
                                        <small>{{ get_phrase('درس') }}</small>
                                    </span>
                                    <i class="fi-rr-angle-left sv-item__arrow"></i>
                                </button>
                            @endforeach

                            @foreach ($sectionData['quizzes'] as $quiz)
                                <button type="button"
                                    class="sv-item sv-item--quiz"
                                    data-item-id="{{ $quiz->id }}"
                                    data-type="quiz"
                                    data-title="{{ $quiz->title }}">
                                    <span class="sv-item__icon"><i class="fi-rr-clipboard"></i></span>
                                    <span class="sv-item__text">
                                        <strong>{{ $quiz->title }}</strong>
                                        <small>{{ get_phrase('اختبار') }}</small>
                                    </span>
                                    <i class="fi-rr-angle-left sv-item__arrow"></i>
                                </button>
                            @endforeach

                            @foreach ($sectionData['assignments'] as $assignment)
                                <button type="button"
                                    class="sv-item sv-item--assignment"
                                    data-item-id="{{ $assignment->id }}"
                                    data-type="assignment"
                                    data-title="{{ $assignment->title }}">
                                    <span class="sv-item__icon"><i class="fi-rr-file"></i></span>
                                    <span class="sv-item__text">
                                        <strong>{{ $assignment->title }}</strong>
                                        <small>{{ get_phrase('واجب') }}</small>
                                    </span>
                                    <i class="fi-rr-angle-left sv-item__arrow"></i>
                                </button>
                            @endforeach
                        </div>
                    </details>
                @endforeach
            @else
                <div class="sv-empty">
                    <i class="fi-rr-folder-open"></i>
                    <p>{{ get_phrase('لا توجد أقسام في هذا الكورس') }}</p>
                </div>
            @endif
        </aside>

        <section class="sv-panel" id="studentsList">
            <div class="sv-panel__head">
                <div>
                    <h6 class="sv-panel__title" id="studentsListTitle">{{ get_phrase('قائمة الطلبة') }}</h6>
                    <p class="sv-panel__subtitle" id="studentsListSubtitle">{{ get_phrase('لم يتم اختيار عنصر بعد') }}</p>
                </div>
                <span class="sv-panel__badge" id="studentsCountBadge" hidden>0</span>
            </div>
            <div class="sv-panel__body" id="studentsListContent">
                <div class="sv-placeholder">
                    <div class="sv-placeholder__icon"><i class="fi-rr-users"></i></div>
                    <strong>{{ get_phrase('اختر عنصراً من المنهج') }}</strong>
                    <p>{{ get_phrase('ستظهر هنا قائمة الطلبة حسب حالة المشاهدة') }}</p>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
(function () {
    let showViewed = true;
    let currentItemId = null;
    let currentItemType = null;
    let currentItemTitle = '';

    const phrases = {
        viewedLesson: @json(get_phrase('طلبة شاهدوا الدرس')),
        notViewedLesson: @json(get_phrase('طلبة لم يشاهدوا الدرس')),
        viewedQuiz: @json(get_phrase('طلبة حلوا الاختبار')),
        notViewedQuiz: @json(get_phrase('طلبة لم يحلوا الاختبار')),
        viewedAssignment: @json(get_phrase('طلبة حلوا الواجب')),
        notViewedAssignment: @json(get_phrase('طلبة لم يحلوا الواجب')),
        noSelection: @json(get_phrase('لم يتم اختيار عنصر بعد')),
        loadingError: @json(get_phrase('حدث خطأ أثناء تحميل البيانات')),
        hint: @json(get_phrase('اضغط على عنصر من المنهج لعرض القائمة')),
    };

    function setToggle(viewed) {
        showViewed = viewed;
        document.getElementById('svToggleViewed').classList.toggle('is-active', viewed);
        document.getElementById('svToggleNotViewed').classList.toggle('is-active', !viewed);
        if (currentItemId && currentItemType) {
            loadStudents(currentItemId, currentItemType, currentItemTitle);
        }
    }

    document.getElementById('svToggleViewed').addEventListener('click', function () {
        setToggle(true);
    });
    document.getElementById('svToggleNotViewed').addEventListener('click', function () {
        setToggle(false);
    });

    $(document).off('click.svItem', '.sv-item').on('click.svItem', '.sv-item', function (e) {
        e.preventDefault();
        const $el = $(this);
        const itemId = $el.data('item-id');
        const itemType = $el.data('type');
        const itemTitle = $el.data('title') || '';

        if (!itemId || !itemType) return;

        currentItemId = itemId;
        currentItemType = itemType;
        currentItemTitle = itemTitle;

        $('.sv-item').removeClass('is-active');
        $el.addClass('is-active');
        loadStudents(itemId, itemType, itemTitle);
    });

    function resolveTitle(itemType) {
        if (itemType === 'lesson') return showViewed ? phrases.viewedLesson : phrases.notViewedLesson;
        if (itemType === 'quiz') return showViewed ? phrases.viewedQuiz : phrases.notViewedQuiz;
        if (itemType === 'assignment') return showViewed ? phrases.viewedAssignment : phrases.notViewedAssignment;
        return phrases.hint;
    }

    function loadStudents(itemId, itemType, itemTitle) {
        const content = document.getElementById('studentsListContent');
        const titleEl = document.getElementById('studentsListTitle');
        const subtitleEl = document.getElementById('studentsListSubtitle');
        const badge = document.getElementById('studentsCountBadge');

        titleEl.textContent = resolveTitle(itemType);
        subtitleEl.textContent = itemTitle || phrases.noSelection;
        badge.hidden = true;

        content.innerHTML =
            '<div class="sv-loading"><span class="sv-loading__spinner"></span><span>{{ get_phrase('جاري التحميل...') }}</span></div>';

        $.ajax({
            url: '{{ route('admin.course.get_students') }}',
            type: 'GET',
            data: {
                course_id: {{ $course->id }},
                item_id: itemId,
                item_type: itemType,
                show_viewed: showViewed ? 1 : 0
            },
            success: function (response) {
                content.innerHTML = response;
                const countEl = content.querySelector('[data-students-count]');
                if (countEl) {
                    badge.textContent = countEl.getAttribute('data-students-count');
                    badge.hidden = false;
                }
            },
            error: function () {
                content.innerHTML = '<div class="sv-alert sv-alert--danger">' + phrases.loadingError + '</div>';
            }
        });
    }
})();
</script>

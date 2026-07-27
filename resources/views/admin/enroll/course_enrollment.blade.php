@extends('layouts.admin')
@push('title', get_phrase('Course enrollment'))
@push('meta')@endpush
@push('css')@endpush
@section('content')
    @php
        $courses = App\Models\Course::where('status', 'active')->orWhere('status', 'private')->orderBy('title', 'asc')->get();
        $students = App\Models\User::where('role', 'student')->orderBy('name', 'asc')->get();
        $studentCount = $students->count();
        $courseCount = $courses->count();
    @endphp

    <div class="admin-page">
        <div class="tf-workspace tf-workspace--wide enroll-workspace">
            <div class="tf-hero">
                <div>
                    <div class="tf-hero__kicker">
                        <i class="fi-rr-user-add"></i>
                        {{ get_phrase('Enrollment') }}
                    </div>
                    <h1 class="tf-hero__title">{{ get_phrase('Enroll Students') }}</h1>
                    <p class="tf-hero__desc">{{ get_phrase('Select students and courses, then confirm enrollment in one step.') }}</p>
                </div>
                <div class="tf-hero__actions">
                    @if (has_permission('admin.enroll.history'))
                        <a href="{{ route('admin.enroll.history') }}" class="tf-btn tf-btn--ghost">
                            <i class="fi-rr-time-past"></i>
                            {{ get_phrase('Enroll History') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="enroll-metric-grid">
                <div class="enroll-metric">
                    <span class="enroll-metric__icon enroll-metric__icon--students">
                        <i class="fi-rr-users"></i>
                    </span>
                    <div>
                        <strong class="enroll-metric__value">{{ number_format($studentCount) }}</strong>
                        <span class="enroll-metric__label">{{ get_phrase('Available students') }}</span>
                    </div>
                </div>
                <div class="enroll-metric">
                    <span class="enroll-metric__icon enroll-metric__icon--courses">
                        <i class="fi-rr-e-learning"></i>
                    </span>
                    <div>
                        <strong class="enroll-metric__value">{{ number_format($courseCount) }}</strong>
                        <span class="enroll-metric__label">{{ get_phrase('Available courses') }}</span>
                    </div>
                </div>
                <div class="enroll-metric">
                    <span class="enroll-metric__icon enroll-metric__icon--combo">
                        <i class="fi-rr-apps"></i>
                    </span>
                    <div>
                        <strong class="enroll-metric__value" id="enrollComboPreview">0</strong>
                        <span class="enroll-metric__label">{{ get_phrase('Enrollment combinations') }}</span>
                    </div>
                </div>
            </div>

            <div class="tf-steps">
                <span class="tf-step is-active" id="enrollStep1">
                    <span class="tf-step__num">1</span>{{ get_phrase('Students') }}
                </span>
                <span class="tf-step" id="enrollStep2">
                    <span class="tf-step__num">2</span>{{ get_phrase('Courses') }}
                </span>
                <span class="tf-step" id="enrollStep3">
                    <span class="tf-step__num">3</span>{{ get_phrase('Confirm') }}
                </span>
            </div>

            <form class="enroll-form" action="{{ route('admin.student.post') }}" method="post" id="enrollStudentForm">
                @csrf

                <div class="enroll-layout">
                    <div class="enroll-layout__main">
                        <section class="tf-section">
                            <div class="tf-section__head">
                                <span class="tf-section__num">1</span>
                                <div>
                                    <h2 class="tf-section__title">{{ get_phrase('Select students') }}</h2>
                                    <p class="tf-section__hint">{{ get_phrase('You can enroll one or multiple students at once.') }}</p>
                                </div>
                            </div>

                            <div class="tf-field">
                                <label class="tf-label" for="multiple_user_id">
                                    {{ get_phrase('Students') }}
                                    <span class="req">*</span>
                                </label>
                                <select class="ol-select2" name="user_id[]" id="multiple_user_id" multiple="multiple" required
                                    data-placeholder="{{ get_phrase('Search and select students...') }}">
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->name }}@if (!empty($student->phone)) ({{ $student->phone }})@endif
                                        </option>
                                    @endforeach
                                </select>
                                <span class="tf-help">{{ get_phrase('Type a name or phone number to filter quickly.') }}</span>
                            </div>
                        </section>

                        <section class="tf-section">
                            <div class="tf-section__head">
                                <span class="tf-section__num">2</span>
                                <div>
                                    <h2 class="tf-section__title">{{ get_phrase('Select courses') }}</h2>
                                    <p class="tf-section__hint">{{ get_phrase('Each selected student will be enrolled in all selected courses.') }}</p>
                                </div>
                            </div>

                            <div class="tf-field">
                                <label class="tf-label" for="multiple_course_id">
                                    {{ get_phrase('Courses') }}
                                    <span class="req">*</span>
                                </label>
                                <select class="ol-select2" name="course_id[]" id="multiple_course_id" multiple="multiple" required
                                    data-placeholder="{{ get_phrase('Search and select courses...') }}">
                                    @foreach ($courses as $row)
                                        <option value="{{ $row->id }}">{{ $row->title }}</option>
                                    @endforeach
                                </select>
                                <span class="tf-help">{{ get_phrase('Active and private courses are listed here.') }}</span>
                            </div>
                        </section>
                    </div>

                    <aside class="enroll-layout__side">
                        <div class="enroll-summary">
                            <div class="enroll-summary__head">
                                <span class="enroll-summary__icon">
                                    <i class="fi-rr-clipboard-list-check"></i>
                                </span>
                                <div>
                                    <h3 class="enroll-summary__title">{{ get_phrase('Enrollment summary') }}</h3>
                                    <p class="enroll-summary__desc">{{ get_phrase('Live preview of your selection') }}</p>
                                </div>
                            </div>

                            <ul class="enroll-summary__stats">
                                <li>
                                    <span>{{ get_phrase('Students selected') }}</span>
                                    <strong id="enrollSelectedStudents">0</strong>
                                </li>
                                <li>
                                    <span>{{ get_phrase('Courses selected') }}</span>
                                    <strong id="enrollSelectedCourses">0</strong>
                                </li>
                                <li class="is-highlight">
                                    <span>{{ get_phrase('Total enrollments') }}</span>
                                    <strong id="enrollTotalCombo">0</strong>
                                </li>
                            </ul>

                            <div class="enroll-summary__note">
                                <i class="fi-rr-info"></i>
                                <p>{{ get_phrase('Already enrolled combinations will be skipped and reported.') }}</p>
                            </div>
                        </div>

                        <div class="enroll-tips">
                            <h4 class="enroll-tips__title">
                                <i class="fi-rr-bulb"></i>
                                {{ get_phrase('Quick tips') }}
                            </h4>
                            <ul>
                                <li>{{ get_phrase('Use search inside each field to find items faster.') }}</li>
                                <li>{{ get_phrase('You can enroll many students into many courses together.') }}</li>
                                <li>{{ get_phrase('Check Enroll History after saving to verify results.') }}</li>
                            </ul>
                        </div>
                    </aside>
                </div>

                <div class="tf-actions">
                    <div class="tf-actions__hint" id="enrollActionsHint">
                        {{ get_phrase('Select at least one student and one course to continue.') }}
                    </div>
                    <div class="tf-actions__btns">
                        @if (has_permission('admin.enroll.history'))
                            <a href="{{ route('admin.enroll.history') }}" class="tf-btn tf-btn--ghost">
                                {{ get_phrase('Cancel') }}
                            </a>
                        @endif
                        <button type="submit" class="tf-btn tf-btn--primary" id="enrollSubmitBtn" disabled>
                            <i class="fi-rr-check"></i>
                            <span>{{ get_phrase('Enroll student') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
    "use strict";

    (function () {
        var $students = $('#multiple_user_id');
        var $courses = $('#multiple_course_id');
        var $submit = $('#enrollSubmitBtn');
        var $hint = $('#enrollActionsHint');

        function ensureSelect2($el, placeholder) {
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
            $el.removeClass('inited');
            $el.select2({
                width: '100%',
                placeholder: placeholder,
                allowClear: true,
                closeOnSelect: false
            }).addClass('inited');
        }

        ensureSelect2($students, $students.data('placeholder') || 'Select students');
        ensureSelect2($courses, $courses.data('placeholder') || 'Select courses');

        function updateEnrollPreview() {
            var studentIds = $students.val() || [];
            var courseIds = $courses.val() || [];
            var sCount = studentIds.length;
            var cCount = courseIds.length;
            var total = sCount * cCount;

            $('#enrollSelectedStudents').text(sCount);
            $('#enrollSelectedCourses').text(cCount);
            $('#enrollTotalCombo, #enrollComboPreview').text(total);

            $('#enrollStep1').toggleClass('is-active', sCount === 0);
            $('#enrollStep1').toggleClass('is-done', sCount > 0);
            $('#enrollStep2').toggleClass('is-active', sCount > 0 && cCount === 0);
            $('#enrollStep2').toggleClass('is-done', cCount > 0);
            $('#enrollStep3').toggleClass('is-active', total > 0);

            var ready = total > 0;
            $submit.prop('disabled', !ready);

            if (!ready) {
                $hint.text(@json(get_phrase('Select at least one student and one course to continue.')));
            } else if (total === 1) {
                $hint.text(@json(get_phrase('Ready to create 1 enrollment.')));
            } else {
                $hint.text(
                    @json(get_phrase('Ready to create')) + ' ' + total + ' ' + @json(get_phrase('enrollments.'))
                );
            }
        }

        $students.on('change', updateEnrollPreview);
        $courses.on('change', updateEnrollPreview);
        $(document).on('select2:select select2:unselect', '#multiple_user_id, #multiple_course_id', updateEnrollPreview);

        updateEnrollPreview();
    })();
</script>
@endpush

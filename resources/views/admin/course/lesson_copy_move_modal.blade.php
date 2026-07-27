@php
    $lesson = \App\Models\Lesson::with('course', 'section')->find($id);
    $courses = \App\Models\Course::with('category.parent')->orderBy('title')->get();
@endphp
@if ($lesson)
@php
    $otherCourses = $courses->where('id', '!=', $lesson->course_id);
@endphp
<form action="{{ route('admin.lesson.copy_or_move') }}" method="post" id="lesson-copy-move-form">
    @csrf
    <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">

    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label">{{ get_phrase('Lesson') }}</label>
        <input class="form-control ol-form-control" type="text" value="{{ $lesson->title }}" readonly>
        <small class="text-muted">{{ get_phrase('Course') }}: {{ $lesson->course->title ?? 'N/A' }}</small>
    </div>

    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label">{{ get_phrase('Action') }} <span class="text-danger">*</span></label>
        <div class="d-flex gap-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="action" id="action_copy" value="copy" checked>
                <label class="form-check-label" for="action_copy">{{ get_phrase('Copy') }}</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="action" id="action_move" value="move">
                <label class="form-check-label" for="action_move">{{ get_phrase('Move') }}</label>
            </div>
        </div>
        <small class="text-muted d-block mt-1">{{ get_phrase('Copy') }}: {{ get_phrase('Keep lesson in current course and add copy to target course') }}</small>
        <small class="text-muted d-block">{{ get_phrase('Move') }}: {{ get_phrase('Remove lesson from current course and add to target course') }}</small>
    </div>

    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label" for="target_course_id">{{ get_phrase('Target Course') }} <span class="text-danger">*</span></label>
        <select class="form-control ol-form-control ol-select2" name="target_course_id" id="target_course_id" required>
            <option value="">{{ get_phrase('Select course') }}</option>
            @foreach ($otherCourses as $course)
                @php
                    $academicYear = $course->category && $course->category->parent_id > 0
                        ? ($course->category->parent->title ?? null)
                        : null;
                @endphp
                <option value="{{ $course->id }}">
                    {{ $course->title }}{{ $academicYear ? ' - ' . get_phrase('السنة الدراسية') . ': ' . $academicYear : '' }}
                </option>
            @endforeach
        </select>
        @if ($otherCourses->isEmpty())
            <small class="text-warning d-block mt-1">{{ get_phrase('No other courses available.') }}</small>
        @endif
    </div>

    <div class="fpb7 mb-3" id="section-wrapper" style="display: none;">
        <label class="form-label ol-form-label" for="target_section_id">{{ get_phrase('Target Section') }} <span class="text-danger">*</span></label>
        <select class="form-control ol-form-control ol-select2" name="target_section_id" id="target_section_id">
            <option value="">{{ get_phrase('Select section') }}</option>
        </select>
        <small class="text-warning d-none mt-1" id="no-sections-msg">{{ get_phrase('This course has no sections. Please add sections first.') }}</small>
    </div>

    <div class="fpb7 mb-3">
        <button type="submit" class="btn ol-btn-primary w-100" id="submit-btn" @if($otherCourses->isEmpty()) disabled @endif>
            <span class="btn-text">{{ get_phrase('Apply') }}</span>
        </button>
    </div>
</form>

<script>
$(function() {
    var otherCoursesIsEmpty = {{ $otherCourses->isEmpty() ? 'true' : 'false' }};
    $('#target_course_id').on('change', function() {
        var courseId = $(this).val();
        var $sectionWrapper = $('#section-wrapper');
        var $sectionSelect = $('#target_section_id');

        $sectionSelect.html('<option value="">{{ get_phrase('Select section') }}</option>');

        if (!courseId) {
            $sectionWrapper.hide();
            $('#no-sections-msg').addClass('d-none');
            $('#submit-btn').prop('disabled', otherCoursesIsEmpty);
            return;
        }

        $.get('{{ url("admin/lesson/load-sections") }}/' + courseId, function(data) {
            $('#no-sections-msg').addClass('d-none');
            $('#submit-btn').prop('disabled', false);
            if (data.sections && data.sections.length > 0) {
                data.sections.forEach(function(section) {
                    $sectionSelect.append('<option value="' + section.id + '">' + section.title + '</option>');
                });
                $sectionWrapper.show();
                $sectionSelect.prop('required', true);
            } else {
                $sectionWrapper.show();
                $('#no-sections-msg').removeClass('d-none');
                $sectionSelect.prop('required', false);
                $sectionSelect.html('<option value="">{{ get_phrase('Select section') }}</option>');
                $('#submit-btn').prop('disabled', true);
            }
        }).fail(function() {
            $sectionWrapper.hide();
            $sectionSelect.prop('required', false);
        });
    });

    $('#lesson-copy-move-form').on('submit', function() {
        var action = $('input[name="action"]:checked').val();
        var actionText = action === 'copy' ? '{{ get_phrase("Copy") }}' : '{{ get_phrase("Move") }}';
        $('#submit-btn .btn-text').text(actionText + '...');
        $('#submit-btn').prop('disabled', true);
    });
});
</script>
@include('admin.init')
@else
    <p class="text-danger mb-0">{{ get_phrase('Lesson not found.') }}</p>
@endif

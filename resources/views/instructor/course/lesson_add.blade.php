@php
    $sections = App\Models\Section::where('course_id', $id)->orderBy('sort')->get();

    if ($lesson_type == 'html5') {
        $lessonTypeLabel = get_phrase('Video url') . ' [.mp4]';
    } elseif ($lesson_type == 'video') {
        $lessonTypeLabel = get_phrase('Video file');
    } elseif ($lesson_type == 'youtube' || $lesson_type == 'academy cloud' || $lesson_type == 'vimeo') {
        $lessonTypeLabel = ucfirst(get_phrase($lesson_type)) . ' ' . get_phrase('Video');
    } elseif ($lesson_type == 'google_drive_video') {
        $lessonTypeLabel = get_phrase('Google drive video');
    } elseif ($lesson_type == 'document') {
        $lessonTypeLabel = get_phrase('Document file');
    } else {
        $lessonTypeLabel = ucfirst($lesson_type);
    }
@endphp

<div class="lesson-wizard tf-modal-form">
    <div class="lesson-wizard__banner">
        <div>
            <p class="lesson-wizard__eyebrow">{{ get_phrase('Step 2 of 2') }}</p>
            <h5 class="lesson-wizard__title">{{ get_phrase('Lesson details') }}</h5>
            <p class="lesson-wizard__desc">{{ get_phrase('Add title, section and content source') }}</p>
        </div>
        <div class="lesson-wizard__type-pill">
            <span>{{ get_phrase('Lesson type') }}</span>
            <strong>{{ $lessonTypeLabel }}</strong>
            <a onclick="ajaxModal('{{ route('modal', ['instructor.course.lesson_type', 'id' => $id]) }}', '{{ get_phrase('Add new lesson') }}', 'modal-lg')"
                class="lesson-wizard__change" href="javascript:void(0)">
                {{ get_phrase('Change') }}
                <i class="fi-rr-arrow-small-right"></i>
            </a>
        </div>
    </div>

    <form class="ajaxFormSubmission" action="{{ route('instructor.lesson.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="course_id" value="{{ $id }}">
        <input type="hidden" name="lesson_type" value="{{ $lesson_type }}">

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Basic info') }}</h6>

            <div class="form-group mb-3">
                <label class="form-label ol-form-label">{{ get_phrase('Title') }} <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control ol-form-control"
                    placeholder="{{ get_phrase('Enter lesson title') }}" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label ol-form-label">{{ get_phrase('Section') }} <span class="text-danger">*</span></label>
                <select class="form-control ol-select2" data-toggle="select2" name="section_id" required>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->title }}</option>
                    @endforeach
                </select>
                <small class="tf-help">{{ get_phrase('Lessons are grouped under sections in the curriculum') }}</small>
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Content source') }}</h6>
            <div class="lesson-form-source">
                @if ($lesson_type == 'youtube')
                    @include('instructor.course.youtube_type_lesson_add')
                @elseif ($lesson_type == 'academy_cloud')
                    @include('admin.course.academy_cloud_type_lesson_add')
                @elseif ($lesson_type == 'vimeo')
                    @include('instructor.course.vimeo_type_lesson_add')
                @elseif ($lesson_type == 'html5')
                    @include('instructor.course.html5_type_lesson_add')
                @elseif ($lesson_type == 'video')
                    @include('instructor.course.video_type_lesson_add')
                @elseif ($lesson_type == 'amazon-s3')
                    @include('amazon_s3_type_lesson_add.php')
                @elseif ($lesson_type == 'google_drive_video')
                    @include('instructor.course.google_drive_type_lesson_add')
                @elseif ($lesson_type == 'document')
                    @include('instructor.course.document_type_lesson_add')
                @elseif ($lesson_type == 'text')
                    @include('instructor.course.text_type_lesson_add')
                @elseif ($lesson_type == 'image')
                    @include('instructor.course.image_file_type_lesson_add')
                @elseif ($lesson_type == 'iframe')
                    @include('instructor.course.iframe_type_lesson_add')
                @endif
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Schedule & summary') }}</h6>
            <div class="row">
                <div class="fpb7 col-sm-6 mb-3">
                    <label class="form-label ol-form-label" for="start_time">
                        {{ get_phrase('Start Time') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="datetime-local" name="start_time" id="start_time" value="{{ now() }}">
                </div>
                <div class="fpb7 col-sm-6 mb-3">
                    <label class="form-label ol-form-label" for="end_time">
                        {{ get_phrase('End Time') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}">
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label ol-form-label">{{ get_phrase('Summary') }}</label>
                <textarea name="summary" class="form-control text_editor" placeholder="{{ get_phrase('Optional short summary for students') }}"></textarea>
            </div>
        </div>

        <div class="form-group mb-3 d-none">
            <label class="form-label ol-form-label">{{ get_phrase('Do you want to keep it free as a preview lesson') }}
                ?</label>
            <br>
            <input type="checkbox" name="free_lesson" id="free_lesson" value="1" class="form-check-input">
            <label for="free_lesson">{{ get_phrase('Mark as free lesson') }}</label>
        </div>

        <div class="lesson-wizard__footer">
            <a href="javascript:void(0)"
                onclick="ajaxModal('{{ route('modal', ['instructor.course.lesson_type', 'id' => $id]) }}', '{{ get_phrase('Add new lesson') }}', 'modal-lg')"
                class="tf-btn tf-btn--ghost">
                <i class="fi-rr-arrow-small-left"></i>
                {{ get_phrase('Back') }}
            </a>
            <button class="tf-btn tf-btn--primary formSubmissionBtn" type="submit" name="button">
                <i class="fi-rr-check"></i>
                {{ get_phrase('Add lesson') }}
            </button>
        </div>
    </form>
</div>

<script>
    'use strict';

    function ajax_get_video_details(url) {
        $('#perloader').show();
        if (checkURLValidity(url)) {
            $.ajax({
                url: "{{ route('get.video.details') }}",
                type: 'POST',
                data: {
                    url: url
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response);
                    jQuery('#duration').val(response.duration);
                    $('#perloader').hide();
                    $('#invalid_url').hide();
                }
            });
        } else {
            $('#invalid_url').show();
            $('#perloader').hide();
            jQuery('#duration').val('');

        }
    }

    function checkURLValidity(video_url) {
        var youtubePregMatch =
            /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
        var vimeoPregMatch = /^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/;
        if (video_url.match(youtubePregMatch)) {
            return true;
        } else if (vimeoPregMatch.test(video_url)) {
            return true;
        } else {
            return false;
        }
    }
</script>
@include('instructor.init')

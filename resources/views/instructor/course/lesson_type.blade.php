@php
    $course = App\Models\Course::where('id', $id)->first();

    $selected_lesson = 'youtube';
    if (isset($param3) && !empty($param3)) {
        $selected_lesson = $param3;
    }
@endphp

<div class="lesson-wizard tf-modal-form">
    <div class="lesson-wizard__banner">
        <div>
            <p class="lesson-wizard__eyebrow">{{ get_phrase('Step 1 of 2') }}</p>
            <h5 class="lesson-wizard__title">{{ get_phrase('Choose lesson type') }}</h5>
            <p class="lesson-wizard__desc">{{ get_phrase('Pick how students will consume this lesson') }}</p>
        </div>
        <div class="lesson-wizard__course">
            <span>{{ get_phrase('Course') }}</span>
            <strong>{{ $course->title }}</strong>
        </div>
    </div>

    <form action="" class="lesson-type-form">
        <input id="course_id_for_lesson" type="hidden" value="" name="course_id_for_lesson">

        <div class="lesson-type-group">
            <h6 class="lesson-type-group__label">{{ get_phrase('Video') }}</h6>
            <div class="lesson-type-grid">
                <label class="lesson-type-card" for="radio-youtube">
                    <input class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-youtube" value="youtube" @if ($selected_lesson == 'youtube') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--youtube">
                            <i class="fi-rr-play-alt"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('YouTube Video') }}</strong>
                            <small>{{ get_phrase('Paste a YouTube link') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>

                <label class="lesson-type-card" for="radio-vimeo">
                    <input class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-vimeo" value="vimeo" @if ($selected_lesson == 'vimeo') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--vimeo">
                            <i class="fi-rr-video-camera-alt"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('Vimeo Video') }}</strong>
                            <small>{{ get_phrase('Paste a Vimeo link') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>

                <label class="lesson-type-card" for="radio-videofile">
                    <input class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-videofile" value="video" @if ($selected_lesson == 'video') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--file">
                            <i class="fi-rr-cloud-upload-alt"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('Video file') }}</strong>
                            <small>{{ get_phrase('Upload from your device') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>

                <label class="lesson-type-card" for="radio-url">
                    <input value="html5" class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-url" @if ($selected_lesson == 'html5') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--url">
                            <i class="fi-rr-link"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('Video url [ .mp4 ]') }}</strong>
                            <small>{{ get_phrase('Direct MP4 URL') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>

                <label class="lesson-type-card" for="radio-drive">
                    <input class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-drive" value="google_drive_video"
                        @if ($selected_lesson == 'google_drive_video') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--drive">
                            <i class="fi-rr-folder"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('Google drive video') }}</strong>
                            <small>{{ get_phrase('From Google Drive') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>
            </div>
        </div>

        <div class="lesson-type-group">
            <h6 class="lesson-type-group__label">{{ get_phrase('Files & content') }}</h6>
            <div class="lesson-type-grid">
                <label class="lesson-type-card" for="radio-document">
                    <input class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-document" value="document" @if ($selected_lesson == 'document') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--doc">
                            <i class="fi-rr-document"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('Document file') }}</strong>
                            <small>{{ get_phrase('PDF, DOC and more') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>

                <label class="lesson-type-card" for="radio-text">
                    <input class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-text" value="text" @if ($selected_lesson == 'text') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--text">
                            <i class="fi-rr-text"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('Text') }}</strong>
                            <small>{{ get_phrase('Written lesson content') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>

                <label class="lesson-type-card" for="radio-image">
                    <input class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-image" value="image" @if ($selected_lesson == 'image') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--image">
                            <i class="fi-rr-picture"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('Image') }}</strong>
                            <small>{{ get_phrase('Image based lesson') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>

                <label class="lesson-type-card" for="radio-iframe">
                    <input class="lesson-type-card__input" type="radio" name="lesson_type"
                        id="radio-iframe" value="iframe" @if ($selected_lesson == 'iframe') checked @endif>
                    <span class="lesson-type-card__body">
                        <span class="lesson-type-card__icon lesson-type-card__icon--iframe">
                            <i class="fi-rr-browser"></i>
                        </span>
                        <span class="lesson-type-card__text">
                            <strong>{{ get_phrase('Iframe embed') }}</strong>
                            <small>{{ get_phrase('Embed external content') }}</small>
                        </span>
                        <span class="lesson-type-card__check"><i class="fi-rr-check"></i></span>
                    </span>
                </label>
            </div>
        </div>

        <div class="lesson-wizard__footer">
            <span class="lesson-wizard__hint">{{ get_phrase('You can change the type later before saving') }}</span>
            <a href="javascript:void(0)" class="tf-btn tf-btn--primary" id="lesson-add-modal"
                onclick="showLessonAddModal()">
                {{ get_phrase('Next') }}
                <i class="fi-rr-arrow-small-right"></i>
            </a>
        </div>
    </form>
</div>

<script type="text/javascript">
    "use strict";

    function showLessonAddModal() {
        var url = $("input[name=lesson_type]:checked").val();
        if (!url) {
            return;
        }
        ajaxModal('{{ route('modal', ['instructor.course.lesson_add', 'id' => $course->id]) }}&lesson_type=' + url,
            '{{ get_phrase('Add new lesson') }}', 'modal-lg');
    }
</script>

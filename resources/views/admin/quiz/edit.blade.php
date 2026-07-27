@php
    $quiz = App\Models\Lesson::join('sections', 'lessons.section_id', 'sections.id')
        ->join('courses', 'sections.course_id', 'courses.id')
        ->select('lessons.*', 'courses.id as course_id')
        ->where('lessons.id', $id)
        ->first();

    $durationParts = $quiz->duration ? explode(':', $quiz->duration) : [];
    $hour = $durationParts[0] ?? 0;
    $minute = $durationParts[1] ?? 0;
    $second = $durationParts[2] ?? 0;

    $sections = App\Models\Section::where('course_id', $quiz->course_id)->orderBy('sort')->get();
    $isAssignment = (int) $quiz->type === 2;
    $typeLabel = $isAssignment ? get_phrase('Assignment') : get_phrase('Quiz');

    $startTime = $quiz->start_time
        ? \Carbon\Carbon::parse($quiz->start_time)->format('Y-m-d\TH:i')
        : '';
    $endTime = $quiz->end_time
        ? \Carbon\Carbon::parse($quiz->end_time)->format('Y-m-d\TH:i')
        : '';
@endphp

<div class="quiz-wizard lesson-wizard tf-modal-form">
    <div class="lesson-wizard__banner">
        <div>
            <p class="lesson-wizard__eyebrow">{{ get_phrase('Curriculum') }}</p>
            <h5 class="lesson-wizard__title">{{ get_phrase('Edit quiz') }}</h5>
            <p class="lesson-wizard__desc">{{ get_phrase('Update title, section, duration and scoring rules') }}</p>
        </div>
        <div class="lesson-wizard__course">
            <span>{{ get_phrase('Type') }}</span>
            <strong>{{ $typeLabel }}</strong>
        </div>
    </div>

    <form action="{{ route('admin.course.quiz.update', $id) }}" method="post">
        @csrf
        <input type="hidden" name="type" value="{{ $quiz->type }}">

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Basic info') }}</h6>

            <div class="mb-3">
                <span class="form-label ol-form-label d-block mb-2">{{ get_phrase('Type') }}</span>
                <div class="tf-choice tf-choice--2">
                    <label for="quiz_type_1" class="{{ !$isAssignment ? '' : 'opacity-50' }}">
                        <input type="radio" id="quiz_type_1" value="1" {{ !$isAssignment ? 'checked' : '' }} disabled>
                        <span class="tf-choice__card">
                            <span>
                                <strong>{{ get_phrase('Quiz') }}</strong>
                                <small>{{ get_phrase('Graded quiz for students') }}</small>
                            </span>
                        </span>
                    </label>
                    <label for="quiz_type_2" class="{{ $isAssignment ? '' : 'opacity-50' }}">
                        <input type="radio" id="quiz_type_2" value="2" {{ $isAssignment ? 'checked' : '' }} disabled>
                        <span class="tf-choice__card">
                            <span>
                                <strong>{{ get_phrase('Assignment') }}</strong>
                                <small>{{ get_phrase('Assignment style assessment') }}</small>
                            </span>
                        </span>
                    </label>
                </div>
                <small class="tf-help">{{ get_phrase('Type cannot be changed after creation') }}</small>
            </div>

            <div class="fpb7 mb-3">
                <label class="form-label ol-form-label" for="title">
                    {{ get_phrase('Title') }}
                    <span class="text-danger ms-1">*</span>
                </label>
                <input class="form-control ol-form-control" type="text" id="title" name="title"
                    value="{{ $quiz->title }}" placeholder="{{ get_phrase('Enter quiz title') }}" required>
            </div>

            <div class="mb-0">
                <label class="form-label ol-form-label" for="section">
                    {{ get_phrase('Section') }}
                    <span class="text-danger ms-1">*</span>
                </label>
                <select class="form-control ol-form-control" id="section" name="section" required>
                    <option value="" disabled>{{ get_phrase('Select an option') }}</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" @selected($section->id == $quiz->section_id)>
                            {{ $section->title }}
                        </option>
                    @endforeach
                </select>
                <small class="tf-help">{{ get_phrase('The quiz will appear under this section in the curriculum') }}</small>
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Duration') }}</h6>
            <div class="quiz-metric-grid">
                <div>
                    <label class="form-label ol-form-label" for="hour">{{ get_phrase('Hour') }}</label>
                    <input class="form-control ol-form-control" type="number" min="0" max="23"
                        id="hour" name="hour" value="{{ (int) $hour }}" placeholder="0">
                </div>
                <div>
                    <label class="form-label ol-form-label" for="minute">{{ get_phrase('Minute') }}</label>
                    <input class="form-control ol-form-control" type="number" min="0" max="59"
                        id="minute" name="minute" value="{{ (int) $minute }}" placeholder="30">
                </div>
                <div>
                    <label class="form-label ol-form-label" for="second">{{ get_phrase('Second') }}</label>
                    <input class="form-control ol-form-control" type="number" min="0" max="59"
                        id="second" name="second" value="{{ (int) $second }}" placeholder="0">
                </div>
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Scoring') }}</h6>
            <div class="quiz-metric-grid">
                <div>
                    <label class="form-label ol-form-label" for="total_mark">
                        {{ get_phrase('Total Mark') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="number" min="1"
                        id="total_mark" name="total_mark" value="{{ $quiz->total_mark }}" required>
                </div>
                <div>
                    <label class="form-label ol-form-label" for="pass_mark">
                        {{ get_phrase('Pass Mark') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="number" min="1"
                        id="pass_mark" name="pass_mark" value="{{ $quiz->pass_mark }}" required>
                </div>
                <div>
                    <label class="form-label ol-form-label" for="retake">
                        {{ get_phrase('Retake') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="number" min="1"
                        id="retake" name="retake" value="{{ $quiz->retake }}" required>
                </div>
            </div>

            <div class="mt-3 p-3 rounded border bg-light">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="show_answer"
                        name="show_answer" value="1" @checked($quiz->show_answer)>
                    <label class="form-check-label" for="show_answer">
                        <strong>{{ get_phrase('Show correct answers to students') }}</strong>
                    </label>
                </div>
                <small class="tf-help d-block mt-2">
                    {{ get_phrase('If disabled, students will only see their score and cannot review correct answers until you enable this.') }}
                </small>
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Schedule') }}</h6>
            <div class="row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <label class="form-label ol-form-label" for="start_time">
                        {{ get_phrase('Start Time') }}
                        <span class="text-muted ms-1">({{ get_phrase('اختياري') }})</span>
                    </label>
                    <input class="form-control ol-form-control" type="datetime-local" id="start_time"
                        name="start_time" value="{{ old('start_time', $startTime) }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label ol-form-label" for="end_time">
                        {{ get_phrase('End Time') }}
                        <span class="text-muted ms-1">({{ get_phrase('اختياري') }})</span>
                    </label>
                    <input class="form-control ol-form-control" type="datetime-local" id="end_time"
                        name="end_time" value="{{ old('end_time', $endTime) }}">
                </div>
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Description') }}</h6>
            <div class="mb-0">
                <textarea name="description" rows="4" class="form-control ol-form-control"
                    placeholder="{{ get_phrase('Optional description for students') }}">{{ $quiz->description }}</textarea>
                <small class="tf-help">{{ get_phrase('Keep it short — students see this before starting') }}</small>
            </div>
        </div>

        <div class="lesson-wizard__footer">
            <span class="lesson-wizard__hint">{{ get_phrase('Changes apply immediately after saving') }}</span>
            <button type="submit" class="tf-btn tf-btn--primary">
                <i class="fi-rr-disk"></i>
                {{ get_phrase('Update Quiz') }}
            </button>
        </div>
    </form>
</div>

@include('admin.init')

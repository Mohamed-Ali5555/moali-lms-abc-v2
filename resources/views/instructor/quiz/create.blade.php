@php
    $sections = App\Models\Section::where('course_id', $id)->orderBy('sort')->get();
@endphp

<div class="quiz-wizard lesson-wizard tf-modal-form">
    <div class="lesson-wizard__banner">
        <div>
            <p class="lesson-wizard__eyebrow">{{ get_phrase('Curriculum') }}</p>
            <h5 class="lesson-wizard__title">{{ get_phrase('Add new quiz') }}</h5>
            <p class="lesson-wizard__desc">{{ get_phrase('Set title, section, duration and scoring rules') }}</p>
        </div>
        <div class="lesson-wizard__course">
            <span>{{ get_phrase('Level') }}</span>
            <strong id="quizLevelBadge">{{ get_phrase('Advanced') }}</strong>
        </div>
    </div>

    <form action="{{ route('instructor.course.quiz.store') }}" method="post">
        @csrf
        <input type="hidden" name="course_id" value="{{ $id }}">

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Basic info') }}</h6>

            <div class="fpb7 mb-3">
                <label class="form-label ol-form-label" for="title">
                    {{ get_phrase('Title') }}
                    <span class="text-danger ms-1">*</span>
                </label>
                <input class="form-control ol-form-control" type="text" id="title" name="title"
                    placeholder="{{ get_phrase('Enter quiz title') }}" required>
            </div>

            <div class="mb-3">
                <span class="form-label ol-form-label d-block mb-2">
                    {{ get_phrase('Level') }}
                    <span class="text-danger ms-1">*</span>
                </span>
                <div class="tf-choice tf-choice--3">
                    @foreach (['Beginner', 'Intermediate', 'Advanced'] as $level)
                        <label for="quiz_level_{{ $level }}">
                            <input type="radio" name="level" id="quiz_level_{{ $level }}" value="{{ $level }}"
                                @checked($level === 'Advanced')
                                onchange="document.getElementById('quizLevelBadge').textContent = '{{ get_phrase($level) }}'">
                            <span class="tf-choice__card">
                                <span>
                                    <strong>{{ get_phrase($level) }}</strong>
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-0">
                <label class="form-label ol-form-label" for="section">
                    {{ get_phrase('Section') }}
                    <span class="text-danger ms-1">*</span>
                </label>
                <select class="form-control ol-form-control" id="section" name="section" required>
                    <option value="">{{ get_phrase('Select an option') }}</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->title }}</option>
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
                    <input class="form-control ol-form-control" type="number" min="0" max="23" value="0"
                        id="hour" name="hour" placeholder="0">
                </div>
                <div>
                    <label class="form-label ol-form-label" for="minute">{{ get_phrase('Minute') }}</label>
                    <input class="form-control ol-form-control" type="number" min="0" max="59" value="30"
                        id="minute" name="minute" placeholder="30">
                </div>
                <div>
                    <label class="form-label ol-form-label" for="second">{{ get_phrase('Second') }}</label>
                    <input class="form-control ol-form-control" type="number" min="0" max="59" value="0"
                        id="second" name="second" placeholder="0">
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
                    <input class="form-control ol-form-control" type="number" min="1" value="10"
                        id="total_mark" name="total_mark" required>
                </div>
                <div>
                    <label class="form-label ol-form-label" for="pass_mark">
                        {{ get_phrase('Pass Mark') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="number" min="1" value="5"
                        id="pass_mark" name="pass_mark" required>
                </div>
                <div>
                    <label class="form-label ol-form-label" for="retake">
                        {{ get_phrase('Retake') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="number" min="1" value="1"
                        id="retake" name="retake" required>
                </div>
            </div>

            <div class="mt-3 p-3 rounded border bg-light">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="show_answer"
                        name="show_answer" value="1">
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
                        name="start_time" value="{{ old('start_time') }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label ol-form-label" for="end_time">
                        {{ get_phrase('End Time') }}
                        <span class="text-muted ms-1">({{ get_phrase('اختياري') }})</span>
                    </label>
                    <input class="form-control ol-form-control" type="datetime-local" id="end_time"
                        name="end_time" value="{{ old('end_time') }}">
                </div>
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Description') }}</h6>
            <div class="mb-0">
                <textarea name="description" rows="4" class="form-control ol-form-control"
                    placeholder="{{ get_phrase('Optional description for students') }}"></textarea>
                <small class="tf-help">{{ get_phrase('Keep it short — students see this before starting') }}</small>
            </div>
        </div>

        <div class="lesson-wizard__footer">
            <span class="lesson-wizard__hint">{{ get_phrase('You can add questions after creating the quiz') }}</span>
            <button type="submit" class="tf-btn tf-btn--primary">
                <i class="fi-rr-check"></i>
                {{ get_phrase('Add Quiz') }}
            </button>
        </div>
    </form>
</div>

@include('instructor.init')

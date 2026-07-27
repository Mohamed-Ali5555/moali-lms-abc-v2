@php
    $quiz = \Modules\BankQuestions\App\Models\BankQuizs::findOrFail($id);
    $duration = $quiz->duration ? explode(':', $quiz->duration) : [0, 0, 0];
    $categories = App\Models\Category::where('parent_id', 0)
        ->with(['bank_category' => fn ($q) => $q->orderBy('title')])
        ->orderBy('title')
        ->get();
@endphp

<div class="quiz-wizard lesson-wizard tf-modal-form">
    <div class="lesson-wizard__banner">
        <div>
            <p class="lesson-wizard__eyebrow">{{ get_phrase('Question Bank') }}</p>
            <h5 class="lesson-wizard__title">{{ get_phrase('Edit Quiz') }}</h5>
            <p class="lesson-wizard__desc">{{ get_phrase('Update title, category, duration and scoring rules') }}</p>
        </div>
        <div class="lesson-wizard__course">
            <span>{{ get_phrase('Status') }}</span>
            <strong id="quizStatusBadge">{{ $quiz->status == 1 ? get_phrase('Active') : get_phrase('Inactive') }}</strong>
        </div>
    </div>

    <form action="{{ route('admin.bank.quizs.update', $quiz->id) }}" method="post">
        @csrf

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Basic info') }}</h6>

            <div class="fpb7 mb-3">
                <label class="form-label ol-form-label" for="title">
                    {{ get_phrase('Title') }}
                    <span class="text-danger ms-1">*</span>
                </label>
                <input class="form-control ol-form-control" type="text" id="title" name="title"
                    value="{{ old('title', $quiz->title) }}" placeholder="{{ get_phrase('Enter quiz title') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label ol-form-label" for="category">
                    {{ get_phrase('Category') }}
                    <span class="text-danger ms-1">*</span>
                </label>
                <select class="form-control ol-form-control ol-select2" id="category" name="category"
                    data-toggle="select2" data-placeholder="{{ get_phrase('Select Category') }}" required>
                    <option value="">{{ get_phrase('Select Category') }}</option>
                    @foreach ($categories as $category)
                        @if ($category->bank_category->count())
                            <optgroup label="{{ $category->title }}">
                                @foreach ($category->bank_category as $sub_category)
                                    <option value="{{ $sub_category->id }}"
                                        @selected(old('category', $quiz->category_id) == $sub_category->id)>
                                        {{ $sub_category->title }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
                <small class="tf-help">{{ get_phrase('Questions will be filtered by this bank category') }}</small>
            </div>

            <div class="mb-0">
                <span class="form-label ol-form-label d-block mb-2">{{ get_phrase('Status') }}</span>
                <div class="tf-choice tf-choice--2">
                    <label for="quiz_status_1">
                        <input type="radio" name="status" id="quiz_status_1" value="1"
                            @checked(old('status', $quiz->status) == 1)
                            onchange="document.getElementById('quizStatusBadge').textContent = '{{ get_phrase('Active') }}'">
                        <span class="tf-choice__card">
                            <span>
                                <strong>{{ get_phrase('Active') }}</strong>
                                <small>{{ get_phrase('Available for use') }}</small>
                            </span>
                        </span>
                    </label>
                    <label for="quiz_status_0">
                        <input type="radio" name="status" id="quiz_status_0" value="0"
                            @checked(old('status', $quiz->status) == 0)
                            onchange="document.getElementById('quizStatusBadge').textContent = '{{ get_phrase('Inactive') }}'">
                        <span class="tf-choice__card">
                            <span>
                                <strong>{{ get_phrase('Inactive') }}</strong>
                                <small>{{ get_phrase('Hidden from selection') }}</small>
                            </span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Duration') }}</h6>
            <div class="quiz-metric-grid">
                <div>
                    <label class="form-label ol-form-label" for="hour">{{ get_phrase('Hour') }}</label>
                    <input class="form-control ol-form-control" type="number" min="0" max="23"
                        id="hour" name="hour" value="{{ old('hour', (int) ($duration[0] ?? 0)) }}" placeholder="0">
                </div>
                <div>
                    <label class="form-label ol-form-label" for="minute">{{ get_phrase('Minute') }}</label>
                    <input class="form-control ol-form-control" type="number" min="0" max="59"
                        id="minute" name="minute" value="{{ old('minute', (int) ($duration[1] ?? 0)) }}" placeholder="30">
                </div>
                <div>
                    <label class="form-label ol-form-label" for="second">{{ get_phrase('Second') }}</label>
                    <input class="form-control ol-form-control" type="number" min="0" max="59"
                        id="second" name="second" value="{{ old('second', (int) ($duration[2] ?? 0)) }}" placeholder="0">
                </div>
            </div>
            <small class="tf-help d-block mt-2">{{ get_phrase('Total time allowed to complete the quiz') }}</small>
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
                        id="total_mark" name="total_mark" value="{{ old('total_mark', $quiz->total_mark) }}" required>
                </div>
                <div>
                    <label class="form-label ol-form-label" for="pass_mark">
                        {{ get_phrase('Pass Mark') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="number" min="1"
                        id="pass_mark" name="pass_mark" value="{{ old('pass_mark', $quiz->pass_mark) }}" required>
                </div>
                <div>
                    <label class="form-label ol-form-label" for="retake">
                        {{ get_phrase('Retake') }}
                        <span class="text-danger ms-1">*</span>
                    </label>
                    <input class="form-control ol-form-control" type="number" min="1"
                        id="retake" name="retake" value="{{ old('retake', $quiz->retake) }}" required>
                </div>
            </div>
            <small class="tf-help d-block mt-2">{{ get_phrase('Pass mark must be less than or equal to total mark') }}</small>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('Description') }}</h6>
            <div class="mb-0">
                <textarea name="description" rows="4" class="form-control ol-form-control"
                    placeholder="{{ get_phrase('Optional description for students') }}">{{ old('description', $quiz->description) }}</textarea>
                <small class="tf-help">{{ get_phrase('Keep it short — students see this before starting') }}</small>
            </div>
        </div>

        <div class="lesson-wizard__footer">
            <span class="lesson-wizard__hint">{{ get_phrase('Changes apply immediately after saving') }}</span>
            <button type="submit" class="tf-btn tf-btn--primary">
                <i class="fi-rr-check"></i>
                {{ get_phrase('Update Quiz') }}
            </button>
        </div>
    </form>
</div>

<script type="text/javascript">
    "use strict";
    $('.ol-select2').select2({
        dropdownParent: $("#ajaxModal"),
        width: '100%'
    });
</script>
@include('admin.init')

<form action="{{ route('admin.course.quiz.choose') }}" method="post">@csrf
    @php

        $category = \App\Models\Category::where('id', $id)->first();
        $categoryId = $category->parent_id == 0 ? $category->id : $category->parent_id;
        $bankCategories = \Modules\BankQuestions\App\Models\BankQuestionsCategory::with(['quizs'])
            ->where(['category_id' => $categoryId])
            ->get();

    @endphp
    <input type="hidden" name="category_id" value="{{ $id }}">
    <input type="hidden" name="course_id" value="{{ $course_id }}">
    <input type="hidden" name="type" value="{{ @$type }}">

    <div class="row mb-3">
        <div class="col-sm-12 fpb-7">
            <label class="form-label ol-form-label">
                {{ get_phrase('Section') }}
                <span class="text-danger ms-1">*</span>
            </label>
            <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="section">
                <option value="">{{ get_phrase('Select an option') }}</option>
                @foreach (App\Models\Section::where('course_id', $course_id)->get() as $section)
                    <option value="{{ $section->id }}">{{ $section->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-12 fpb-7">
            <label class="form-label ol-form-label">
                @if (@$type == 'quiz')
                    {{ get_phrase('Quiz') }}
                @else
                    {{ get_phrase('Assignment') }}
                @endif
                <span class="text-danger ms-1">*</span>
            </label>
            <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="quiz">
                <option value="" disabled>{{ get_phrase('Select an option') }}</option>
                @foreach ($bankCategories as $Category)
                    <optgroup label="{{ $Category->title }}">
                        @foreach ($Category->quizs as $quiz)
                            <option value="{{ $quiz->id }}">{{ $quiz->title }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3 p-3 rounded border bg-light">
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" role="switch" id="show_answer"
                name="show_answer" value="1">
            <label class="form-check-label" for="show_answer">
                <strong>{{ get_phrase('Show correct answers to students') }}</strong>
            </label>
        </div>
        <small class="text-muted d-block mt-2">
            {{ get_phrase('If disabled, students will only see their score and cannot review correct answers until you enable this.') }}
        </small>
    </div>

    <div class="row mb-3">
        <div class="fpb7 col-sm-6 mb-3">
            <label class="form-label ol-form-label" for="start_time">
                {{ get_phrase('Start Time') }}
                <span class="text-muted ms-1">({{ get_phrase('اختياري') }})</span>
            </label>
            <input class="form-control ol-form-control" type="datetime-local" id="start_time" name="start_time"
                value="{{ old('start_time') }}">
        </div>
        <div class="fpb7 col-sm-6 mb-3">
            <label class="form-label ol-form-label" for="end_time">
                {{ get_phrase('End Time') }}
                <span class="text-muted ms-1">({{ get_phrase('اختياري') }})</span>
            </label>
            <input class="form-control ol-form-control" type="datetime-local" id="end_time" name="end_time"
                value="{{ old('end_time') }}">
        </div>
    </div>

    {{-- <div class="fpb-7 mb-3">
        <label for="description"
            class="form-label ol-form-label col-form-label">{{ get_phrase('Description') }}</label>
        <textarea name="description" rows="5" class="form-control ol-form-control text_editor"></textarea>
    </div> --}}

    <div class="fpb7">
        @if (@$type == 'quiz')
            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Add quiz ') }}</button>
        @else
            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Add Assignment ') }}</button>
        @endif
    </div>
</form>

@include('admin.init')

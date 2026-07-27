
@php

    $quiz           = \App\Models\Lesson::where('id',$id)->first();
    $questionsTitle = $quiz->questions->pluck('title')->toArray();
    $category       = \App\Models\Category::where('id',$quiz->course->category_id)->first();
    $categoryId     = $category->parent_id == 0 ? $category->id : $category->parent_id;
    $bankCategories = \Modules\BankQuestions\App\Models\BankQuestionsCategory::with(['questions'=>function($query)use($questionsTitle){
        $query->whereNotIn('title',$questionsTitle);
    }])->where(['category_id'=>$categoryId])->get();

@endphp

<style>
    .select2-selection.select2-selection--multiple {
        cursor: pointer !important;
    }
.selection,.select2-container--default .select2-results>.select2-results__options {
      font-size: 18px;
      resize: none;
      direction: rtl;
    }

</style>

<form class="ajaxForm" action="{{ route('admin.course.question.choose') }}" method="post">
    @csrf
    <input type="hidden" name="quiz_id" value="{{ $id }}">

    <div class="row mb-3">
        <div class="col-sm-12 fpb-7">
            <label class="form-label ol-form-label">
                {{ get_phrase('Question') }}
                <span class="text-danger ms-1">*</span>
            </label>
            <select class="form-control ol-form-control ol-select2 " data-toggle="select2" name="question" >
                <option value="">{{ get_phrase('Select a question') }}</option>
                @foreach ($bankCategories as $Category)
                    <optgroup label="{{ $Category->title }}">
                        @foreach ($Category->questions as $question)
                            <option value="{{ $question->id }}">{!! $question->title !!}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
    </div>

    <div class="d-flex gap-3">
        <a href="#" class="btn ol-btn-primary" id="questionBackBtn"
            onclick="ajaxModal('{{ route('modal', ['admin.questions.index', 'id' => $id]) }}', '{{ get_phrase('Questions') }}', 'modal-xl')">
            <i class="fi-rr-angle-small-left"></i> {{ get_phrase('Back') }}
        </a>

        <div class="fpb7">
            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Add Question') }}</button>
        </div>
    </div>
</form>


@include('admin.init')
<script>

    // after response this function will call
    function responseBack() {
        document.querySelector('#questionBackBtn').click();
    }
</script>

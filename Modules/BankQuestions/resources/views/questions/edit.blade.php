@php
    $question = \Modules\BankQuestions\App\Models\BankQuestions::with('quizs')->where('id', $id)->first();
    $quiz = $question->quizs->first();
@endphp
<style>
    .select2-selection.select2-selection--multiple {
        cursor: pointer !important;
    }
#options,#answer-select2 {

    font-size: 18px;
    resize: none;
    direction: rtl;
    }
.tagify__tag>div>* {
    direction: rtl;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    direction: rtl;
}
</style>
<form class="ajaxForm" action="{{ route('admin.bank.question.update', $id) }}" method="post">@csrf

    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
    <div class="row">
        <div class="col-sm-12">
            <div class="mb-3">
                <label class="form-label ol-form-label">
                    {{ get_phrase('Question Type') }}
                    <span class="text-danger ms-1">*</span>
                </label>
                <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="type" disabled
                    onchange="getOptionType(this)">
                    <option value="">{{ get_phrase('Select an option') }}</option>
                    <option @if ($question->type == 'mcq') selected @endif value="mcq">
                        {{ get_phrase('Multiple Choice') }}</option>
                    <option @if ($question->type == 'fill_blanks') selected @endif value="fill_blanks">
                        {{ get_phrase('Fill in the blanks') }}</option>
                    <option @if ($question->type == 'true_false') selected @endif value="true_false">
                        {{ get_phrase('True or False') }}</option>
                </select>
                         <input type="hidden" name="type" value="{{ $question->type }}">

            </div>
        </div>
    </div>


    <div class="fpb-7 mb-3">
        <label for="title" class="form-label ol-form-label">
            {{ get_phrase('Write question') }}
            <span class="text-danger ms-1">*</span>
        </label>
        <textarea name="title" id="editor1">{!! $question->title !!}</textarea>
    </div>

    <div class="load-question-type"></div>

    <div class="d-flex gap-3">


        <a href="#" class="btn ol-btn-primary" id="questionBackBtn"
            onclick="ajaxModal('{{ route('modal', ['bankquestions::questions.index', 'id' => $quiz->id]) }}', '{{ get_phrase('Questions') }}', 'modal-xl')">
            <i class="fi-rr-angle-small-left"></i> {{ get_phrase('Back') }}
        </a>

               {{-- <a href="#" class="btn ol-btn-primary" id="questionBackBtn"
            onclick="ajaxModal('{{ route('modal', ['admin.questions.index', 'id' => $question->quiz_id]) }}', '{{ get_phrase('Questions') }}', 'modal-xl')">
            <i class="fi-rr-angle-small-left"></i> {{ get_phrase('Back') }}
        </a> --}}
        <div class="fpb7">
            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Update Question') }}</button>
        </div>
    </div>
</form>

@include('admin.init')
<script>
    "use strict";

    function initBankQuestionEditEditor() {
        if (typeof ensureCkEditor !== 'function') {
            setTimeout(initBankQuestionEditEditor, 150);
            return;
        }

        ensureCkEditor(function () {
            if (!document.getElementById('editor1')) {
                return;
            }

            if (CKEDITOR.instances.editor1) {
                try { CKEDITOR.instances.editor1.destroy(true); } catch (e) {}
            }

            CKEDITOR.replace('editor1', {
                language: 'ar',
                contentsLangDirection: 'rtl',
                height: 220,
                allowedContent: true
            });

            $('form.ajaxForm').on('submit', function () {
                if (CKEDITOR.instances.editor1) {
                    CKEDITOR.instances.editor1.updateElement();
                }
            });
        });
    }

    if (typeof ensureTagify === 'function') {
        ensureTagify(function () {});
    }

    initBankQuestionEditEditor();
    setupQuestion("{{ $question->type }}");

    function getOptionType(elem) {
        setupQuestion(elem.value);
    }

    function setupQuestion(type) {
        if (!type) {
            return;
        }

        $.ajax({
            type: 'get',
            url: "{{ route('admin.bank.question.type') }}",
            data: {
                id: "{{ $question->id }}",
                type: type,
            },
            success: function (response) {
                $('.load-question-type').empty().append(response);
            }
        });
    }

    function responseBack() {
        window.location.reload();
    }
</script>

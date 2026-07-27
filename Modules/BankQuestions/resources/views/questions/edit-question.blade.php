@php
    $question = \Modules\BankQuestions\App\Models\BankQuestions::where('id', $id)->first();
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

    <div class="row mb-3">
        <div class="col-sm-12 fpb-7">
            <label class="form-label ol-form-label">
                {{ get_phrase('Category') }}
                <span class="text-danger ms-1">*</span>
            </label>
            <select class="form-control ol-form-control ol-select2" data-toggle="select2" id="category" name="category_id" data-placeholder="Type to search...">
                <option value="all" disabled>{{ get_phrase('All') }}</option>

                @foreach (App\Models\Category::where('parent_id', 0)->orderBy('title', 'desc')->get() as $category)
                    <option class="text-center" disabled>
                        {{ $category->title }}</option>

                    @foreach ($category->bank_category as $sub_category)
                        <option @selected($sub_category->id == $question->category_id) value="{{ $sub_category->id }}">
                            {{"-- ".$category->title}} | {{ $sub_category->title }}
                        </option>
                    @endforeach
                @endforeach
            </select>
        </div>
    </div>

{{-- {{$question->quizs}} --}}
    <div class="row mb-3">
        <div class="col-sm-12 fpb-7">
            <label class="form-label ol-form-label">
                {{ get_phrase('quiz') }}
                <span class="text-danger ms-1">*</span>
            </label>
            <select class="form-control ol-form-control ol-select2" multiple data-toggle="select2" id="quiz_id" name="quiz_id[]" data-placeholder="Type to search...">
                <option disabled >{{ get_phrase('choose') }}</option>
                @foreach ($question->category->quizs as $quiz)
                    <option
                    @selected(in_array($quiz->id, array_column($question->quizs->toArray(), 'id')))
                    value="{{$quiz->id}}" >{{ $quiz->title }}</option>
                @endforeach
            </select>
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
        <div class="fpb7">
            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Update Question') }}</button>
        </div>
    </div>
</form>

@include('admin.init')

<script>

    CKEDITOR.replace('editor1');

    // Sync before submit
    $('form.ajaxForm').on('submit', function() {
        CKEDITOR.instances.editor1.updateElement();
    });

</script>
<script>


    $(document).ready(function() {
        $('#category').on('change', function() {
            var categoryId = $(this).val();
            if (categoryId) {
                $.ajax({
                    url: '{{ route("admin.bank.quizs.using.category", ":id") }}'.replace(':id', categoryId),
                    type: 'GET',
                    success: function(response) {
                        $('#quiz_id').empty();
                        $('#quiz_id').append('<option selected disapled>Chose</option>');
                        $.each(response, function(key, value) {
                            $('#quiz_id').append('<option value="' + value.id + '">' + value.title + '</option>');
                        });
                    }
                });
            } else {
                $('#quiz_id').empty().append('<option disabled value="">اختر عنصر</option>');
            }
        });
    });

    //    $(document).ready(function() {
    //     $('#category').on('change', function() {
    //         var categoryId = $(this).val();
    //         $('#quiz_id').empty();
    //         $('#quiz_id').append(
    //             '<option id="NotQuiz" value="0">{{ get_phrase('لا يوجد كويزات') }}</option>');

    //         if (categoryId) {
    //             $.ajax({
    //                 url: '{{ route('admin.bank.quizs.using.category', ':id') }}'.replace(':id',
    //                     categoryId),
    //                 type: 'GET',
    //                 success: function(response) {
    //                     $.each(response, function(key, value) {
    //                         $('#quiz_id').append(
    //                             '<option class="onlyQuiz" value="' + value.id +
    //                             '">' + value.title + '</option>');
    //                     });
    //                 }
    //             });
    //         }
    //     });


        // $('#quiz_id').on('change', function() {
        //     var selected = $(this).val() || [];
        //     if (selected.includes("0")) {
        //         $('.onlyQuiz').prop('disabled', true).prop('selected', false);
        //     } else {
        //         $('#NotQuiz').prop('disabled', true).prop('selected', false);
        //         $('.onlyQuiz').prop('disabled', false);
        //     }

        //     if (selected.length === 0) {
        //         $('#NotQuiz').prop('disabled', false);
        //         $('.onlyQuiz').prop('disabled', false);
        //     }

        //     $('#quiz_id').trigger('change.select2');
        // });
    });
    setupQuestion("{{ $question->type }}");

    function getOptionType(elem) {
        let type = elem.value;
        setupQuestion(type);
    }

    function setupQuestion(type) {
        if (type) {
            $.ajax({
                type: "get",
                url: "{{ route('admin.bank.question.type') }}",
                data: {
                    id: "{{ $question->id }}",
                    type: type,
                },
                success: function(response) {
                    $('.load-question-type').empty().append(response)
                }
            });
        }
    }

    // after response this function will call

</script>
<script>
      function responseBack() {
        window.location.reload();
    }
</script>

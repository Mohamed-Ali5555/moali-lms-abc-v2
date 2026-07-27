@php
    $question = App\Models\Question::where('id', $id)->first();
@endphp
<style>
    .select2-selection.select2-selection--multiple {
        cursor: pointer !important;
    }

    #options,
    #answer-select2 {

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

<form class="ajaxForm" action="{{ route('admin.course.question.update', $id) }}" method="post">@csrf

    <input type="hidden" name="quiz_id" value="{{ $question->quiz_id }}">
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

    {{-- <div class="card p-3 shadow-sm border-0 mb-3">
        <label class="form-label fw-bold">Question image</label>
        <div class="input-group">
            <input type="file" id="question-image"  name="question_image" accept="image/*" class="form-control">
        </div>
        <div id="preview" class="mt-3 d-flex flex-wrap gap-2">
            @if($question->question_image)
                <img src="/{{ ltrim($question->question_image, '/') }}" class="img-thumbnail" alt="Question Image" style="max-width: 200px; max-height: 200px; object-fit: contain;" onerror="this.src='/uploads/system/placeholder.png'">
            @endif
        </div>
    </div> --}}


    <div class="load-question-type"></div>

    <div class="d-flex gap-3">
        <a href="#" class="btn ol-btn-primary" id="questionBackBtn"
            onclick="ajaxModal('{{ route('modal', ['admin.questions.index', 'id' => $question->quiz_id]) }}', '{{ get_phrase('Questions') }}', 'modal-xl')">
            <i class="fi-rr-angle-small-left"></i> {{ get_phrase('Back') }}
        </a>

        <div class="fpb7">
            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Update Question') }}</button>
        </div>
    </div>
</form>

@include('admin.init')
<script>
    document.getElementById('question-image').addEventListener('change', function (event) {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';

        const file = event.target.files[0];
        if (!file) return;


        if (!file.type.startsWith('image/')) {
            alert('من فضلك اختر صورة فقط');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-thumbnail';
            img.style.width = '150px';
            img.style.height = '150px';
            img.style.objectFit = 'cover';

            preview.appendChild(img);
        };

        reader.readAsDataURL(file);
    });
</script>
<script>
    ensureCkEditor(function() {
        CKEDITOR.replace('editor1');
        $('form.ajaxForm').on('submit', function() {
            CKEDITOR.instances.editor1.updateElement();
        });
    });
</script>
<script>
    setupQuestion("{{ $question->type }}");

    function getOptionType(elem) {
        let type = elem.value;
        setupQuestion(type);
    }

    function setupQuestion(type) {
        if (type) {
            $.ajax({
                type: "get",
                url: "{{ route('admin.load.question.type') }}",
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
    function responseBack() {
        document.querySelector('#questionBackBtn').click();
    }
</script>

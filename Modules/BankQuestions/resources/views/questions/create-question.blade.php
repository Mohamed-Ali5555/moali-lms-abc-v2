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
<form class="ajaxForm" action="{{ route('admin.bank.question.store') }}" method="post">@csrf
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
                        <option value="{{ $sub_category->id }}">
                            {{"-- ".$category->title}} | {{ $sub_category->title }} </option>
                    @endforeach
                @endforeach
            </select>
        </div>
    </div>


    <div class="row mb-3">
        <div class="col-sm-12 fpb-7">
            <label class="form-label ol-form-label">
                {{ get_phrase('quiz') }}
                <span class="text-danger ms-1">*</span>
            </label>
            <select class="form-control ol-form-control ol-select2" multiple data-toggle="select2" id="quiz_id" name="quiz_id[]" data-placeholder="Type to search...">
                <option disabled >{{ get_phrase('cheose') }}</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="mb-3">
                <label class="form-label ol-form-label">
                    {{ get_phrase('Question Type') }}
                    <span class="text-danger ms-1">*</span>
                </label>
                <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="type"
                    onchange="getOptionType(this)" id="question-type-select">
                    <option value="" selected disabled>{{ get_phrase('Select an option') }}</option>
                    <option value="mcq">{{ get_phrase('Multiple Choice') }}</option>
                    <option value="fill_blanks">{{ get_phrase('Fill in the blanks') }}</option>
                    <option value="true_false">{{ get_phrase('True or False') }}</option>
                </select>
            </div>
        </div>
    </div>



        <div class="fpb-7 mb-3">
            <label for="title" class="form-label ol-form-label">
                {{ get_phrase('Write question') }}
                <span class="text-danger ms-1">*</span>
            </label>

            <textarea name="title"  id="editor1"></textarea>
        </div>



    <div class="load-question-type"></div>


    <div class="d-flex gap-3">
        <div class="fpb7">
            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Add Question') }}</button>
        </div>
    </div>
</form>


@include('admin.init')

<script>
    var editor1;
    var processingPaste = false;
    var editorInitialized = false;
    var optionEditors = {};

    // دالة تحويل base64 image إلى File object
    function base64ToFile(base64String, filename) {
        var arr = base64String.split(',');
        var mime = arr[0].match(/:(.*?);/)[1];
        var bstr = atob(arr[1]);
        var n = bstr.length;
        var u8arr = new Uint8Array(n);
        
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        
        return new File([u8arr], filename, {type: mime});
    }

    // دالة رفع الصورة إلى endpoint
    function uploadImageToServer(file) {
        return new Promise(function(resolve, reject) {
            var formData = new FormData();
            formData.append('upload', file);
            formData.append('_token', '{{ csrf_token() }}');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("admin.ckeditor.upload") }}', true);
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.uploaded === 1) {
                            resolve(response.url);
                        } else {
                            reject(new Error(response.error ? response.error.message : 'فشل رفع الصورة'));
                        }
                    } catch(e) {
                        reject(new Error('خطأ في معالجة الاستجابة'));
                    }
                } else {
                    reject(new Error('خطأ في الاتصال بالخادم'));
                }
            };
            
            xhr.onerror = function() {
                reject(new Error('خطأ في الاتصال'));
            };
            
            xhr.send(formData);
        });
    }

    // دالة معالجة المحتوى من CKEditor
    async function processPastedContent(html, skipPreview) {
        skipPreview = skipPreview !== undefined ? skipPreview : false;

        var temp = document.createElement('div');
        temp.innerHTML = html;

        // معالجة الصور base64 ورفعها إلى الخادم
        var images = temp.querySelectorAll('img[src^="data:image"], img[src^="blob:"]');
        
        for (var i = 0; i < images.length; i++) {
            var img = images[i];
            var src = img.getAttribute('src') || '';
            
            if (src.startsWith('data:image')) {
                var filename = 'pasted-image-' + Date.now() + '-' + i + '.png';
                var file = base64ToFile(src, filename);
                
                img.setAttribute('data-uploading', 'true');
                img.style.opacity = '0.5';
                
                try {
                    var imageUrl = await uploadImageToServer(file);
                    img.setAttribute('src', imageUrl);
                    img.removeAttribute('data-uploading');
                    img.style.opacity = '1';
                    img.style.maxWidth = '100%';
                    img.style.height = 'auto';
                } catch(error) {
                    console.error('Error uploading image:', error);
                    img.removeAttribute('data-uploading');
                    img.style.opacity = '1';
                }
            } else if (src.startsWith('blob:')) {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                var imgElement = new Image();
                imgElement.crossOrigin = 'anonymous';
                
                try {
                    await new Promise(function(resolve, reject) {
                        imgElement.onload = resolve;
                        imgElement.onerror = reject;
                        imgElement.src = src;
                    });
                    
                    canvas.width = imgElement.width;
                    canvas.height = imgElement.height;
                    ctx.drawImage(imgElement, 0, 0);
                    
                    var base64 = canvas.toDataURL('image/png');
                    var filename = 'pasted-image-' + Date.now() + '-' + i + '.png';
                    var file = base64ToFile(base64, filename);
                    
                    img.setAttribute('data-uploading', 'true');
                    img.style.opacity = '0.5';
                    
                    var imageUrl = await uploadImageToServer(file);
                    img.setAttribute('src', imageUrl);
                    img.removeAttribute('data-uploading');
                    img.style.opacity = '1';
                    img.style.maxWidth = '100%';
                    img.style.height = 'auto';
                } catch(error) {
                    console.error('Error processing blob image:', error);
                    img.removeAttribute('data-uploading');
                    img.style.opacity = '1';
                }
            }
        }

        var content = temp.innerHTML;

        // معالجة المعادلات
        content = content.replace(
            /([A-Za-z₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹]+)\s*\/\s*([A-Za-z₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹]+)/g,
            '<span class="math-fraction" dir="ltr"><span class="numerator">$1</span><span class="denominator">$2</span></span>'
        );

        content = content.replace(
            /\(([^)]*[A-Za-z₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹][^)]*)\)/g,
            '<span class="math-expr" dir="ltr">($1)</span>'
        );

        content = content.replace(
            /([A-Z])\s+(في|هي|هو)\s+([A-Za-z]*[₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹]+)/g,
            '<span class="math-expr" dir="ltr">$1</span> $2 <span class="math-expr" dir="ltr">$3</span>'
        );

        content = content.replace(
            /\b([A-Za-z][₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹]+)\b/g,
            '<span class="math-expr" dir="ltr">$1</span>'
        );

        return content;
    }

    // ==================== تهيئة المحرر الرئيسي ====================
    function initEditor() {
        if (editorInitialized && CKEDITOR.instances['editor1']) {
            console.log('Editor already initialized');
            return;
        }

        var editorElement = document.getElementById('editor1');
        if (!editorElement) {
            console.error('Editor element not found');
            editorInitialized = false;
            return;
        }

        // تدمير المحرر إذا كان موجود
        if (CKEDITOR.instances['editor1']) {
            try {
                CKEDITOR.instances['editor1'].destroy(true);
            } catch(e) {
                console.log('Error destroying old editor1:', e);
            }
        }

        // تفريغ محتوى textarea قبل initialization
        if (editorElement) {
            editorElement.value = '';
        }

        setTimeout(function() {
            try {
                editor1 = CKEDITOR.replace('editor1', {
                    language: 'ar',
                    contentsLangDirection: 'rtl',
                    height: 400,
                    allowedContent: true,
                    enterMode: CKEDITOR.ENTER_P,
                    shiftEnterMode: CKEDITOR.ENTER_BR,
                    fillEmptyBlocks: false,
                    forcePasteAsPlainText: false,
                    pasteFromWordRemoveFontStyles: false,
                    pasteFromWordRemoveStyles: false,
                    image_previewText: ' ',
                    filebrowserImageBrowseUrl: false,
                    filebrowserUploadUrl: '{{ route("admin.ckeditor.upload") }}?_token={{ csrf_token() }}',
                    filebrowserUploadMethod: 'form',
                    removeDialogTabs: 'image:advanced;image:Link'
                });

                // إضافة event listeners بعد initialization
                editor1.on('change', function() {
                    this.updateElement();
                });

                editor1.on('paste', function(evt) {
                    if (processingPaste) return;

                    processingPaste = true;
                    var data = evt.data;
                    var pastedContent = data.dataValue;

                    if (pastedContent && pastedContent.length > 10) {
                        // skipPreview: true لأن الصور في محرر السؤال مش لازم تتحط في preview
                        data.dataValue = processPastedContent(pastedContent, true);
                    }

                    setTimeout(function() {
                        processingPaste = false;
                    }, 1000);

                    this.updateElement();
                });

                editorInitialized = true;
                console.log('✓ Main editor initialized successfully');

            } catch(e) {
                console.error('✗ Error initializing editor:', e);
                editorInitialized = false;
            }
        }, 200);
    }
</script>
<script>

    // $(document).ready(function() {
    //     $('#category').on('change', function() {
    //         var categoryId = $(this).val();
    //         if (categoryId) {
    //             $.ajax({
    //                 url: '{{ route("admin.bank.quizs.using.category", ":id") }}'.replace(':id', categoryId),
    //                 type: 'GET',
    //                 success: function(response) {
    //                     $('#quiz_id').empty();
    //                     $.each(response, function(key, value) {
    //                         $('#quiz_id').append('<option value="' + value.id + '">' + value.title + '</option>');
    //                     });
    //                 }
    //             });
    //         } else {
    //             $('#quiz_id').empty().append('<option disabled value="">اختر عنصر</option>');
    //         }
    //     });
    // });
    $(document).ready(function() {
        console.log('Document ready - initializing...');

        // تهيئة المحرر الرئيسي
        setTimeout(function() {
            if (document.getElementById('editor1')) {
                initEditor();
            }

            // تحديد نوع السؤال تلقائياً على Multiple Choice
            var typeSelect = document.getElementById('question-type-select');
            if (typeSelect && (!typeSelect.value || typeSelect.value === '')) {
                typeSelect.value = 'mcq';

                // استدعاء setupQuestion مباشرة عشان يحمل المحتوى
                console.log('Auto-loading MCQ options on page load...');
                setupQuestion('mcq');

                // تحديث Select2 بعد تحميل المحتوى
                setTimeout(function() {
                    if ($(typeSelect).hasClass('select2-hidden-accessible')) {
                        $(typeSelect).val('mcq').trigger('change.select2');
                    } else if (typeof $ !== 'undefined' && $.fn.select2) {
                        // إذا Select2 مش متحمل بعد، استدعيه يدوياً
                        $(typeSelect).select2();
                        $(typeSelect).val('mcq').trigger('change.select2');
                    }
                }, 1000);
            }
        }, 300);

        $('#category').on('change', function() {
            var categoryId = $(this).val();
            $('#quiz_id').empty();
            $('#quiz_id').append(
                '<option id="NotQuiz" value="0">{{ get_phrase('لا يوجد كويزات') }}</option>');

            if (categoryId) {
                $.ajax({
                    url: '{{ route('admin.bank.quizs.using.category', ':id') }}'.replace(':id',
                        categoryId),
                    type: 'GET',
                    success: function(response) {
                        $.each(response, function(key, value) {
                            $('#quiz_id').append(
                                '<option class="onlyQuiz" value="' + value.id +
                                '">' + value.title + '</option>');
                        });
                    }
                });
            }
        });


        $('#quiz_id').on('change', function() {
            var selected = $(this).val() || [];
            if (selected.includes("0")) {
                $('.onlyQuiz').prop('disabled', true).prop('selected', false);
            } else {
                $('#NotQuiz').prop('disabled', true).prop('selected', false);
                $('.onlyQuiz').prop('disabled', false);
            }

            if (selected.length === 0) {
                $('#NotQuiz').prop('disabled', false);
                $('.onlyQuiz').prop('disabled', false);
            }

            $('#quiz_id').trigger('change.select2');
        });
    });
    function getOptionType(elem) {
        let type = elem.value;
        console.log('Question type changed to:', type);
        setupQuestion(type);
    }

    function setupQuestion(type) {
        if (type) {
            console.log('setupQuestion called with type:', type);

            $.ajax({
                type: "get",
                url: "{{ route('admin.bank.question.type') }}",
                data: {
                    type: type
                },
                success: function(response) {
                    console.log('Question type loaded, response length:', response.length);
                    $('.load-question-type').html(response);

                    setTimeout(function() {
                        try {
                            console.log('Attempting to initialize option editors...');
                            var textareas = document.querySelectorAll('.load-question-type textarea');
                            console.log('Found textareas:', textareas.length);
                        } catch(e) {
                            console.error('Error initializing option editors:', e);
                        }
                    }, 700);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading question type:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                }
            });
        }
    }

    // after response this function will call
    function responseBack() {
        window.location.reload();
    }
</script>

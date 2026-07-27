<style>
    #preview img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }
    .option-box {
        border: 1px solid #e1e1e1;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        background: #fafafa;
        transition: 0.2s;
    }
    .option-box:hover {
        background: #f1f1f1;
        transform: translateY(-2px);
    }
    .option-box span {
        font-weight: 500;
    }
    .correct {
        color: #198754;
        font-weight: bold;
    }
</style>

<div class="alert alert-warning" role="alert" dir="rtl">
  ⚠️ تنبيه: عند تعديل السؤال وإضافة إجابة جديدة، يُرجى إعادة اختيار الإجابة الصحيحة مرة أخرى.
</div>

{{-- الاختيارات --}}
<div class="mb-3">
    <label for="options" class="form-label fw-bold">الاختيارات</label>
    <input id="options" value="{{$question->options}}" name="options" class="form-control tagify" type="text"
           placeholder="أضف اختيارات (نص أو صورة)">
    <input type="hidden" name="options_data" id="options_data" value="">
</div>

{{-- إضافة صورة --}}
<div class="card p-3 shadow-sm border-0 mb-3">
    <label class="form-label fw-bold">إضافة صورة كخيار</label>
    <div class="input-group">
        <input type="file" id="image-option" accept="image/*" class="form-control">
        <button type="button" id="btn-add-image" class="btn btn-primary">
            <i class="bi bi-image me-1"></i> إضافة صورة
        </button>
    </div>
    <div id="preview" class="mt-3 d-flex flex-wrap gap-2"></div>
</div>

<div class="mb-3">
    <label for="answer-select2" class="form-label fw-bold">الإجابات الصحيحة</label>
    <select id="answer-select2" name="answer[]" multiple="multiple" style="width:100%" class="form-select">
        @foreach (json_decode($question->options, true) ?? [] as $option)
            <option value="{{ $option }}"
                data-img="{{ preg_match('/\.(jpg|jpeg|png|gif)$/i', $option) ? asset($option) : '' }}"
                @if(in_array($option, json_decode($question->answer, true) ?? [])) selected @endif>
                {{ preg_match('/\.(jpg|jpeg|png|gif)$/i', $option) ? '' : $option }}
            </option>
        @endforeach
    </select>
</div>

<script>
$('#answer-select2').select2({
    closeOnSelect: false,
    templateResult: function (option) {
        if (!option.id) return option.text;

        let img = $(option.element).data('img');
        if (img) {
            return $('<span><img src="' + img + '" style="width:40px;height:40px;object-fit:cover;margin-right:8px;border-radius:5px;"> ' + option.text + '</span>');
        }
        return option.text;
    },
    templateSelection: function (option) {
        let img = $(option.element).data('img');
        if (img) {
            return $('<span><img src="' + img + '" style="width:20px;height:20px;object-fit:cover;margin-right:5px;border-radius:3px;"> ' + option.text + '</span>');
        }
        return option.text;
    }
});
    window.optionStorageUrl = "{{ asset('uploads/questions') }}";

    var inputElm   = document.querySelector('#options');
    var imageInput = document.querySelector('#image-option');
    var btnAddImage = document.querySelector('#btn-add-image');
    var preview    = document.querySelector('#preview');
    var hiddenInput = document.querySelector('#options_data');

    if (inputElm && inputElm.tagify) {
        inputElm.tagify.destroy();
    }

    var tagify = new Tagify(inputElm, {
        maxTags: 20,
        dropdown: {
            enabled: 0,
            closeOnSelect: false
        }
    });

    var tempImages = window.tempImages || {}; 

    // تهيئة البيانات الأصلية
    function initializeData() {
        try {
            var options = JSON.parse('{{ $question->options }}');
            var answers = JSON.parse('{{ $question->answer }}');
            
            // تحويل options إلى تنسيق Tagify
            var tagifyData = options.map(option => ({ value: option }));
            tagify.addTags(tagifyData);
            
            // تحديث hidden input بالبيانات الأصلية
            updateHiddenInput();
        } catch (e) {
            console.error('خطأ في تحميل البيانات:', e);
        }
    }

    // Select2 مع الصور
    $('#answer-select2').select2({
        templateResult: formatState,
        templateSelection: formatState,
        escapeMarkup: m => m
    });

    function formatState(state){
        if(!state.id) return state.text;
        var $el = $(state.element);
        var img = $el.data('img');
        if(img){
            return `<span style="display:flex;align-items:center;">
                        <img src="${img}" style="height:40px;width:60px;object-fit:cover;margin-right:8px;border-radius:4px;" />
                        <span>${state.text || ''}</span>
                    </span>`;
        }
        return state.text;
    }

    function genTempName(file){
        var ext = (file.name.split('.').pop() || 'jpg').toLowerCase();
        var rnd = Math.random().toString(36).slice(2,8);
        return `tmp_${Date.now()}_${rnd}.${ext}`;
    }

    btnAddImage.addEventListener('click', ()=>{
        var f = imageInput.files[0];
        if(!f) return alert('اختر صورة أولاً');
        var tempName = genTempName(f);
        var previewUrl = URL.createObjectURL(f);

        tempImages[tempName] = { file: f, previewUrl };

        tagify.addTags([{ value: tempName }]);

        var img = document.createElement('img');
        img.src = previewUrl;
        img.dataset.temp = tempName;
        preview.appendChild(img);

        imageInput.value = '';
        updateSelectOptions();
        updateHiddenInput();
    });

    inputElm.addEventListener('change', ()=>{
        updateSelectOptions();
        updateHiddenInput();
    });
    $('#answer-select2').on('change', updateHiddenInput);

    function updateSelectOptions(){
        let arr = [];
        try { arr = JSON.parse(inputElm.value || '[]'); } catch(e) { arr = []; }

        var $sel = $('#answer-select2');
        $sel.empty();

        arr.forEach(item => {
            var v = (typeof item === 'string') ? item : item.value || '';
            var isTemp = !!tempImages[v];
            let dataImg = null;
            var text = v;
            if(isTemp) {
                dataImg = tempImages[v].previewUrl;
                text = '';
            }
            else if (/\.(jpe?g|png|gif|webp|svg)$/i.test(v)) {
                dataImg = window.optionStorageUrl + '/' + v.split('/').pop();
                text = '';
            }

            //var text = isTemp ? 'صورة' : v;

            var $opt = $('<option>').val(v).text(text);
            if(dataImg) $opt.attr('data-img', dataImg);
            $sel.append($opt);
        });

        $sel.trigger('change.select2');
    }

    async function updateHiddenInput(){
        let optionsArr = [];
        try { optionsArr = JSON.parse(inputElm.value || '[]'); } catch(e) { optionsArr = []; }

        var answers = $('#answer-select2').val() || [];

        var promises = Object.entries(tempImages).map(([name, obj]) => {
            return new Promise(resolve => {
                var reader = new FileReader();
                reader.onload = e => resolve({ name, base64: e.target.result });
                reader.readAsDataURL(obj.file);
            });
        });

        var base64Images = await Promise.all(promises);

        var payload = {
            options: optionsArr,
            answers: answers,
            images: base64Images
        };

        hiddenInput.value = JSON.stringify(payload);
    }

    // تهيئة البيانات عند تحميل الصفحة
    $(document).ready(function() {
        initializeData();
    });
</script>
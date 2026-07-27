<style>
    .q-mcq-panel { direction: rtl; }

    .q-options-list {
        display: grid;
        gap: 10px;
    }

    .q-option-row {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto auto;
        gap: 10px;
        align-items: center;
        padding: 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        transition: .2s ease;
    }

    .q-option-row.is-correct {
        border-color: #0d9488;
        background: rgba(13, 148, 136, 0.06);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.08);
    }

    .q-option-letter {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        color: #0f172a;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .q-option-row.is-correct .q-option-letter {
        background: rgba(13, 148, 136, 0.15);
        border-color: rgba(13, 148, 136, 0.3);
        color: #0f766e;
    }

    .q-option-body {
        min-width: 0;
    }

    .q-option-input {
        width: 100%;
        min-height: 44px;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 10px 12px;
        font-weight: 500;
    }

    .q-option-input:focus {
        border-color: #0d9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12) !important;
        outline: none;
    }

    .q-option-media {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 44px;
    }

    .q-option-media img {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .q-option-media-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }

    .q-option-math {
        padding: 8px 10px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 16px;
        line-height: 1.4;
        direction: rtl;
    }

    .q-option-check {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        margin: 0;
    }

    .q-option-check input {
        width: 18px;
        height: 18px;
        margin: 0;
        accent-color: #0d9488;
        cursor: pointer;
    }

    .q-option-row.is-correct .q-option-check {
        border-color: #0d9488;
        background: rgba(13, 148, 136, 0.1);
        color: #0f766e;
    }

    .q-option-remove {
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 12px;
        background: #fee2e2;
        color: #dc2626;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .q-option-remove:hover {
        background: #fecaca;
    }

    .q-options-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }

    .q-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 16px;
        border-radius: 12px;
        border: 1.5px dashed #94a3b8;
        background: #fff;
        color: #0f172a;
        font-weight: 700;
        cursor: pointer;
    }

    .q-add-btn:hover {
        border-color: #0d9488;
        color: #0d9488;
        background: rgba(13, 148, 136, 0.04);
    }

    .q-add-btn--solid {
        border-style: solid;
        border-color: #0d9488;
        background: #0d9488;
        color: #fff;
    }

    .q-add-btn--solid:hover {
        background: #0f766e;
        color: #fff;
    }

    .q-math-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 46px;
        border: 1.5px dashed #94a3b8;
        border-radius: 12px;
        background: #fff;
        color: #0f172a;
        font-weight: 700;
        transition: .2s ease;
    }

    .q-math-toggle:hover,
    .q-math-toggle.is-open {
        border-color: #0d9488;
        color: #0f766e;
        background: rgba(13, 148, 136, 0.06);
    }

    .q-math-toggle.is-open {
        border-style: solid;
    }

    .q-math-help {
        display: grid;
        gap: 8px;
        margin: 0 0 14px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        font-size: 12.5px;
        color: #475569;
        line-height: 1.55;
    }

    .q-math-help code {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 6px;
        background: #fff;
        border: 1px solid #e2e8f0;
        font-size: 12px;
        color: #0f172a;
        direction: ltr;
    }

    .q-math-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .q-option-math .math-expr,
    #math-editor-container .cke_editable .math-expr {
        direction: ltr !important;
        unicode-bidi: embed !important;
        display: inline-block !important;
        font-family: 'Cambria Math', Arial, sans-serif !important;
        white-space: nowrap !important;
    }

    .q-option-math .math-fraction,
    #math-editor-container .cke_editable .math-fraction {
        direction: ltr !important;
        display: inline-flex !important;
        flex-direction: column !important;
        align-items: center !important;
        vertical-align: middle !important;
        margin: 0 3px !important;
        font-family: 'Cambria Math', Arial, sans-serif !important;
    }

    .q-option-math .math-fraction .numerator,
    #math-editor-container .cke_editable .math-fraction .numerator {
        border-bottom: 1px solid #333 !important;
        padding: 2px 5px !important;
        min-width: 20px !important;
        text-align: center !important;
    }

    .q-option-math .math-fraction .denominator,
    #math-editor-container .cke_editable .math-fraction .denominator {
        padding: 2px 5px !important;
        min-width: 20px !important;
        text-align: center !important;
    }

    @media (max-width: 575.98px) {
        .q-option-row {
            grid-template-columns: 36px minmax(0, 1fr) 40px;
            grid-template-areas:
                "letter body remove"
                "letter check check";
        }

        .q-option-letter { grid-area: letter; width: 36px; height: 36px; }
        .q-option-body { grid-area: body; }
        .q-option-check { grid-area: check; justify-self: start; }
        .q-option-remove { grid-area: remove; }

        .q-math-actions { grid-template-columns: 1fr; }
    }
</style>

<div class="q-mcq-panel">
    <div class="lesson-form-section">
        <h6 class="lesson-form-section__title">{{ get_phrase('الاختيارات') }}</h6>
        <p class="tf-help mb-3">{{ get_phrase('أضف الاختيارات بزر + وعلّم على الإجابة الصحيحة بالتشيك بوكس') }}</p>

        <div id="q-options-list" class="q-options-list"></div>

        <div class="q-options-actions">
            <button type="button" id="btn-add-option" class="q-add-btn q-add-btn--solid">
                <i class="fi-rr-plus"></i>
                {{ get_phrase('إضافة اختيار') }}
            </button>
            <label class="q-add-btn" for="image-option" style="margin:0;cursor:pointer;">
                <i class="fi-rr-picture"></i>
                {{ get_phrase('إضافة صورة') }}
            </label>
            <input type="file" id="image-option" accept="image/*" class="d-none">
        </div>

        <input type="hidden" name="options_data" id="options_data">
        <div id="q-answer-fields"></div>
    </div>

    <div class="lesson-form-section">
        <h6 class="lesson-form-section__title">{{ get_phrase('معادلة رياضية') }}</h6>
        <button type="button" id="btn-toggle-math-editor" class="q-math-toggle">
            <i class="fi-rr-calculator"></i>
            <span>{{ get_phrase('إضافة معادلة رياضية') }}</span>
        </button>

        <div id="math-editor-container" style="display: none; margin-top: 14px;">
            <div class="q-math-help">
                <div><strong>كيف تستخدم محرر المعادلات؟</strong></div>
                <div>1) اكتب أو الصق المعادلة داخل المحرر.</div>
                <div>2) الكسور تُكتب هكذا: <code>a/b</code> والأُس هكذا: <code>x²</code>.</div>
                <div>3) <strong>إضافة كنص</strong>: يضيف المعادلة كاختيار نصي منسّق.</div>
                <div>4) <strong>تحويل إلى صورة</strong>: يحوّل المعادلة لصورة ويضيفها كاختيار.</div>
            </div>

            <label class="form-label ol-form-label" for="math-editor">{{ get_phrase('محرر المعادلات') }}</label>
            <textarea id="math-editor" name="math-editor" rows="8" class="form-control mb-3"></textarea>

            <div class="q-math-actions">
                <button type="button" id="btn-add-math-text" class="tf-btn tf-btn--ghost">
                    <i class="fi-rr-text"></i>
                    {{ get_phrase('إضافة كنص') }}
                </button>
                <button type="button" id="btn-convert-math" class="tf-btn tf-btn--primary">
                    <i class="fi-rr-picture"></i>
                    {{ get_phrase('تحويل إلى صورة') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/global/html2canvas/html2canvas.min.js') }}"></script>

<script>
(function initBankMcqBuilder() {
    var listEl = document.getElementById('q-options-list');
    var hiddenInput = document.getElementById('options_data');
    var answerFields = document.getElementById('q-answer-fields');
    var btnAddOption = document.getElementById('btn-add-option');
    var imageInput = document.getElementById('image-option');
    var btnToggleMathEditor = document.getElementById('btn-toggle-math-editor');
    var mathEditorContainer = document.getElementById('math-editor-container');
    var btnAddMathText = document.getElementById('btn-add-math-text');
    var btnConvertMath = document.getElementById('btn-convert-math');
    var mathEditorInstance = null;

    if (!listEl || !hiddenInput) {
        return;
    }

    var letters = ['أ', 'ب', 'ج', 'د', 'هـ', 'و', 'ز', 'ح', 'ط', 'ي'];
    var options = [];
    var tempImages = {};

    function uid() {
        return 'opt_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7);
    }

    function genTempName(file) {
        var ext = ((file && file.name ? file.name.split('.').pop() : 'png') || 'png').toLowerCase();
        return 'tmp_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8) + '.' + ext;
    }

    function processMathExpressions(text) {
        if (!text || typeof text !== 'string') return text;
        var processed = text;
        processed = processed.replace(
            /([A-Za-z0-9₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹]+)\s*\/\s*([A-Za-z0-9₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹]+)/g,
            '<span class="math-fraction" dir="ltr"><span class="numerator">$1</span><span class="denominator">$2</span></span>'
        );
        processed = processed.replace(
            /([A-Za-z0-9]+)([⁰¹²³⁴⁵⁶⁷⁸⁹]+)/g,
            '<span class="math-expr" dir="ltr">$1$2</span>'
        );
        return processed;
    }

    function hasMathExpression(text) {
        if (!text || typeof text !== 'string') return false;
        return /\S+\s*\/\s*\S+/.test(text) || /[⁰¹²³⁴⁵⁶⁷⁸⁹]/.test(text);
    }

    function addOption(data) {
        if (options.length >= 10) {
            alert('الحد الأقصى 10 اختيارات');
            return;
        }

        options.push({
            id: uid(),
            value: (data && data.value) || '',
            html: (data && data.html) || '',
            isImage: !!(data && data.isImage),
            previewUrl: (data && data.previewUrl) || '',
            correct: !!(data && data.correct)
        });

        render();
    }

    function removeOption(id) {
        var opt = options.find(function (o) { return o.id === id; });
        if (opt && opt.isImage && tempImages[opt.value]) {
            delete tempImages[opt.value];
        }
        options = options.filter(function (o) { return o.id !== id; });
        if (options.length === 0) {
            addOption({});
            addOption({});
            return;
        }
        render();
    }

    function render() {
        listEl.innerHTML = '';

        options.forEach(function (opt, index) {
            var row = document.createElement('div');
            row.className = 'q-option-row' + (opt.correct ? ' is-correct' : '');
            row.dataset.id = opt.id;

            var letter = letters[index] || String(index + 1);

            var bodyHtml = '';
            if (opt.isImage) {
                bodyHtml =
                    '<div class="q-option-media">' +
                        '<img src="' + (opt.previewUrl || '') + '" alt="">' +
                        '<span class="q-option-media-label">اختيار بصورة</span>' +
                    '</div>';
            } else if (opt.html) {
                bodyHtml =
                    '<div class="q-option-math">' + opt.html + '</div>' +
                    '<input type="hidden" class="q-option-value" value="' + escapeAttr(opt.value) + '">';
            } else {
                bodyHtml =
                    '<input type="text" class="form-control q-option-input" placeholder="اكتب نص الاختيار هنا" value="' + escapeAttr(opt.value) + '">';
            }

            row.innerHTML =
                '<div class="q-option-letter">' + letter + '</div>' +
                '<div class="q-option-body">' + bodyHtml + '</div>' +
                '<label class="q-option-check">' +
                    '<input type="checkbox" class="q-option-correct"' + (opt.correct ? ' checked' : '') + '>' +
                    '<span>إجابة صحيحة</span>' +
                '</label>' +
                '<button type="button" class="q-option-remove" title="حذف">' +
                    '<i class="fi-rr-trash"></i>' +
                '</button>';

            listEl.appendChild(row);
        });

        syncPayload();
    }

    function escapeAttr(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function fileToBase64(file) {
        return new Promise(function (resolve) {
            var reader = new FileReader();
            reader.onload = function (e) {
                resolve(e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    function syncFromDom() {
        listEl.querySelectorAll('.q-option-row').forEach(function (row) {
            var id = row.dataset.id;
            var opt = options.find(function (o) { return o.id === id; });
            if (!opt) return;

            var input = row.querySelector('.q-option-input');
            if (input && !opt.isImage && !opt.html) {
                opt.value = input.value.trim();
            }

            var check = row.querySelector('.q-option-correct');
            opt.correct = !!(check && check.checked);
            row.classList.toggle('is-correct', opt.correct);
        });
    }

    function syncPayload() {
        syncFromDom();

        var optionsArr = [];
        var answers = [];

        options.forEach(function (opt) {
            if (!opt.value && !opt.isImage && !opt.html) {
                return;
            }

            var item = { value: opt.value };
            if (opt.html) {
                item.html = opt.html;
            }
            optionsArr.push(item);

            if (opt.correct && opt.value) {
                answers.push(opt.value);
            }
        });

        var images = Object.keys(tempImages).map(function (name) {
            return {
                name: name,
                base64: tempImages[name].base64 || ''
            };
        }).filter(function (img) {
            return !!img.base64;
        });

        hiddenInput.value = JSON.stringify({
            options: optionsArr,
            answers: answers,
            images: images
        });

        answerFields.innerHTML = '';
        if (answers.length === 0) {
            var empty = document.createElement('input');
            empty.type = 'hidden';
            empty.name = 'answer';
            empty.value = '';
            answerFields.appendChild(empty);
        } else {
            answers.forEach(function (ans) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'answer[]';
                input.value = ans;
                answerFields.appendChild(input);
            });
        }
    }

    listEl.addEventListener('input', function (e) {
        if (e.target.classList.contains('q-option-input')) {
            syncPayload();
        }
    });

    listEl.addEventListener('change', function (e) {
        if (!e.target.classList.contains('q-option-correct')) {
            return;
        }
        var row = e.target.closest('.q-option-row');
        if (!row) return;
        var opt = options.find(function (o) { return o.id === row.dataset.id; });
        if (!opt) return;
        opt.correct = !!e.target.checked;
        row.classList.toggle('is-correct', opt.correct);
        syncPayload();
    });

    listEl.addEventListener('click', function (e) {
        var btn = e.target.closest('.q-option-remove');
        if (!btn) return;
        var row = btn.closest('.q-option-row');
        if (!row) return;
        removeOption(row.dataset.id);
    });

    btnAddOption.addEventListener('click', function () {
        addOption({});
        var lastInput = listEl.querySelector('.q-option-row:last-child .q-option-input');
        if (lastInput) lastInput.focus();
    });

    imageInput.addEventListener('change', function () {
        var file = imageInput.files && imageInput.files[0];
        if (!file) return;

        var tempName = genTempName(file);
        var previewUrl = URL.createObjectURL(file);

        fileToBase64(file).then(function (base64) {
            tempImages[tempName] = { file: file, previewUrl: previewUrl, base64: base64 };
            addOption({
                value: tempName,
                isImage: true,
                previewUrl: previewUrl
            });
        });

        imageInput.value = '';
    });

    function base64ToFile(base64String, filename) {
        var arr = base64String.split(',');
        var mime = arr[0].match(/:(.*?);/)[1];
        var bstr = atob(arr[1]);
        var n = bstr.length;
        var u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, { type: mime });
    }

    function processPastedContent(html) {
        var temp = document.createElement('div');
        temp.innerHTML = html;

        var paragraphs = temp.querySelectorAll('p, div, span, body');
        if (paragraphs.length === 0) {
            paragraphs = [temp];
        }

        paragraphs.forEach(function (p) {
            if (!p.textContent.trim()) return;
            var protectedContent = p.innerHTML;
            protectedContent = protectedContent.replace(
                /([A-Za-z0-9₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹]+)\s*\/\s*([A-Za-z0-9₀₁₂₃₄₅₆₇₈₉⁰¹²³⁴⁵⁶⁷⁸⁹]+)/g,
                '<span class="math-fraction" dir="ltr"><span class="numerator">$1</span><span class="denominator">$2</span></span>'
            );
            protectedContent = protectedContent.replace(
                /([A-Za-z0-9]+)([⁰¹²³⁴⁵⁶⁷⁸⁹]+)/g,
                '<span class="math-expr" dir="ltr">$1$2</span>'
            );
            p.innerHTML = protectedContent;
        });

        return temp.innerHTML;
    }

    if (btnToggleMathEditor && mathEditorContainer) {
        btnToggleMathEditor.addEventListener('click', function (e) {
            e.preventDefault();
            var isVisible = mathEditorContainer.style.display !== 'none';

            if (isVisible) {
                mathEditorContainer.style.display = 'none';
                btnToggleMathEditor.classList.remove('is-open');
                btnToggleMathEditor.innerHTML = '<i class="fi-rr-calculator"></i><span>إضافة معادلة رياضية</span>';
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['math-editor']) {
                    CKEDITOR.instances['math-editor'].destroy(true);
                    mathEditorInstance = null;
                }
                return;
            }

            mathEditorContainer.style.display = 'block';
            btnToggleMathEditor.classList.add('is-open');
            btnToggleMathEditor.innerHTML = '<i class="fi-rr-cross-small"></i><span>إخفاء محرر المعادلات</span>';

            function initMathEditor() {
                if (typeof CKEDITOR === 'undefined') {
                    if (typeof ensureCkEditor === 'function') {
                        ensureCkEditor(initMathEditor);
                    } else {
                        setTimeout(initMathEditor, 200);
                    }
                    return;
                }

                var mathEditorElement = document.getElementById('math-editor');
                if (!mathEditorElement) {
                    setTimeout(initMathEditor, 200);
                    return;
                }

                if (CKEDITOR.instances['math-editor']) {
                    try { CKEDITOR.instances['math-editor'].destroy(true); } catch (err) {}
                }

                mathEditorElement.value = '';
                mathEditorInstance = CKEDITOR.replace('math-editor', {
                    language: 'ar',
                    contentsLangDirection: 'rtl',
                    height: 240,
                    allowedContent: true,
                    filebrowserUploadUrl: '{{ route("admin.ckeditor.upload") }}?_token={{ csrf_token() }}',
                    filebrowserUploadMethod: 'form',
                    removeDialogTabs: 'image:advanced;image:Link'
                });

                mathEditorInstance.on('paste', function (evt) {
                    if (!evt.data.dataValue) return;
                    evt.data.dataValue = processPastedContent(evt.data.dataValue);
                });
            }

            if (typeof ensureCkEditor === 'function') {
                ensureCkEditor(initMathEditor);
            } else {
                setTimeout(initMathEditor, 200);
            }
        });
    }

    if (btnAddMathText) {
        btnAddMathText.addEventListener('click', function (e) {
            e.preventDefault();
            if (!mathEditorInstance) {
                alert('المحرر غير جاهز');
                return;
            }

            var content = mathEditorInstance.getData();
            if (!content || !content.trim()) {
                alert('اكتب معادلة أولاً');
                return;
            }

            var temp = document.createElement('div');
            temp.innerHTML = content;
            var text = (temp.textContent || temp.innerText || '').trim();
            if (!text) {
                alert('اكتب معادلة أولاً');
                return;
            }

            var html = processPastedContent(content);
            addOption({
                value: text,
                html: html
            });

            mathEditorInstance.setData('');
        });
    }

    if (btnConvertMath) {
        btnConvertMath.addEventListener('click', function (e) {
            e.preventDefault();
            if (!mathEditorInstance) {
                alert('المحرر غير جاهز');
                return;
            }

            var content = mathEditorInstance.getData();
            if (!content || !content.trim()) {
                alert('اكتب معادلة أولاً');
                return;
            }

            btnConvertMath.disabled = true;
            btnConvertMath.innerHTML = '<i class="fi-rr-time-past"></i> جاري التحويل...';

            var tempDiv = document.createElement('div');
            tempDiv.style.position = 'absolute';
            tempDiv.style.left = '-9999px';
            tempDiv.style.padding = '20px';
            tempDiv.style.background = 'white';
            tempDiv.style.direction = 'rtl';
            tempDiv.style.fontSize = '24px';
            tempDiv.innerHTML = content;
            document.body.appendChild(tempDiv);

            html2canvas(tempDiv, {
                backgroundColor: '#ffffff',
                scale: 2,
                logging: false,
                useCORS: true
            }).then(function (canvas) {
                canvas.toBlob(function (blob) {
                    var tempName = genTempName({ name: 'math-equation.png' });
                    var file = new File([blob], tempName, { type: 'image/png' });
                    var previewUrl = URL.createObjectURL(blob);

                    fileToBase64(file).then(function (base64) {
                        tempImages[tempName] = { file: file, previewUrl: previewUrl, base64: base64 };
                        addOption({
                            value: tempName,
                            isImage: true,
                            previewUrl: previewUrl
                        });
                    });

                    document.body.removeChild(tempDiv);
                    mathEditorInstance.setData('');
                    mathEditorContainer.style.display = 'none';
                    btnToggleMathEditor.classList.remove('is-open');
                    btnToggleMathEditor.innerHTML = '<i class="fi-rr-calculator"></i><span>إضافة معادلة رياضية</span>';

                    btnConvertMath.disabled = false;
                    btnConvertMath.innerHTML = '<i class="fi-rr-picture"></i> تحويل إلى صورة';
                }, 'image/png');
            }).catch(function () {
                if (tempDiv.parentNode) document.body.removeChild(tempDiv);
                alert('حدث خطأ أثناء تحويل المعادلة إلى صورة');
                btnConvertMath.disabled = false;
                btnConvertMath.innerHTML = '<i class="fi-rr-picture"></i> تحويل إلى صورة';
            });
        });
    }

    // Keep payload fresh right before AJAX submit
    var form = listEl.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            syncPayload();
        });
    }

    // Start with 4 empty choices
    addOption({});
    addOption({});
    addOption({});
    addOption({});
})();
</script>

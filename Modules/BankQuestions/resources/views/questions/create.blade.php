<style>
    .q-type-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        min-height: 140px;
        margin: 12px 0;
        padding: 24px;
        border-radius: 14px;
        border: 1.5px dashed #cbd5e1;
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
    }

    .q-type-loading__spinner {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 3px solid #e2e8f0;
        border-top-color: #0d9488;
        animation: q-type-spin .7s linear infinite;
    }

    @keyframes q-type-spin {
        to { transform: rotate(360deg); }
    }

    .q-create-wizard.is-loading-type .tf-choice {
        pointer-events: none;
        opacity: .65;
    }
</style>

<div class="quiz-wizard lesson-wizard tf-modal-form q-create-wizard" dir="rtl">
    <div class="lesson-wizard__banner">
        <div>
            <p class="lesson-wizard__eyebrow">{{ get_phrase('بنك الأسئلة') }}</p>
            <h5 class="lesson-wizard__title">{{ get_phrase('إضافة سؤال') }}</h5>
            <p class="lesson-wizard__desc">{{ get_phrase('اختر نوع السؤال، اكتب نصه، ثم حدد الإجابات') }}</p>
        </div>
        <div class="lesson-wizard__course">
            <span>{{ get_phrase('النوع') }}</span>
            <strong id="qTypeBadge">{{ get_phrase('غير محدد') }}</strong>
        </div>
    </div>

    <form class="ajaxForm" action="{{ route('admin.bank.question.store') }}" method="post">
        @csrf
        <input type="hidden" name="quiz_id" value="{{ $id }}">
        <input type="hidden" name="category_id" value="{{ @$category_id }}">

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('نوع السؤال') }}</h6>
            <div class="tf-choice tf-choice--3">
                <label for="q_type_mcq">
                    <input type="radio" name="type" id="q_type_mcq" value="mcq" required
                        onchange="onQuestionTypeChange(this)">
                    <span class="tf-choice__card">
                        <span>
                            <strong>{{ get_phrase('اختيار من متعدد') }}</strong>
                            <small>{{ get_phrase('إجابة صحيحة واحدة أو أكثر') }}</small>
                        </span>
                    </span>
                </label>
                <label for="q_type_fill">
                    <input type="radio" name="type" id="q_type_fill" value="fill_blanks"
                        onchange="onQuestionTypeChange(this)">
                    <span class="tf-choice__card">
                        <span>
                            <strong>{{ get_phrase('أكمل الفراغ') }}</strong>
                            <small>{{ get_phrase('الطالب يكتب الإجابة بنفسه') }}</small>
                        </span>
                    </span>
                </label>
                <label for="q_type_tf">
                    <input type="radio" name="type" id="q_type_tf" value="true_false"
                        onchange="onQuestionTypeChange(this)">
                    <span class="tf-choice__card">
                        <span>
                            <strong>{{ get_phrase('صح أو خطأ') }}</strong>
                            <small>{{ get_phrase('اختيار صحيح أو خطأ فقط') }}</small>
                        </span>
                    </span>
                </label>
            </div>
        </div>

        <div class="lesson-form-section">
            <h6 class="lesson-form-section__title">{{ get_phrase('نص السؤال') }}</h6>
            <div class="mb-0">
                <textarea name="title" id="editor1" required></textarea>
                <small class="tf-help">{{ get_phrase('يمكنك لصق نص أو صور أو معادلات داخل المحرر') }}</small>
            </div>
        </div>

        <div class="load-question-type"></div>

        <div class="lesson-wizard__footer">
            <a href="#" class="tf-btn tf-btn--ghost" id="questionBackBtn"
                onclick="ajaxModal('{{ route('modal', ['bankquestions::questions.index', 'id' => $id]) }}', '{{ get_phrase('الأسئلة') }}', 'modal-xl'); return false;">
                <i class="fi-rr-angle-small-left"></i>
                {{ get_phrase('رجوع') }}
            </a>
            <button type="submit" class="tf-btn tf-btn--primary">
                <i class="fi-rr-check"></i>
                {{ get_phrase('إضافة السؤال') }}
            </button>
        </div>
    </form>
</div>

@include('admin.init')
<script>
    "use strict";

    var qTypeLabels = {
        mcq: @json(get_phrase('اختيار من متعدد')),
        fill_blanks: @json(get_phrase('أكمل الفراغ')),
        true_false: @json(get_phrase('صح أو خطأ'))
    };

    var qTypeRequest = null;

    function initBankQuestionEditor() {
        if (typeof ensureCkEditor !== 'function') {
            setTimeout(initBankQuestionEditor, 150);
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

            CKEDITOR.instances.editor1.on('change', function () {
                this.updateElement();
            });
            CKEDITOR.instances.editor1.on('paste', function () {
                this.updateElement();
            });
        });
    }

    if (typeof ensureTagify === 'function') {
        ensureTagify(function () {});
    }

    initBankQuestionEditor();

    function onQuestionTypeChange(elem) {
        var type = elem.value;
        var badge = document.getElementById('qTypeBadge');
        if (badge) {
            badge.textContent = qTypeLabels[type] || type;
        }
        setupQuestion(type);
    }

    function showQuestionTypeLoading() {
        $('.q-create-wizard').addClass('is-loading-type');
        $('.load-question-type').html(
            '<div class="q-type-loading" dir="rtl">' +
                '<div class="q-type-loading__spinner"></div>' +
                '<div>{{ get_phrase('جاري تحميل حقول النوع...') }}</div>' +
            '</div>'
        );
    }

    function hideQuestionTypeLoading() {
        $('.q-create-wizard').removeClass('is-loading-type');
    }

    function setupQuestion(type) {
        if (!type) {
            return;
        }

        if (qTypeRequest) {
            qTypeRequest.abort();
        }

        // Destroy math editor instance if it exists before replacing the panel
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['math-editor']) {
            try { CKEDITOR.instances['math-editor'].destroy(true); } catch (e) {}
        }

        showQuestionTypeLoading();

        qTypeRequest = $.ajax({
            type: 'get',
            url: "{{ route('admin.bank.question.type') }}",
            data: { type: type },
            success: function (response) {
                $('.load-question-type').empty().append(response);
            },
            error: function (xhr, status) {
                if (status === 'abort') {
                    return;
                }
                $('.load-question-type').html(
                    '<div class="q-type-loading" dir="rtl">' +
                        '<div>{{ get_phrase('تعذر تحميل الحقول، حاول مرة أخرى') }}</div>' +
                    '</div>'
                );
            },
            complete: function () {
                hideQuestionTypeLoading();
                qTypeRequest = null;
            }
        });
    }

    // Keep modal open after save — clear fields so another question can be added
    function responseBack() {
        if (typeof CKEDITOR !== 'undefined') {
            if (CKEDITOR.instances.editor1) {
                CKEDITOR.instances.editor1.setData('');
                CKEDITOR.instances.editor1.updateElement();
            }
            if (CKEDITOR.instances['math-editor']) {
                try { CKEDITOR.instances['math-editor'].destroy(true); } catch (e) {}
            }
        }

        $('.form-validation-error-label').remove();

        var selectedType = $('input[name="type"]:checked').val();
        if (selectedType) {
            setupQuestion(selectedType);
        } else {
            $('.load-question-type').empty();
        }

        var $modalBody = $('#ajaxModal .modal-body');
        if ($modalBody.length) {
            $modalBody.animate({ scrollTop: 0 }, 200);
        }

        var badge = document.getElementById('qTypeBadge');
        if (badge && selectedType) {
            badge.textContent = qTypeLabels[selectedType] || selectedType;
        }
    }

    // Sync CKEditor content into textarea before AJAX submit
    $(document).off('submit.bankQuestionCreate', '.q-create-wizard form.ajaxForm')
        .on('submit.bankQuestionCreate', '.q-create-wizard form.ajaxForm', function () {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor1) {
                CKEDITOR.instances.editor1.updateElement();
            }
        });
</script>

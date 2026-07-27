<div class="lesson-form-section" dir="rtl">
    <h6 class="lesson-form-section__title">{{ get_phrase('الإجابة') }}</h6>
    <label class="form-label ol-form-label" for="answer">
        {{ get_phrase('الإجابة/الإجابات الصحيحة') }}
        <span class="text-danger ms-1">*</span>
    </label>
    <input class="form-control ol-form-control js-fill-answer" type="text" name="answer" id="answer"
        placeholder="{{ get_phrase('اكتب الإجابة ثم اضغط Enter') }}" required>
    <small class="tf-help">{{ get_phrase('يمكنك إضافة أكثر من إجابة. اكتب كل إجابة ثم اضغط Enter.') }}</small>
</div>

<script>
(function initFillBlanksAnswer() {
    if (typeof ensureTagify !== 'function') {
        setTimeout(initFillBlanksAnswer, 150);
        return;
    }

    ensureTagify(function () {
        var el = document.querySelector('#answer');
        if (!el) {
            return;
        }
        if (el.tagify) {
            try { el.tagify.destroy(); } catch (e) {}
        }
        new Tagify(el, {
            placeholder: @json(get_phrase('اكتب الإجابة ثم اضغط Enter'))
        });
        el.classList.add('inited');
    });
})();
</script>

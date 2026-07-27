<div class="lesson-form-section" dir="rtl">
    <h6 class="lesson-form-section__title">{{ get_phrase('الإجابة') }}</h6>
    <div class="tf-choice tf-choice--2">
        <label for="true">
            <input type="radio" name="answer" id="true" value="true" required>
            <span class="tf-choice__card">
                <span>
                    <strong>{{ get_phrase('صح') }}</strong>
                    <small>{{ get_phrase('اعتبار العبارة صحيحة') }}</small>
                </span>
            </span>
        </label>
        <label for="false">
            <input type="radio" name="answer" id="false" value="false">
            <span class="tf-choice__card">
                <span>
                    <strong>{{ get_phrase('خطأ') }}</strong>
                    <small>{{ get_phrase('اعتبار العبارة خاطئة') }}</small>
                </span>
            </span>
        </label>
    </div>
</div>

<div class="lg-row" data-row>
    <div class="lg-row__head">
        <span class="lg-row__badge" data-index-label>#{{ $index + 1 }}</span>
        <button type="button" class="lg-row__remove" data-remove>
            <i class="fi-rr-trash"></i> {{ get_phrase('حذف') }}
        </button>
    </div>
    <div class="lg-field">
        <label>{{ get_phrase('عنوان البند') }}</label>
        <input type="text"
            name="{{ $type }}[{{ $index }}][title]"
            data-name-title
            value="{{ $title }}"
            required
            placeholder="{{ get_phrase('مثال: قبول الشروط') }}">
    </div>
    <div class="lg-field">
        <label>{{ get_phrase('المحتوى') }}</label>
        <textarea name="{{ $type }}[{{ $index }}][body]"
            data-name-body
            required
            rows="4"
            placeholder="{{ get_phrase('اكتب نص البند هنا...') }}">{{ $body }}</textarea>
    </div>
</div>

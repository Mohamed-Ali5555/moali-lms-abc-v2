@php
    $years = \App\Models\Category::where('parent_id', 0)->orderBy('sort', 'asc')->orderBy('title')->get();
    $prefillFrom = isset($from_id) ? (int) $from_id : (int) request('from_id', 0);
@endphp

<form action="{{ route('admin.category.transfer') }}" method="post" id="category-transfer-form" class="cat-transfer">
    @csrf

    <div class="cat-transfer__banner">
        <div>
            <p class="cat-transfer__eyebrow">{{ get_phrase('نقل جماعي') }}</p>
            <h5 class="cat-transfer__title">{{ get_phrase('نقل بين السنوات الدراسية') }}</h5>
            <p class="cat-transfer__desc">{{ get_phrase('انقل الطلبة والبيانات المرتبطة من سنة إلى أخرى دفعة واحدة') }}</p>
        </div>
        <div class="cat-transfer__flow" aria-hidden="true">
            <span class="cat-transfer__flow-dot">1</span>
            <span class="cat-transfer__flow-line"></span>
            <span class="cat-transfer__flow-dot">2</span>
        </div>
    </div>

    <div class="cat-transfer__grid">
        <div class="cat-transfer__field">
            <label class="form-label ol-form-label" for="transfer_from_id">
                {{ get_phrase('من السنة') }} <span class="text-danger">*</span>
            </label>
            <select class="form-control ol-form-control ol-select2" name="from_id" id="transfer_from_id" required>
                <option value="">{{ get_phrase('اختر السنة المصدر') }}</option>
                @foreach ($years as $year)
                    <option value="{{ $year->id }}" @selected($prefillFrom === (int) $year->id)>
                        {{ $year->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="cat-transfer__arrow" aria-hidden="true">
            <i class="fi-rr-exchange"></i>
        </div>

        <div class="cat-transfer__field">
            <label class="form-label ol-form-label" for="transfer_to_id">
                {{ get_phrase('إلى السنة') }} <span class="text-danger">*</span>
            </label>
            <select class="form-control ol-form-control ol-select2" name="to_id" id="transfer_to_id" required>
                <option value="">{{ get_phrase('اختر السنة الهدف') }}</option>
                @foreach ($years as $year)
                    <option value="{{ $year->id }}">{{ $year->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="cat-transfer__preview" id="transferPreview" hidden>
        <div class="cat-transfer__preview-head">
            <strong id="transferPreviewTitle">{{ get_phrase('معاينة النقل') }}</strong>
            <span class="cat-transfer__preview-hint" id="transferPreviewHint"></span>
        </div>
        <div class="cat-transfer__stats" id="transferStats">
            <div class="cat-transfer__stat" data-stat="students">
                <strong>0</strong>
                <span>{{ get_phrase('طلبة') }}</span>
            </div>
            <div class="cat-transfer__stat" data-stat="subjects">
                <strong>0</strong>
                <span>{{ get_phrase('مواد') }}</span>
            </div>
            <div class="cat-transfer__stat" data-stat="courses">
                <strong>0</strong>
                <span>{{ get_phrase('كورسات') }}</span>
            </div>
            <div class="cat-transfer__stat" data-stat="books">
                <strong>0</strong>
                <span>{{ get_phrase('كتب') }}</span>
            </div>
            <div class="cat-transfer__stat" data-stat="bank_categories">
                <strong>0</strong>
                <span>{{ get_phrase('بنك أسئلة') }}</span>
            </div>
        </div>
    </div>

    <div class="cat-transfer__options">
        <p class="cat-transfer__options-label">{{ get_phrase('ماذا تريد نقله؟') }}</p>

        <label class="cat-transfer__option is-recommended">
            <input type="checkbox" name="transfer_students" value="1" checked>
            <span class="cat-transfer__option-body">
                <span class="cat-transfer__option-icon"><i class="fi-rr-users"></i></span>
                <span class="cat-transfer__option-text">
                    <strong>{{ get_phrase('الطلبة') }}</strong>
                    <small>{{ get_phrase('نقل الصف الدراسي لجميع الطلبة في السنة المصدر') }}</small>
                </span>
                <span class="cat-transfer__option-tag">{{ get_phrase('موصى به') }}</span>
            </span>
        </label>

        <label class="cat-transfer__option">
            <input type="checkbox" name="transfer_subjects" value="1">
            <span class="cat-transfer__option-body">
                <span class="cat-transfer__option-icon"><i class="fi-rr-apps"></i></span>
                <span class="cat-transfer__option-text">
                    <strong>{{ get_phrase('المواد الفرعية') }}</strong>
                    <small>{{ get_phrase('نقل المواد تحت السنة الجديدة مع كورساتها') }}</small>
                </span>
            </span>
        </label>

        <label class="cat-transfer__option">
            <input type="checkbox" name="transfer_books" value="1">
            <span class="cat-transfer__option-body">
                <span class="cat-transfer__option-icon"><i class="fi-rr-book-alt"></i></span>
                <span class="cat-transfer__option-text">
                    <strong>{{ get_phrase('الكتب') }}</strong>
                    <small>{{ get_phrase('نقل كتب المكتبة المرتبطة بالسنة المصدر') }}</small>
                </span>
            </span>
        </label>

        <label class="cat-transfer__option">
            <input type="checkbox" name="transfer_bank" value="1">
            <span class="cat-transfer__option-body">
                <span class="cat-transfer__option-icon"><i class="fi-rr-clipboard"></i></span>
                <span class="cat-transfer__option-text">
                    <strong>{{ get_phrase('تصنيفات بنك الأسئلة') }}</strong>
                    <small>{{ get_phrase('نقل تصنيفات بنك الأسئلة المرتبطة بالسنة') }}</small>
                </span>
            </span>
        </label>
    </div>

    <div class="cat-transfer__confirm">
        <label class="cat-transfer__confirm-check">
            <input type="checkbox" name="confirm" value="1" required>
            <span>{{ get_phrase('أؤكد أنني راجعت المعاينة وأريد تنفيذ النقل الآن') }}</span>
        </label>
    </div>

    <button type="submit" class="btn ol-btn-primary w-100 cat-transfer__submit" id="transferSubmitBtn" disabled>
        <i class="fi-rr-shuffle me-1"></i>
        <span>{{ get_phrase('تنفيذ النقل') }}</span>
    </button>
</form>

<script>
(function () {
    var $from = $('#transfer_from_id');
    var $to = $('#transfer_to_id');
    var $preview = $('#transferPreview');
    var $submit = $('#transferSubmitBtn');
    var previewUrl = @json(route('admin.category.transfer.preview'));
    var phrases = {
        pickBoth: @json(get_phrase('اختر السنة المصدر والهدف لعرض المعاينة')),
        same: @json(get_phrase('لا يمكن أن تكون السنة المصدر والهدف متطابقتين')),
        preview: @json(get_phrase('معاينة النقل')),
        loading: @json(get_phrase('جاري تحميل المعاينة...')),
        error: @json(get_phrase('تعذر تحميل المعاينة')),
        to: @json(get_phrase('إلى')),
    };
    var req = null;

    function syncTargetOptions() {
        var fromId = $from.val();
        $to.find('option').each(function () {
            var val = $(this).attr('value');
            if (!val) return;
            $(this).prop('disabled', val === fromId);
        });
        if ($to.val() && $to.val() === fromId) {
            $to.val('').trigger('change.select2');
        }
    }

    function setStat(key, value) {
        $preview.find('[data-stat="' + key + '"] strong').text(value);
    }

    function loadPreview() {
        var fromId = $from.val();
        var toId = $to.val();

        syncTargetOptions();

        if (!fromId || !toId) {
            $preview.prop('hidden', true);
            $submit.prop('disabled', true);
            return;
        }

        if (fromId === toId) {
            $preview.prop('hidden', false);
            $('#transferPreviewTitle').text(phrases.same);
            $('#transferPreviewHint').text('');
            setStat('students', 0);
            setStat('subjects', 0);
            setStat('courses', 0);
            setStat('books', 0);
            setStat('bank_categories', 0);
            $submit.prop('disabled', true);
            return;
        }

        $preview.prop('hidden', false);
        $('#transferPreviewTitle').text(phrases.loading);
        $('#transferPreviewHint').text('');
        $submit.prop('disabled', true);

        if (req) req.abort();

        req = $.get(previewUrl, { from_id: fromId, to_id: toId })
            .done(function (res) {
                if (!res || !res.ok) {
                    $('#transferPreviewTitle').text(phrases.error);
                    return;
                }
                $('#transferPreviewTitle').text(phrases.preview);
                $('#transferPreviewHint').text(res.from.title + ' → ' + res.to.title);
                setStat('students', res.counts.students);
                setStat('subjects', res.counts.subjects);
                setStat('courses', res.counts.courses);
                setStat('books', res.counts.books);
                setStat('bank_categories', res.counts.bank_categories);
                refreshSubmit();
            })
            .fail(function (xhr) {
                if (xhr.statusText === 'abort') return;
                var msg = phrases.error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#transferPreviewTitle').text(msg);
                $submit.prop('disabled', true);
            });
    }

    function refreshSubmit() {
        var hasOption = $('#category-transfer-form input[type="checkbox"][name^="transfer_"]:checked').length > 0;
        var confirmed = $('#category-transfer-form input[name="confirm"]').is(':checked');
        var fromId = $from.val();
        var toId = $to.val();
        $submit.prop('disabled', !(hasOption && confirmed && fromId && toId && fromId !== toId));
    }

    $from.on('change', loadPreview);
    $to.on('change', loadPreview);
    $('#category-transfer-form').on('change', 'input[type="checkbox"]', refreshSubmit);

    $('#category-transfer-form').on('submit', function () {
        $submit.prop('disabled', true).find('span').text(@json(get_phrase('جاري التنفيذ...')));
    });

    syncTargetOptions();
    if ($from.val()) {
        loadPreview();
    }
})();
</script>
@include('admin.init')

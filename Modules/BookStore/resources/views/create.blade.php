@php
    $categories = App\Models\Category::where('parent_id', 0)->orderBy('title', 'asc')->get();
@endphp

<form action="{{ route('admin.bookstore.store') }}" method="post" enctype="multipart/form-data" class="book-form tf-modal-form" id="book-create-form">
    @csrf
    <input type="hidden" name="parent_id" value="{{ $parent_id ?? 0 }}">

    <div class="book-form__banner">
        <div>
            <p class="book-form__eyebrow">{{ get_phrase('المكتبة') }}</p>
            <h5 class="book-form__title">{{ get_phrase('إضافة كتاب جديد') }}</h5>
            <p class="book-form__desc">{{ get_phrase('أدخل بيانات الكتاب والسعر والغلاف ثم احفظ') }}</p>
        </div>
        <div class="book-form__badge">
            <i class="fi-rr-book-alt"></i>
            <span>{{ get_phrase('كتاب جديد') }}</span>
        </div>
    </div>

    <div class="book-form__section">
        <div class="book-form__section-head">
            <span class="book-form__section-icon"><i class="fi-rr-info"></i></span>
            <div>
                <strong>{{ get_phrase('المعلومات الأساسية') }}</strong>
                <small>{{ get_phrase('السنة الدراسية واسم الكتاب') }}</small>
            </div>
        </div>

        <div class="book-form__row">
            <div class="book-form__field">
                <label for="category_id" class="form-label ol-form-label">
                    {{ get_phrase('Category') }} <span class="text-danger">*</span>
                </label>
                <select class="form-control ol-form-control ol-select2" name="category_id" id="category_id" required>
                    <option value="" disabled selected>{{ get_phrase('Select a category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="book-form__field">
                <label for="book_name" class="form-label ol-form-label">
                    {{ get_phrase('Book Name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" class="form-control ol-form-control" id="book_name"
                    value="{{ old('title') }}"
                    placeholder="{{ get_phrase('Enter your book name') }}"
                    aria-label="{{ get_phrase('Enter your book name') }}" required />
            </div>
        </div>
    </div>

    <div class="book-form__section">
        <div class="book-form__section-head">
            <span class="book-form__section-icon book-form__section-icon--amber"><i class="fi-rr-dollar"></i></span>
            <div>
                <strong>{{ get_phrase('التسعير') }}</strong>
                <small>{{ get_phrase('السعر الأساسي والخصم الاختياري') }}</small>
            </div>
        </div>

        <div class="book-form__row book-form__row--price">
            <div class="book-form__field">
                <label for="price" class="form-label ol-form-label">
                    {{ get_phrase('Price') }} <span class="text-danger">*</span>
                </label>
                <div class="book-form__price-wrap">
                    <input type="number" min="0" step="0.01" name="price" class="form-control ol-form-control"
                        oninput="this.value = Math.abs(this.value)" id="price"
                        value="{{ old('price') }}"
                        placeholder="0.00"
                        aria-label="{{ get_phrase('Enter your book price') }}" required />
                    <span class="book-form__currency">L.E</span>
                </div>
            </div>

            <div class="book-form__field">
                <label class="form-label ol-form-label">{{ get_phrase('Enable discount') }}</label>
                <label class="book-form__switch" for="if_discount">
                    <input type="checkbox" name="if_discount" value="1" id="if_discount" class="book-form__switch-input" />
                    <span class="book-form__switch-ui"></span>
                    <span class="book-form__switch-text" id="discountSwitchLabel">{{ get_phrase('غير مفعّل') }}</span>
                </label>
            </div>
        </div>

        <div class="book-form__discount" id="discount_price" hidden>
            <label for="discount_price_input" class="form-label ol-form-label">{{ get_phrase('Discount price') }}</label>
            <div class="book-form__price-wrap">
                <input type="number" min="0" step="0.01" name="discount_price" id="discount_price_input"
                    class="form-control ol-form-control" oninput="this.value = Math.abs(this.value)"
                    value="{{ old('discount_price') }}"
                    placeholder="0.00" />
                <span class="book-form__currency">L.E</span>
            </div>
            <small class="book-form__help">{{ get_phrase('يجب أن يكون أقل من السعر الأساسي') }}</small>
        </div>
    </div>

    <div class="book-form__section">
        <div class="book-form__section-head">
            <span class="book-form__section-icon book-form__section-icon--sky"><i class="fi-rr-document"></i></span>
            <div>
                <strong>{{ get_phrase('الوصف والغلاف') }}</strong>
                <small>{{ get_phrase('تفاصيل اختيارية وصورة العرض') }}</small>
            </div>
        </div>

        <div class="book-form__field">
            <label for="description" class="form-label ol-form-label">
                {{ get_phrase('Book Description') }}
                <small class="text-muted">({{ get_phrase('optional') }})</small>
            </label>
            <textarea name="description" id="description" rows="4"
                placeholder="{{ get_phrase('Enter Description') }}"
                class="form-control ol-form-control text_editor">{{ old('description') }}</textarea>
        </div>

        <div class="book-form__media">
            <div class="book-form__field book-form__field--grow">
                <label for="thumbnail" class="form-label ol-form-label">
                    {{ get_phrase('Thumbnail') }}
                    <small class="text-muted">({{ get_phrase('optional') }})</small>
                </label>
                <input type="file" name="thumbnail" class="form-control ol-form-control" id="thumbnail" accept="image/*" />
                <small class="book-form__help">{{ get_phrase('يفضّل صورة مربعة واضحة للغلاف') }}</small>
            </div>
            <div class="book-form__preview" id="thumbnailPreview" hidden>
                <img src="" alt="{{ get_phrase('Thumbnail') }}" id="thumbnailPreviewImg">
            </div>
        </div>
    </div>

    <div class="book-form__footer">
        <span class="book-form__hint">{{ get_phrase('يمكنك تعديل البيانات لاحقاً في أي وقت') }}</span>
        <button type="submit" class="tf-btn tf-btn--primary">
            <i class="fi-rr-check"></i>
            <span>{{ get_phrase('حفظ الكتاب') }}</span>
        </button>
    </div>
</form>

<script>
(function () {
    var $discount = $('#if_discount');
    var $box = $('#discount_price');
    var $label = $('#discountSwitchLabel');
    var onText = @json(get_phrase('مفعّل'));
    var offText = @json(get_phrase('غير مفعّل'));

    function syncDiscount() {
        var on = $discount.is(':checked');
        $label.text(on ? onText : offText);
        $box.prop('hidden', !on);
        if (!on) {
            $('#discount_price_input').val('');
        }
    }

    $discount.on('change', syncDiscount);
    syncDiscount();

    $('#thumbnail').on('change', function (e) {
        var file = e.target.files && e.target.files[0];
        var $preview = $('#thumbnailPreview');
        var $img = $('#thumbnailPreviewImg');
        if (!file || !file.type.match(/^image\//)) {
            $preview.prop('hidden', true);
            $img.attr('src', '');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (ev) {
            $img.attr('src', ev.target.result);
            $preview.prop('hidden', false);
        };
        reader.readAsDataURL(file);
    });
})();
</script>
@include('admin.init')

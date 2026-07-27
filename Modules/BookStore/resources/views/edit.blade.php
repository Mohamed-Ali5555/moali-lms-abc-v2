@php
    $book = Modules\BookStore\App\Models\Book::where('id', $id)->first();
    $parent_categories = App\Models\Category::where('parent_id', 0)->orderBy('title', 'asc')->get();
@endphp

@if ($book)
<form action="{{ route('admin.bookstore.update', $book->id) }}" method="post" enctype="multipart/form-data" class="book-form tf-modal-form" id="book-edit-form">
    @csrf

    <div class="book-form__banner">
        <div>
            <p class="book-form__eyebrow">{{ get_phrase('المكتبة') }}</p>
            <h5 class="book-form__title">{{ get_phrase('تعديل الكتاب') }}</h5>
            <p class="book-form__desc">{{ get_phrase('حدّث بيانات الكتاب أو السعر أو الغلاف') }}</p>
        </div>
        <div class="book-form__course">
            <span>{{ get_phrase('الكتاب الحالي') }}</span>
            <strong>{{ $book->title }}</strong>
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
                    <option value="">{{ get_phrase('Select a category') }}</option>
                    @foreach ($parent_categories as $parent_category)
                        <option value="{{ $parent_category->id }}" @selected((int) $book->category_id === (int) $parent_category->id)>
                            {{ $parent_category->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="book-form__field">
                <label for="book_name" class="form-label ol-form-label">
                    {{ get_phrase('Book Name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" class="form-control ol-form-control" value="{{ $book->title }}"
                    id="book_name" placeholder="{{ get_phrase('Enter your book name') }}"
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
                        placeholder="0.00"
                        aria-label="{{ get_phrase('Enter your book price') }}" value="{{ $book->price }}"
                        required />
                    <span class="book-form__currency">L.E</span>
                </div>
            </div>

            <div class="book-form__field">
                <label class="form-label ol-form-label">{{ get_phrase('Enable discount') }}</label>
                <label class="book-form__switch" for="if_discount">
                    <input type="checkbox" name="if_discount" value="1" id="if_discount"
                        class="book-form__switch-input" @checked((int) $book->if_discount === 1) />
                    <span class="book-form__switch-ui"></span>
                    <span class="book-form__switch-text" id="discountSwitchLabel">
                        {{ (int) $book->if_discount === 1 ? get_phrase('مفعّل') : get_phrase('غير مفعّل') }}
                    </span>
                </label>
            </div>
        </div>

        <div class="book-form__discount" id="discount_price" @if ((int) $book->if_discount !== 1) hidden @endif>
            <label for="discount_priceInput" class="form-label ol-form-label">{{ get_phrase('Discount price') }}</label>
            <div class="book-form__price-wrap">
                <input type="number" min="0" step="0.01" name="discount_price" id="discount_priceInput"
                    class="form-control ol-form-control" oninput="this.value = Math.abs(this.value)"
                    placeholder="0.00"
                    value="{{ $book->discount_price }}" />
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
            <textarea name="description" rows="4" class="form-control ol-form-control text_editor" id="description"
                placeholder="{{ get_phrase('Enter your description') }}"
                aria-label="{{ get_phrase('Enter your description') }}">{{ $book->disc }}</textarea>
        </div>

        <div class="book-form__media">
            <div class="book-form__field book-form__field--grow">
                <label for="thumbnail" class="form-label ol-form-label">
                    {{ get_phrase('Thumbnail') }}
                    <small class="text-muted">({{ get_phrase('optional') }})</small>
                </label>
                <input type="file" name="thumbnail" class="form-control ol-form-control" id="thumbnail" accept="image/*" />
                <small class="book-form__help">{{ get_phrase('اتركه فارغاً للإبقاء على الغلاف الحالي') }}</small>
            </div>
            <div class="book-form__preview" id="thumbnailPreview">
                <img src="{{ get_image($book->thumbnail) }}" alt="{{ $book->title }}" id="thumbnailPreviewImg">
            </div>
        </div>
    </div>

    <div class="book-form__footer">
        <span class="book-form__hint">{{ get_phrase('التغييرات تُحفظ فور الإرسال') }}</span>
        <button type="submit" class="tf-btn tf-btn--primary">
            <i class="fi-rr-check"></i>
            <span>{{ get_phrase('حفظ التعديلات') }}</span>
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
            $('#discount_priceInput').val('');
        }
    }

    $discount.on('change', syncDiscount);
    syncDiscount();

    $('#thumbnail').on('change', function (e) {
        var file = e.target.files && e.target.files[0];
        var $preview = $('#thumbnailPreview');
        var $img = $('#thumbnailPreviewImg');
        if (!file || !file.type.match(/^image\//)) {
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
@else
    <p class="text-danger mb-0">{{ get_phrase('Book not found.') }}</p>
@endif

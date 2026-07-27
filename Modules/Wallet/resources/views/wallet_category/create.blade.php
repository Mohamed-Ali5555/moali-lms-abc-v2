@php
    $users = App\Models\User::get();
@endphp

<form action="{{ route('admin.wallet_category.store') }}" method="post" enctype="multipart/form-data">
    @CSRF
    <div class="tf-modal-form">
        <div class="mb-3">
            <label for="balance" class="form-label ol-form-label">{{ get_phrase('balance') }}</label>
            <input type="number" name="balance" class="form-control ol-form-control" id="balance" oninput="this.value = Math.max(1, Math.abs(this.value))" required>
            <small class="form-text text-muted">{{ get_phrase('Amount to credit for this category wallet.') }}</small>
        </div>

        <div class="mb-3">
            <label for="icon-picker" class="form-label ol-form-label">{{ get_phrase('method') }}</label>
            <select class="form-control ol-form-control" name="type">
                <option value="" disabled>please select method of payment</option>
                <option value="by_hand" {{ old('type') == 'by_hand' ? 'selected' : '' }}>
                    كاش
                </option>
                <option value="gift" {{ old('type') == 'gift' ? 'selected' : '' }}>
                    هديه
                </option>
            </select>

            @error('type')
                <div id="validationServer04Feedback" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        @php 
        $categories = App\Models\Category::where('parent_id',0)->get();
        @endphp

        <div class="mb-3">
            <label for="type"
                class="form-label ol-form-label">{{ get_phrase('categorys') }}<span
                    class="text-danger ms-1">*</span></label>
            <select class="form-control ol-form-control" id="category_id"
                name="category_id" required>
                <option value="" disabled>please select category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                @endforeach
            </select>

            @error('category_id')
                <div id="validationServer04Feedback" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="note" class="form-label ol-form-label">{{ get_phrase(' note') }}
                <small class="text-muted">({{ get_phrase('optional') }})</small></label>
            <textarea name="note" rows="4" class="form-control ol-form-control" id="note"
                placeholder="{{ get_phrase('Enter your note') }}" aria-label="{{ get_phrase('Enter your note') }}"></textarea>
        </div>

        <div class="mb-2">
            <button type="submit" class="btn ol-btn-primary w-100 mt-2">{{ get_phrase('Submit') }}</button>
        </div>
    </div>
</form>


<script type="text/javascript">
    "use strict";

    $(function() {
        if ($('.icon-picker').length) {
            $('.icon-picker').iconpicker();
        }
    });
</script>

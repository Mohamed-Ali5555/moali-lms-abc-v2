@php
    $category = App\Models\Category::where('id', $id)->first();
    $parent_categories = App\Models\Category::where('parent_id', 0)
        ->where('id', '!=', $id)
        ->orderBy('title', 'asc')
        ->get();
@endphp
<form action="{{ route('admin.category.update', $category->id) }}" method="post" enctype="multipart/form-data">
    @CSRF

    <div class="tf-modal-form">
        <div class="mb-3">
            <label class="form-label ol-form-label">{{ get_phrase('Parent category') }}</label>
            <select class="form-control ol-form-control ol-select2" name="parent_id">
                <option value="0" @if ($category->parent_id == 0) selected @endif>
                    {{ get_phrase('- Mark it as parent -') }}</option>
                @foreach ($parent_categories as $parent_category)
                    <option value="{{ $parent_category->id }}" @if ($category->parent_id == $parent_category->id) selected @endif>
                        {{ $parent_category->title }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">{{ get_phrase('Leave as parent if this is a top-level category.') }}</small>
        </div>

        <div class="mb-3">
            <label for="category_name" class="form-label ol-form-label">{{ get_phrase('Category Name') }}</label>
            <input type="text" name="title" value="{{ $category->title }}" class="form-control ol-form-control"
                id="category_name" placeholder="{{ get_phrase('Enter your category name') }}"
                aria-label="{{ get_phrase('Enter your unique category name') }}" required />
        </div>

        <div class="mb-3">
            <label for="description" class="form-label ol-form-label">{{ get_phrase('Category Description') }}
                <small class="text-muted">({{ get_phrase('optional') }})</small></label>
            <textarea name="description" rows="4" class="form-control ol-form-control text_editor" id="description"
                placeholder="{{ get_phrase('Enter your description') }}"
                aria-label="{{ get_phrase('Enter your description') }}">{{ $category->description }}</textarea>
        </div>

        <div class="mb-3">
            <label for="thumbnail" class="form-label ol-form-label">{{ get_phrase('Choose category thumbnail') }}
                <small class="text-muted">({{ get_phrase('optional') }})</small></label>
            <input type="file" name="thumbnail" class="form-control ol-form-control" id="thumbnail"
                accept="image/*" />
            <small class="form-text text-muted">{{ get_phrase('Recommended size') }}: 200 x 200 px — {{ get_phrase('Leave empty to keep the current image.') }}</small>
        </div>

        <div class="mb-3">
            <label class="form-label ol-form-label" for="status">{{ get_phrase('Status') }}</label>
            <select class="form-control ol-form-control ol-select2" name="status" id="status" required>
                <option value="" disabled>{{ get_phrase('Choose status ...') }}</option>
                <option value="1" @if ($category->status == 1) selected @endif>
                    {{ get_phrase('Active') }}</option>
                <option value="0" @if ($category->status == 0) selected @endif>
                    {{ get_phrase('Inactive') }}</option>
            </select>
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
    $('.ol-select2').select2({
        dropdownParent: $("#ajaxModal")
    });
</script>
@include('admin.init')

<form action="{{ route('admin.category.store') }}" method="post" enctype="multipart/form-data">
    @CSRF
    <input type="hidden" name="parent_id" value="{{ $parent_id }}">

    <div class="tf-modal-form">
        <div class="mb-3">
            <label for="category_name" class="form-label ol-form-label">{{ get_phrase('Category Name') }}</label>
            <input type="text" name="title" class="form-control ol-form-control" id="category_name"
                placeholder="{{ get_phrase('Enter your category name') }}"
                aria-label="{{ get_phrase('Enter your unique category name') }}" required />
            <small class="form-text text-muted">{{ get_phrase('Use a clear name students will recognize.') }}</small>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label ol-form-label">{{ get_phrase('Category Description') }}
                <small class="text-muted">({{ get_phrase('optional') }})</small></label>
            <textarea name="description" rows="4" class="form-control ol-form-control text_editor" id="description"
                placeholder="{{ get_phrase('Enter your description') }}"
                aria-label="{{ get_phrase('Enter your description') }}"></textarea>
        </div>

        <div class="mb-3">
            <label for="thumbnail" class="form-label ol-form-label">{{ get_phrase('Thumbnail') }}
                <small class="text-muted">({{ get_phrase('optional') }})</small></label>
            <input type="file" name="thumbnail" class="form-control ol-form-control" id="thumbnail" accept="image/*" />
            <small class="form-text text-muted">{{ get_phrase('Recommended size') }}: 200 x 200 px</small>
        </div>

        <div class="mb-3">
            <label class="form-label ol-form-label" for="status">{{ get_phrase('Status') }}</label>
            <select class="form-control ol-form-control ol-select2" name="status" id="status" required>
                <option value="" disabled>{{ get_phrase('Choose status ...') }}</option>
                <option value="1">{{ get_phrase('Active') }}</option>
                <option value="0">{{ get_phrase('Inactive') }}</option>
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
</script>
@include('admin.init')

<form action="{{ route('admin.blog.category.store') }}" method="post">@csrf
    <div class="tf-modal-form">
        <div class="mb-3">
            <label class="form-label ol-form-label" for="title">{{ get_phrase('Title') }}</label>
            <input class="form-control ol-form-control" type="text" id="title" name="title" required>
            <small class="form-text text-muted">{{ get_phrase('Short name for this blog category.') }}</small>
        </div>
        <div class="mb-3">
            <label class="form-label ol-form-label" for="subtitle">
                {{ get_phrase('Subtitle') }} <small class="text-muted">{{ get_phrase('(80  Character)') }}</small>
            </label>
            <textarea class="form-control ol-form-control" rows="3" name="subtitle" id="subtitle" maxlength="80"></textarea>
        </div>

        <div class="mb-2">
            <button type="submit" class="btn ol-btn-primary w-100 mt-2">{{ get_phrase('Add category') }}</button>
        </div>
    </div>
</form>

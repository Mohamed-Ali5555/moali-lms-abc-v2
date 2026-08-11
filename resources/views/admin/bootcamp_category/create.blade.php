<form action="{{ route('admin.bootcamp.category.store') }}" method="post" enctype="multipart/form-data">@csrf
    <div class="fpb7 mb-2">
        <label class="form-label ol-form-label" for="title">{{ get_phrase('Title') }}</label>
        <input class="form-control ol-form-control" type="text" id="title" name="title" required>
    </div>
    <div class="fpb-7 mb-3">
        <label class="form-label ol-form-label">
            {{ get_phrase('p-Category') }}
            <span class="text-danger ms-1">*</span>
        </label>
        <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="category_id">
            <option value="" disabled>{{ get_phrase('Select a p-category') }}</option>
            @foreach (App\Models\Category::where('parent_id',0)->orderBy('title', 'asc')->get() as $category)
                <option value="{{ $category->id }}"> {{ $category->title }}</option>
            @endforeach
        </select>

    </div>
    <div class="mb-3">
        <label for="thumbnail" class="form-label ol-form-label">{{ get_phrase('Thumbnail') }} <small
                class="text-muted">({{ get_phrase('*') }})</small></label>
        <input type="file" name="thumbnail" class="form-control ol-form-control" id="thumbnail" accept="image/*" />
    </div>
    <div class="fpb7 mb-2">
        <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Add category') }}</button>
    </div>
</form>

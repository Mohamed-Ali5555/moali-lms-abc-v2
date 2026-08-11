<style>
    .image_preview {
        width: 100%;
        height: 250px;
        border-radius: 8px;
        overflow: hidden
    }

    .image_preview img{
        widows: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
</style>


<input type="hidden" name="course_type" value="general" required>
<input type="hidden" name="instructors[]" value="{{ auth()->user()->id }}" required>


<div class="row mb-3">
    <label for="title" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Bootcamp title') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-10">
        <input type="text" name="title" value="{{ $bootcamp_details->title }}" class="form-control ol-form-control"
            id="title" required>
    </div>
</div>


<div class="row mb-3">
    <label for="description"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Description') }}</label>
    <div class="col-sm-10">
        <textarea name="description" rows="5" class="form-control ol-form-control text_editor" id="description">{!! $bootcamp_details->description !!}</textarea>
    </div>
</div>

<div class="row mb-3">
    <label class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Category') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-10">
        <select class="ol-select2" name="category_id" data-minimum-results-for-search="Infinity" required>
            <option value="">{{ get_phrase('Select a category') }}</option>
            @foreach (App\Models\BootcampCategory::orderBy('title', 'asc')->get() as $category)
                <option value="{{ $category->id }}" @if ($bootcamp_details->category_id == $category->id) selected @endif>
                    {{ $category->title }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <label for="title" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Publish Date') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-10">
        <input type="date" name="publish_date" value="{{ date('Y-m-d', $bootcamp_details->publish_date) }}" class="form-control ol-form-control"
            id="title" required>
    </div>
</div>
<div class="row mb-3">
    <label for="thumbnail" class="form-label ol-form-label col-sm-2 col-form-label">{{get_phrase('Thumbnail')}}</label>
    <div class="col-sm-10">
        <input type="file" name="thumbnail" class="form-control ol-form-control" id="thumbnail" accept="image/*" />
    </div>

    <div class="offset-md-2 offset-lg-3 col-md-10 col-lg-6 fpb-7 mt-3">
        <div class="image_preview">
            <img src="{{ asset($bootcamp_details->thumbnail) }}" id="preview_thumbnail" width="100%" alt="blog-thumbnail">
        </div>
    </div>
</div>
@push('js')
    <script>
        $(function() {
            $('#banner, #thumbnail').change(function(e) {
                e.preventDefault();

                var img_type = $(this).attr('id');
                var x = URL.createObjectURL(event.target.files[0]);
                $('#preview_' + img_type).attr('src', x);
            });
        });
    </script>
@endpush

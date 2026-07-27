@php
    $newsletter = App\Models\Newsletter::where('id', $id)->first();
@endphp

<form action="{{ route('admin.newsletter.update', $id) }}" method="post">@csrf
    <div class="tf-modal-form">
        <div class="mb-3">
            <label for="subject" class="form-label ol-form-label">{{ get_phrase('Subject') }}</label>
            <input type="text" name="subject" class="form-control ol-form-control" id="subject" value="{{ $newsletter->subject }}" placeholder="{{ get_phrase('Subject') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label ol-form-label">{{ get_phrase('Description') }}</label>
            <textarea name="description" id="description" required>{{ $newsletter->description }}</textarea>
        </div>

        <div class="mb-2">
            <button type="submit" class="btn ol-btn-primary w-100 mt-2">{{ get_phrase('Submit') }}</button>
        </div>
    </div>
</form>

<script type="text/javascript">
    "use strict";

    $('#description').summernote({
        height: 180, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true, // set focus to editable area after initializing summernote
        toolbar: [
            ['color', ['color']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol']],
            ['table', ['table']],
            ['insert', ['link']]
        ]
    });
</script>

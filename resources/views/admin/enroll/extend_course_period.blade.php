@php
    $course = $course_id ? \App\Models\Course::find($course_id) : null;
    $subscribers_count = $course ? \App\Models\Enrollment::where('course_id', $course_id)->count() : 0;
@endphp
@if ($course)
<form action="{{ route('admin.enroll.history.extend_course', $course_id) }}" method="post">
    @csrf
    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label">{{ get_phrase('Course') }}</label>
        <input class="form-control ol-form-control" type="text" value="{{ $course->title }}" readonly>
    </div>

    <div class="fpb7 mb-3">
        <p class="text-muted mb-0">{{ get_phrase('Subscribers count') }}: <strong>{{ $subscribers_count }}</strong></p>
        <small class="text-muted">{{ get_phrase('The same number of days will be added to each subscriber\'s expiry date. Lifetime access will become from today + days.') }}</small>
    </div>

    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label" for="add_days">{{ get_phrase('Add days to expiry') }}</label>
        <input type="number" class="form-control ol-form-control" name="add_days" id="add_days" min="1" max="3650" value="30" required>
    </div>

    <div class="fpb7 mb-3">
        <button type="submit" class="btn ol-btn-primary w-100">{{ get_phrase('Extend period for all subscribers') }}</button>
    </div>
</form>
@else
    <p class="text-danger mb-0">{{ get_phrase('Course not found.') }}</p>
@endif

@include('admin.init')

@php
    $enrollment = App\Models\Enrollment::where('id', $id)->first();
@endphp
<form action="{{ route('admin.enroll.history.update_expiry_date', $id) }}" method="post">
    @csrf
    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label" for="student_name">{{ get_phrase('Student Name') }}</label>
        <input class="form-control ol-form-control" type="text" id="student_name" 
            value="{{ get_user_info($enrollment->user_id)->name }}" readonly>
    </div>

    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label" for="course_name">{{ get_phrase('Course') }}</label>
        <input class="form-control ol-form-control" type="text" id="course_name" 
            value="{{ get_course_info($enrollment->course_id)->title }}" readonly>
    </div>

    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label" for="current_expiry_date">{{ get_phrase('Current Expiry Date') }}</label>
        <input class="form-control ol-form-control" type="text" id="current_expiry_date" 
            value="{{ $enrollment->expiry_date ? date('d M Y h:i A', $enrollment->expiry_date) : get_phrase('Lifetime access') }}" readonly>
    </div>

    <div class="fpb7 mb-3">
        <label class="form-label ol-form-label" for="expiry_date">{{ get_phrase('New Expiry Date') }}</label>
        <input type="datetime-local" class="form-control ol-form-control" name="expiry_date" id="expiry_date" 
            value="{{ $enrollment->expiry_date ? date('Y-m-d\TH:i', $enrollment->expiry_date) : '' }}" />
        <small class="text-muted">{{ get_phrase('Leave empty for lifetime access') }}</small>
    </div>

    <div class="fpb7 mb-3">
        <button type="submit" class="btn ol-btn-primary w-100">{{ get_phrase('Update Expiry Date') }}</button>
    </div>
</form>

@include('admin.init')

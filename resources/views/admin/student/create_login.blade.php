<div class="row mb-3">
    <label for="email" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('البريد الإلكتروني') }}<span class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="email" name="email" class="form-control ol-form-control" id="email" placeholder="name@example.com" @isset($student->email) value="{{ old('email', isset($student) ? $student->email : '') }}"  @endisset required>
    </div>
</div>

@if (!isset($student->email))
    <div class="row mb-3">
        <label for="password" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('كلمة المرور') }}<span class="text-danger ms-1">*</span></label>
        <div class="col-sm-8">
            <input type="password" name="password" class="form-control ol-form-control" id="password" placeholder="{{ get_phrase('أدخل كلمة المرور') }}">
        </div>
    </div>
@endisset

<div class="row mb-3">
    <label for="email" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Email') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="email" name="email" class="form-control ol-form-control" id="email" @isset($admin->email) value="{{ $admin->email }}" @endisset required>
    </div>
</div>

{{-- @if(!isset($admin->email))
    <div class="row mb-3">
        <label for="password" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Password') }}<span
                class="text-danger ms-1">*</span></label>
        <div class="col-sm-8">
            <input type="password" name="password" class="form-control ol-form-control" id="password">
        </div>
    </div>
@endisset --}}
<div class="row mb-3">
    <label for="password" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('old_password') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="password" class="form-control" name="old_password" value="{{ old('old_password') }}"
            id="old_password">
    </div>
</div>
<div class="row mb-3">
    <label for="password" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('new_password') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-8"><input type="password" class="form-control" name="new_password"
            value="{{ old('new_password') }}" id="new_password">
    </div>
</div>

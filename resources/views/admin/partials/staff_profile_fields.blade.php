@php
    $profile = $profile ?? ($admin ?? ($instructor ?? null));
    $regions = get_saudi_regions();
@endphp

<div class="row mb-3">
    <label for="user-name" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('الاسم الكامل') }}<span class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="text" name="name" class="form-control ol-form-control @error('name') is-invalid @enderror" id="user-name"
            placeholder="{{ get_phrase('اكتب الاسم بالكامل') }}"
            value="{{ old('name', $profile->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="goverment" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('المنطقة') }}<span class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <select class="ol-select2 form-control @error('goverment') is-invalid @enderror" id="goverment" name="goverment" required>
            <option disabled value="" @selected(!old('goverment', $profile->goverment ?? ''))>{{ get_phrase('اختر المنطقة') }}</option>
            @foreach ($regions as $region)
                <option value="{{ $region }}" @selected(old('goverment', $profile->goverment ?? '') === $region)>{{ $region }}</option>
            @endforeach
        </select>
        @error('goverment')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="gender" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('الجنس') }}</label>
    <div class="col-sm-8">
        <select class="form-control ol-form-control" name="gender" id="gender">
            <option value="" disabled @selected(!old('gender', $profile->gender ?? ''))>{{ get_phrase('اختر الجنس') }}</option>
            <option value="1" @selected(old('gender', $profile->gender ?? '') == 1)>{{ get_phrase('ذكر') }}</option>
            <option value="2" @selected(old('gender', $profile->gender ?? '') == 2)>{{ get_phrase('أنثى') }}</option>
        </select>
    </div>
</div>

<div class="row mb-3">
    <label for="user-phone" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('رقم الجوال') }}</label>
    <div class="col-sm-8">
        <input type="text" name="phone" class="form-control ol-form-control @error('phone') is-invalid @enderror" id="user-phone"
            placeholder="05XXXXXXXX"
            value="{{ old('phone', $profile->phone ?? '') }}"
            maxlength="10" inputmode="numeric">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="national-id" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('رقم الهوية') }}<span class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="text" name="national_id" class="form-control ol-form-control @error('national_id') is-invalid @enderror" id="national-id"
            placeholder="{{ get_phrase('10 أرقام — يبدأ بـ 1 أو 2') }}"
            value="{{ old('national_id', $profile->national_id ?? '') }}"
            maxlength="10" inputmode="numeric" pattern="[12][0-9]{9}" required>
        <small class="text-muted">{{ get_phrase('رقم الإقامة أو الهوية الوطنية — 10 أرقام') }}</small>
        @error('national_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="user-address" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('العنوان') }}</label>
    <div class="col-sm-8">
        <input type="text" name="address" class="form-control ol-form-control @error('address') is-invalid @enderror" id="user-address"
            placeholder="{{ get_phrase('اكتب العنوان') }}"
            value="{{ old('address', $profile->address ?? '') }}">
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="photo" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('صورة المستخدم') }}</label>
    <div class="col-sm-8">
        <input type="file" name="photo" class="form-control ol-form-control @error('photo') is-invalid @enderror" id="photo" accept="image/*">
        @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


@php

$regions = [
    'الرياض',
    'مكة المكرمة',
    'المدينة المنورة',
    'القصيم',
    'المنطقة الشرقية',
    'عسير',
    'تبوك',
    'حائل',
    'الحدود الشمالية',
    'جازان',
    'نجران',
    'الباحة',
    'الجوف',
];


@endphp





<div class="row mb-3">
    <label for="user-name" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('الاسم الكامل') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="text" name="name" class="form-control ol-form-control" id="user-name"
            placeholder="{{ get_phrase('اكتب الاسم بالكامل') }}"
            @isset($student->name) value="{{ old('name', isset($student) ? $student->name : '') }}" @endisset
            required>
    </div>
</div>


{{-- <div class="row mb-3">
    <label for="short_description"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('نبذة تعريفية') }}</label>
    <div class="col-sm-8">
        <textarea name="about" rows="3" class="form-control ol-form-control" id="short_description"
                    placeholder="{{ get_phrase('اكتب نبذة تعريفية مختصرة') }}">
                @isset($student->about)
                {{ $student->about }}
                @endisset
        </textarea>
    </div>
</div> --}}


<div class="row mb-3">
    <label for="categories"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('المرحلة الدراسية') }}</label>
    <div class="col-sm-8">
        <select class="ol-select2 form-control ol-select2-multiple" id="category" name="category" required>
            <option value="" disabled>{{ get_phrase('اختر المرحلة الدراسية') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->title }}</option>
            @endforeach
        </select>

        @error('category')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>
</div>


<div class="row mb-3">
    <label for="goverments" class="form-label ol-form-label col-sm-2 col-form-label"
        data-toggle="select2">{{ get_phrase('المنطقة') }}</label>
    <div class="col-sm-8">
        <select class="ol-select2 form-control ol-select2-multiple" id="goverment" name="goverment" required>
      
            <option disabled
                value=""
                @selected(!old('goverment'))>
                {{ get_phrase('اختر المنطقة') }}
            </option>
            @foreach ($regions as $region)

                <option value="{{ $region }}"
                    @selected(old('goverment') === $region)>
                    {{ $region }}
                </option>

            @endforeach
        </select>

        @error('goverment')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>
</div>


<div class="row mb-3">
        <label for="gender"
            class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('الجنس') }}</label>
        <div class="col-sm-8">

            <select class="form-control" name="gender">
                <option disabled>{{ get_phrase('اختر الجنس') }}</option>
                <option value="1">ذكر</option>
                <option value="2">أنثى</option>
            </select>
    </div>
</div>
<div class="row mb-3">
    <label for="parent-phone"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('رقم جوال ولي الأمر') }}</label>
    <div class="col-sm-8">
        <input type="text" name="parent_phone" class="form-control ol-form-control" id="parent-phone"
            placeholder="05XXXXXXXX" maxlength="10" inputmode="numeric"
            @isset($student->parent_phone) value="{{ old('parent_phone', isset($student) ? $student->parent_phone : '') }}" @endisset>
    </div>
</div>


<div class="row mb-3">
    <label for="user-phone" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('رقم الجوال') }}</label>
    <div class="col-sm-8">
        <input type="text" name="phone" placeholder="05XXXXXXXX" class="form-control ol-form-control"
            id="user-phone" maxlength="10" inputmode="numeric"
            @isset($student->phone) value="{{ old('phone', isset($student) ? $student->phone : '') }}"  @endisset>
    </div>
</div>


<div class="row mb-3">
    <label for="national-id"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('رقم الهوية') }}<span class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="text" name="national_id" class="form-control ol-form-control @error('national_id') is-invalid @enderror" id="national-id"
            placeholder="{{ get_phrase('10 أرقام — يبدأ بـ 1 أو 2') }}"
            maxlength="10" inputmode="numeric" pattern="[12][0-9]{9}" required
            @isset($student->national_id) value="{{ old('national_id', isset($student) ? $student->national_id : '') }}" @endisset>
        <small class="text-muted">{{ get_phrase('رقم الهوية أو الهوية الوطنية — 10 أرقام') }}</small>
        @error('national_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>


<div class="row mb-3">
    <label for="user-address"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('العنوان') }}</label>
    <div class="col-sm-8">
        <input type="text" name="address" class="form-control ol-form-control" id="user-address"
            placeholder="{{ get_phrase('اكتب العنوان') }}"
            @isset($student->address) value="{{ old('address', isset($student) ? $student->address : '') }}"  @endisset>
    </div>
</div>
<div class="row mb-3">
    <label for="photo"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('صورة المستخدم') }}</label>
    <div class="col-sm-8">
        <input type="file" name="photo" class="form-control ol-form-control" id="photo">
    </div>
</div>

<div class="row mb-3">
    <label for="user-name" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Name') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="text" name="name" class="form-control ol-form-control" id="user-name"
            @isset($student->name) value="{{ old('name', $student->name) }}" @endisset required>
    </div>
</div>


{{-- <div class="row mb-3">
    <label for="short_description"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Biography') }}</label>
    <div class="col-sm-8">
        <textarea name="about" rows="3" class="form-control ol-form-control" id="short_description">
                    @isset($student->about)
                    {{ old('about', $student->about) }}
                    @endisset
        </textarea>
    </div>
</div> --}}
{{-- category edit start --}}
<div class="row mb-3">
    <label for="categories"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('categories') }}</label>
    <div class="col-sm-8">
        <select class="ol-select2 form-control ol-select2-multiple" id="category" name="category" required>
            <option value="" disabled>please select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ $category->id == $student->category ? 'selected' : '' }}>
                    {{ $category->title }}
                </option>
            @endforeach
        </select>

        @error('category')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>
</div>
{{-- end category --}}
{{-- start goverment --}}
<div class="row mb-3">
    <label for="goverment"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('goverment') }}</label>
    <div class="col-sm-8">
        <select class="ol-select2 form-control ol-select2-multiple" id="goverment" name="goverment" required>
            <option option value=""disabled>اختر المحافظة</option>
            @php
                $governorates = [
                    'القاهرة',
                    'الجيزة',
                    'الإسكندرية',
                    'الشرقية',
                    'الدقهلية',
                    'القليوبية',
                    'الغربية',
                    'المنوفية',
                    'البحيرة',
                    'كفر الشيخ',
                    'دمياط',
                    'بورسعيد',
                    'الإسماعيلية',
                    'السويس',
                    'مطروح',
                    'الفيوم',
                    'بني سويف',
                    'المنيا',
                    'أسيوط',
                    'سوهاج',
                    'قنا',
                    'الأقصر',
                    'أسوان',
                    'البحر الأحمر',
                    'الوادي الجديد',
                    'شمال سيناء',
                    'جنوب سيناء',
                ];
            @endphp

            @foreach ($governorates as $gov)
                <option value="{{ $gov }}"
                    {{ isset($student) && $student->goverment == $gov ? 'selected' : '' }}>
                    {{ $gov }}
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
{{-- end goverment --}}
<div class="row mb-3">
    <label for="gender" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('gender') }}</label>
    <div class="col-sm-8">
        <select class="form-control" name="gender">
            <option disabled>اختر النوع</option>
            <option value="1" @selected($student->gender == 1)>ذكر</option>
            <option value="2" @selected($student->gender == 2)>أنثى</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <label for="parent-phone"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Parent-Phone') }}</label>
    <div class="col-sm-8">
        <input type="text" name="parent_phone" class="form-control ol-form-control" id="parent-phone"
            @isset($student->parent_phone) value="{{ old('parent_phone', $student->parent_phone) }}" @endisset>
    </div>
</div>

<div class="row mb-3">
    <label for="user-phone" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Phone') }}</label>
    <div class="col-sm-8">
        <input type="text" name="phone" class="form-control ol-form-control" id="user-phone"
            @isset($student->phone) value="{{ old('phone', $student->phone) }}" @endisset>
    </div>
</div>

<div class="row mb-3">
    <label for="national-id"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('National-id') }}</label>
    <div class="col-sm-8">
        <input type="number" name="national_id" class="form-control ol-form-control" id="national-id"
            @isset($student->national_id) value="{{ old('national_id', $student->national_id) }}" @endisset>
    </div>
</div>

<div class="row mb-3">
    <label for="user-address"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Address') }}</label>
    <div class="col-sm-8">
        <input type="text" name="address" class="form-control ol-form-control" id="user-address"
            @isset($student->address) value="{{ old('address', $student->address) }}" @endisset>
    </div>
</div>
<div class="row mb-3">
    <label for="photo"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('User image') }}</label>
    <div class="col-sm-8">
        <input type="file" name="photo" class="form-control ol-form-control" id="photo">
    </div>
</div>




<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "اختر المحافظة", // نص افتراضي للبحث
            allowClear: true, // يسمح بإزالة الاختيار
            width: '100%' // جعل القائمة بعرض الحقل بالكامل
        });
    });
</script>

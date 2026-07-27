<div class="row mb-3">
    <label for="user-name" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Name') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="text" name="name" class="form-control ol-form-control" id="user-name"
            placeholder="please write your name"
            @isset($student->name) value="{{ old('name', isset($student) ? $student->name : '') }}" @endisset
            required>
    </div>
</div>


{{-- <div class="row mb-3">
    <label for="short_description"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Biography') }}</label>
    <div class="col-sm-8">
        <textarea name="about" rows="3" class="form-control ol-form-control" id="short_description"
                    placeholder="please write your short_description">
                @isset($student->about)
                {{ $student->about }}
                @endisset
        </textarea>
    </div>
</div> --}}


<div class="row mb-3">
    <label for="categories"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('categories') }}</label>
    <div class="col-sm-8">
        <select class="ol-select2 form-control ol-select2-multiple" id="category" name="category" required>
            <option value="" disabled>please select category</option>
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
        data-toggle="select2">{{ get_phrase('goverments') }}</label>
    <div class="col-sm-8">
        <select class="ol-select2 form-control ol-select2-multiple" id="goverment" name="goverment" required>
            <option value="" disabled>اختر المحافظة</option>
            <option value="القاهرة">القاهرة</option>
            <option value="الجيزة">الجيزة</option>
            <option value="الإسكندرية">الإسكندرية</option>
            <option value="الشرقية">الشرقية</option>
            <option value="الدقهلية">الدقهلية</option>
            <option value="القليوبية">القليوبية</option>
            <option value="الغربية">الغربية</option>
            <option value="المنوفية">المنوفية</option>
            <option value="البحيرة">البحيرة</option>
            <option value="كفر الشيخ">كفر الشيخ</option>
            <option value="دمياط">دمياط</option>
            <option value="بورسعيد">بورسعيد</option>
            <option value="الإسماعيلية">الإسماعيلية</option>
            <option value="السويس">السويس</option>
            <option value="مطروح">مطروح</option>
            <option value="الفيوم">الفيوم</option>
            <option value="بني سويف">بني سويف</option>
            <option value="المنيا">المنيا</option>
            <option value="أسيوط">أسيوط</option>
            <option value="سوهاج">سوهاج</option>
            <option value="قنا">قنا</option>
            <option value="الأقصر">الأقصر</option>
            <option value="أسوان">أسوان</option>
            <option value="البحر الأحمر">البحر الأحمر</option>
            <option value="الوادي الجديد">الوادي الجديد</option>
            <option value="شمال سيناء">شمال سيناء</option>
            <option value="جنوب سيناء">جنوب سيناء</option>
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
            class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('gender') }}</label>
        <div class="col-sm-8">

            <select class="form-control" name="gender">
                <option disabled>اختر النوع</option>
                <option value="1">ذكر</option>
                <option value="2">أنثى</option>
            </select>
    </div>
</div>
<div class="row mb-3">
    <label for="parent-phone"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('parent Phone') }}</label>
    <div class="col-sm-8">
        <input type="number" name="parent_phone" class="form-control ol-form-control" id="parent-phone"
            placeholder="please write your parent phone"
            @isset($student->parent_phone) value="{{ old('parent_phone', isset($student) ? $student->parent_phone : '') }}" @endisset>
    </div>
</div>


<div class="row mb-3">
    <label for="user-phone" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Phone') }}</label>
    <div class="col-sm-8">
        <input type="number" name="phone" placeholder="please write your phone" class="form-control ol-form-control"
            id="user-phone"
            @isset($student->phone) value="{{ old('phone', isset($student) ? $student->phone : '') }}"  @endisset>
    </div>
</div>


<div class="row mb-3">
    <label for="national-id"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('National-id') }}</label>
    <div class="col-sm-8">
        <input type="number" min="1" name="national_id" class="form-control ol-form-control" id="national-id"
            placeholder="please write your national id"
            @isset($student->national_id) value="{{ old('national_id', isset($student) ? $student->national_id : '') }}" @endisset>
    </div>
</div>


<div class="row mb-3">
    <label for="user-address"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Address') }}</label>
    <div class="col-sm-8">
        <input type="text" name="address" class="form-control ol-form-control" id="user-address"
            placeholder="please write your address"
            @isset($student->address) value="{{ old('phone', isset($student) ? $student->address : '') }}"  @endisset>
    </div>
</div>
<div class="row mb-3">
    <label for="photo"
        class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('User image') }}</label>
    <div class="col-sm-8">
        <input type="file" name="photo" class="form-control ol-form-control" id="photo">
    </div>
</div>

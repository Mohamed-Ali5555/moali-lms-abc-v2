@extends('theme::layouts.master')

@push('title', get_phrase('إنشاء حساب'))
@push('css') <link rel="stylesheet" href="{{ asset('modules/theme/css/register-modern.css') }}">
@endpush

@section('content')
@php
$siteName = trim((get_theme_settings('jop_title') ?: '') . ' ' . (get_theme_settings('name') ?: ''));
$logo = get_theme_settings('logo');


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

<div class="rg-page main_content">
    <div class="container">
        <div class="rg-shell">


        <aside class="rg-brand">
            <div>
                @if ($logo)
                    <img class="rg-brand__logo" src="{{ asset($logo) }}" alt="{{ $siteName }}">
                @endif

                <span class="rg-brand__badge">
                    <i class="fa-solid fa-user-plus"></i>
                    {{ get_phrase('إنشاء حساب جديد') }}
                </span>

                <h1 class="rg-brand__title">
                    {{ get_phrase('انضم للمنصة الآن') }}
                </h1>

                <p class="rg-brand__desc">
                    {{ get_phrase('أدخل بياناتك بشكل صحيح واستمتع بتجربة تعليمية مميزة داخل المنصة.') }}
                </p>

                <ul class="rg-brand__points">
                    <li>
                        <i class="fa-solid fa-graduation-cap"></i>
                        {{ get_phrase('الوصول إلى الدورات والكتب التعليمية') }}
                    </li>

                    <li>
                        <i class="fa-solid fa-shield-halved"></i>
                        {{ get_phrase('بياناتك محفوظة وآمنة') }}
                    </li>

                    <li>
                        <i class="fa-solid fa-bolt"></i>
                        {{ get_phrase('تفعيل سريع للحساب بعد التسجيل') }}
                    </li>
                </ul>
            </div>

            <div class="rg-brand__art">
                <img src="{{ asset('modules/theme/images/signup.svg') }}"
                    alt="{{ get_phrase('إنشاء حساب') }}">
            </div>
        </aside>

        <div class="rg-form-wrap">

            <p class="rg-form__eyebrow">
                {{ get_phrase('CREATE ACCOUNT') }}
            </p>

            <h2 class="rg-form__title">
                {{ get_phrase('أنشئ حسابك الآن') }}
            </h2>

            <p class="rg-form__sub">
                {{ get_phrase('جميع الحقول المطلوبة مميزة بعلامة *، والتأكد من صحة بياناتك يساعد على تفعيل حسابك بسهولة.') }}
            </p>

            <form action="{{ route('theme.register') }}"
                id="login-form"
                enctype="multipart/form-data"
                method="post"
                novalidate>

                @csrf

                <div class="rg-grid">

                    {{-- الاسم الكامل --}}
                    <div class="rg-field rg-span-2">
                        <label for="name">
                            {{ get_phrase('الاسم الكامل') }}
                            <span class="req">*</span>
                        </label>

                        <div class="rg-input">
                            <i class="fa-solid fa-user"></i>

                            <input type="text"
                                required
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="{{ get_phrase('اكتب اسمك باللغة العربية') }}"
                                onkeypress="validateArabicInput(event)"
                                class="@error('name') is-invalid @enderror"
                                autocomplete="name">
                        </div>

                        @error('name')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- البريد الإلكتروني --}}
                    <div class="rg-field rg-span-2">

                        @php
                            $emailRequired = is_email_required();
                        @endphp

                        <label for="email">

                            {{ get_phrase('البريد الإلكتروني') }}

                            @if ($emailRequired)
                                <span class="req">*</span>
                            @else
                                <span class="text-muted"
                                    style="font-weight:600;font-size:.85em;">
                                    ({{ get_phrase('اختياري') }})
                                </span>
                            @endif

                        </label>

                        <div class="rg-input">
                            <i class="fa-solid fa-envelope"></i>

                            <input type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="name@example.com"
                                class="@error('email') is-invalid @enderror"
                                autocomplete="email"
                                @if ($emailRequired) required @endif>
                        </div>

                        @error('email')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- رقم الهوية الوطنية --}}
                    <div class="rg-field rg-span-2">

                        @php
                            $nationalIdRequired = is_national_id_required();
                        @endphp

                        <label for="national_id">

                            {{ get_phrase('رقم الهوية الوطنية') }}

                            @if ($nationalIdRequired)
                                <span class="req">*</span>
                            @else
                                <span class="text-muted"
                                    style="font-weight:600;font-size:.85em;">
                                    ({{ get_phrase('اختياري') }})
                                </span>
                            @endif

                        </label>

                        <div class="rg-input">
                            <i class="fa-solid fa-id-card"></i>

                            <input type="text"
                                inputmode="numeric"
                                id="national_id"
                                name="national_id"
                                value="{{ old('national_id') }}"
                                maxlength="10"
                                minlength="10"
                                placeholder="رقم الهوية الوطنية"
                                onkeypress="validateNumberInput(event)"
                                class="@error('national_id') is-invalid @enderror"
                                @if ($nationalIdRequired) required @endif>
                        </div>

                        @error('national_id')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror

                        <span class="rg-hint custom_input_message">
                            {{ get_phrase('أدخل رقم الهوية الوطنية المكوّن من 10 أرقام') }}
                        </span>

                    </div>


                    {{-- رقم الجوال --}}
                    <div class="rg-field">

                        <label for="phone">
                            {{ get_phrase('رقم الجوال') }}
                            <span class="req">*</span>
                        </label>

                        <div class="rg-input">
                            <i class="fa-solid fa-mobile-screen-button"></i>

                            <input type="text"
                                inputmode="numeric"
                                required
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                maxlength="10"
                                minlength="10"
                                placeholder="05XXXXXXXX"
                                onkeypress="validateNumberInput(event)"
                                class="@error('phone') is-invalid @enderror"
                                autocomplete="tel">
                        </div>

                        @error('phone')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror

                    </div>


                    {{-- رقم جوال ولي الأمر --}}
                    <div class="rg-field">

                        <label for="parent_phone">
                            {{ get_phrase('رقم جوال ولي الأمر') }}
                            <span class="req">*</span>
                        </label>

                        <div class="rg-input">
                            <i class="fa-solid fa-phone-volume"></i>

                            <input type="text"
                                inputmode="numeric"
                                required
                                id="parent_phone"
                                name="parent_phone"
                                value="{{ old('parent_phone') }}"
                                maxlength="10"
                                minlength="10"
                                placeholder="05XXXXXXXX"
                                onkeypress="validateNumberInput(event)"
                                class="@error('parent_phone') is-invalid @enderror">
                        </div>

                        @error('parent_phone')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror

                    </div>


                    {{-- الجنس --}}
                    <div class="rg-field">

                        <label for="gender">
                            {{ get_phrase('الجنس') }}
                            <span class="req">*</span>
                        </label>

                        <div class="rg-input custom-select">
                            <i class="fa-solid fa-venus-mars"></i>

                            <select class="form-control ot-input @error('gender') is-invalid @enderror"
                                id="gender"
                                name="gender"
                                required>

                                <option disabled
                                    value=""
                                    @selected(!old('gender'))>
                                    اختر الجنس
                                </option>

                                <option value="1"
                                    @selected(old('gender') == '1')>
                                    ذكر
                                </option>

                                <option value="2"
                                    @selected(old('gender') == '2')>
                                    أنثى
                                </option>

                            </select>
                        </div>

                        @error('gender')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror

                    </div>


                    {{-- المنطقة --}}
                    <div class="rg-field">

                        <label for="goverment">
                            {{ get_phrase('المنطقة') }}
                            <span class="req">*</span>
                        </label>

                        <div class="rg-input custom-select">
                            <i class="fa-solid fa-location-dot"></i>

                            <select name="goverment"
                                id="goverment"
                                class="form-control">

                                <option disabled
                                    value=""
                                    @selected(!old('goverment'))>
                                    اختر المنطقة
                                </option>

                                @foreach ($regions as $region)

                                    <option value="{{ $region }}"
                                        @selected(old('goverment') === $region)>
                                        {{ $region }}
                                    </option>

                                @endforeach

                            </select>
                        </div>

                        @error('goverment')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror

                    </div>


                    {{-- المرحلة الدراسية --}}
                    <div class="rg-field rg-span-2">

                        <label for="category">

                            {{ get_phrase('المرحلة الدراسية') }}

                            <span class="req">*</span>

                        </label>

                        <div class="rg-input custom-select">

                            <i class="fa-solid fa-school"></i>

                            <select class="form-control ot-input @error('category') is-invalid @enderror"
                                id="category"
                                name="category">

                                <option disabled
                                    value=""
                                    @selected(!old('category'))>
                                    اختر المرحلة الدراسية
                                </option>

                                @foreach ($categories as $category)

                                    <option value="{{ $category->id }}"
                                        @selected(old('category') == $category->id)>

                                        {{ $category->title }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        @error('category')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror

                    </div>


                    {{-- صورة الهوية --}}
                    <div class="rg-field rg-span-2">

                        @php
                            $nationalImageRequired = is_national_image_required();
                        @endphp

                        <label>

                            {{ get_phrase('صورة الهوية الوطنية / شهادة الميلاد') }}

                            @if ($nationalImageRequired)

                                <span class="req">*</span>

                            @else

                                <span class="text-muted"
                                    style="font-weight:600;font-size:.85em;">
                                    ({{ get_phrase('اختياري') }})
                                </span>

                            @endif

                        </label>

                        <div class="rg-upload national_container">

                            <span class="rg-upload__icon">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </span>

                            <div class="rg-upload__text">

                                <strong>
                                    {{ get_phrase('رفع الملف') }}
                                </strong>

                                <p>
                                    {{ get_phrase('ارفع صورة الهوية الوطنية أو شهادة الميلاد') }}
                                </p>

                            </div>

                            <img alt="preview">

                            <input type="file"
                                id="national_image"
                                name="national_image"
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                @if ($nationalImageRequired) required @endif
                                onchange="uploadNationalImage(this)">

                        </div>

                        <span class="rg-error text-danger custom-error-text"
                            id="error_national_image">
                        </span>

                        @error('national_image')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror

                        <span class="rg-hint">
                            jpeg, png, jpg, webp — بحد أقصى 50 ميجابايت
                        </span>

                    </div>


                    {{-- كلمة المرور --}}
                    <div class="rg-field">

                        <label for="password">

                            {{ get_phrase('كلمة المرور') }}

                            <span class="req">*</span>

                        </label>

                        <div class="rg-input">

                            <i class="fa-solid fa-lock"></i>

                            <input type="password"
                                required
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="new-password">

                        </div>

                        <span class="rg-error text-danger custom-error-text"
                            id="error_password">
                        </span>

                        @error('password')
                            <small class="rg-error text-danger">{{ $message }}</small>
                        @enderror

                    </div>


                    {{-- تأكيد كلمة المرور --}}
                    <div class="rg-field">

                        <label for="password_confirmation">

                            {{ get_phrase('تأكيد كلمة المرور') }}

                            <span class="req">*</span>

                        </label>

                        <div class="rg-input">

                            <i class="fa-solid fa-lock"></i>

                            <input type="password"
                                required
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="••••••••"
                                autocomplete="new-password">

                        </div>

                        <span class="rg-error text-danger custom-error-text"
                            id="error_password_confirmation">
                        </span>

                    </div>


                    {{-- الشروط والأحكام --}}
                    <div class="rg-field rg-span-2">

                        <div class="remember-me terms-condition mb-0">

                            <label class="rg-check">

                                <input class="ot-checkbox"
                                    type="checkbox"
                                    value="1"
                                    name="agree"
                                    {{ old('agree') ? 'checked' : '' }}>

                                <span>

                                    أوافق على

                                    <a href="{{ route('theme.terms.condition') }}"
                                        target="_blank">

                                        الشروط والأحكام

                                    </a>

                                </span>

                            </label>

                        </div>

                        <span class="rg-error text-danger custom-error-text"
                            id="error_agree">
                        </span>

                    </div>


                    {{-- إنشاء الحساب --}}
                    <div class="rg-span-2">

                        @if (get_frontend_settings('recaptcha_status'))

                            <button class="rg-submit eBtn gradient g-recaptcha"
                                data-sitekey="{{ get_frontend_settings('recaptcha_sitekey') }}"
                                data-callback="onLoginSubmit"
                                data-action="submit"
                                type="button">

                                <i class="fa-solid fa-user-check"></i>

                                {{ get_phrase('إنشاء الحساب') }}

                            </button>

                        @else

                            <button type="submit"
                                class="rg-submit eBtn gradient">

                                <i class="fa-solid fa-user-check"></i>

                                {{ get_phrase('إنشاء الحساب') }}

                            </button>

                        @endif

                    </div>

                </div>

            </form>


            {{-- تسجيل الدخول --}}
            <div class="rg-footer login-link">

                <span>
                    {{ get_phrase('عندك حساب بالفعل؟') }}
                </span>

                <a href="{{ route('theme.show_login') }}">
                    {{ get_phrase('سجّل دخولك الآن') }}
                </a>

            </div>

        </div>

    </div>
</div>
```

</div>

@endsection

@push('js')

<script>
    "use strict";

    let hasShownNumberToast = false;
    let hasShownArabicToast = false;


    /*
    |--------------------------------------------------------------------------
    | التحقق من الأرقام
    |--------------------------------------------------------------------------
    */

    function validateNumberInput(event) {

        try {

            const charCode = event.which ? event.which : event.keyCode;

            if (
                charCode === 8 ||
                charCode === 9 ||
                charCode === 27 ||
                charCode === 13 ||
                (charCode === 46 && event.shiftKey === false) ||
                (charCode >= 35 && charCode <= 40)
            ) {
                return true;
            }

            if (
                (charCode === 65 ||
                    charCode === 67 ||
                    charCode === 86 ||
                    charCode === 88) &&
                (event.ctrlKey === true || event.metaKey === true)
            ) {
                return true;
            }

            if (charCode < 48 || charCode > 57) {

                event.preventDefault();

                if (!hasShownNumberToast && typeof Swal !== 'undefined') {

                    hasShownNumberToast = true;

                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,

                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    Toast.fire({
                        icon: "error",
                        title: "مسموح بإدخال الأرقام فقط"
                    });

                    setTimeout(() => {
                        hasShownNumberToast = false;
                    }, 1500);
                }

                return false;
            }

            return true;

        } catch (error) {

            console.error('Error in validateNumberInput:', error);

            return true;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | التحقق من اللغة العربية
    |--------------------------------------------------------------------------
    */

    function validateArabicInput(event) {

        try {

            const charCode = event.which ? event.which : event.keyCode;

            if (
                charCode === 8 ||
                charCode === 9 ||
                charCode === 27 ||
                charCode === 13 ||
                (charCode === 46 && event.shiftKey === false) ||
                (charCode >= 35 && charCode <= 40)
            ) {
                return true;
            }

            if (
                (charCode === 65 ||
                    charCode === 67 ||
                    charCode === 86 ||
                    charCode === 88) &&
                (event.ctrlKey === true || event.metaKey === true)
            ) {
                return true;
            }

            const char = String.fromCharCode(charCode);

            const arabicRegex = /^[\u0600-\u06FF\s]+$/;

            if (!arabicRegex.test(char)) {

                event.preventDefault();

                if (!hasShownArabicToast && typeof Swal !== 'undefined') {

                    hasShownArabicToast = true;

                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,

                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });

                    Toast.fire({
                        icon: "error",
                        title: "مسموح بالكتابة باللغة العربية فقط"
                    });

                    setTimeout(() => {
                        hasShownArabicToast = false;
                    }, 1500);
                }

                return false;
            }

            return true;

        } catch (error) {

            console.error('Error in validateArabicInput:', error);

            return true;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | رفع صورة الهوية
    |--------------------------------------------------------------------------
    */

    function uploadNationalImage(input) {

        try {

            if (!input || !input.files) {
                return;
            }

            const inputParent = input.closest("div");

            if (!inputParent) {
                return;
            }

            const label =
                inputParent.querySelector('.rg-upload__text p') ||
                inputParent.querySelector('label p');

            const preview = inputParent.querySelector("img");

            const file = input.files[0];

            if (file) {

                const reader = new FileReader();

                reader.onloadend = function () {

                    if (preview) {

                        preview.src = reader.result;

                        preview.style.display = "block";
                    }
                };

                reader.readAsDataURL(file);

                inputParent.classList.add("uploaded");

                if (label) {

                    label.textContent = file.name;
                }

            } else {

                if (preview) {

                    preview.style.display = "none";
                }

                if (label) {

                    label.textContent =
                        "ارفع صورة الهوية الوطنية أو شهادة الميلاد";
                }

                inputParent.classList.remove("uploaded");
            }

        } catch (error) {

            console.error('Error in uploadNationalImage:', error);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | التحقق من النموذج
    |--------------------------------------------------------------------------
    */

    function validateForm() {

        try {

            const errorElements =
                document.querySelectorAll('.custom-error-text');

            errorElements.forEach(el => el.textContent = '');

            let errors = [];

            let firstErrorField = null;


            // الاسم

            const name = document.getElementById('name');

            if (!name || !name.value || !name.value.trim()) {

                errors.push('يرجى إدخال الاسم الكامل');

                if (!firstErrorField) {
                    firstErrorField = name;
                }
            }


            // البريد الإلكتروني

            const email = document.getElementById('email');

            const emailRequired =
                {{ is_email_required() ? 'true' : 'false' }};

            if (
                emailRequired &&
                (!email || !email.value || !email.value.trim())
            ) {

                errors.push('يرجى إدخال البريد الإلكتروني');

                if (!firstErrorField) {
                    firstErrorField = email;
                }

            } else if (
                email &&
                email.value &&
                email.value.trim() &&
                !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)
            ) {

                errors.push('يرجى إدخال بريد إلكتروني صحيح');

                if (!firstErrorField) {
                    firstErrorField = email;
                }
            }


            // الهوية الوطنية

            const nationalId =
                document.getElementById('national_id');

            const nationalIdRequired =
                {{ is_national_id_required() ? 'true' : 'false' }};

            if (
                nationalIdRequired &&
                (!nationalId ||
                    !nationalId.value ||
                    !nationalId.value.trim())
            ) {

                errors.push('يرجى إدخال رقم الهوية الوطنية');

                if (!firstErrorField) {
                    firstErrorField = nationalId;
                }

            } else if (
                nationalId &&
                nationalId.value &&
                nationalId.value.trim() &&
                nationalId.value.length !== 10
            ) {

                errors.push(
                    'رقم الهوية الوطنية يجب أن يتكون من 10 أرقام'
                );

                if (!firstErrorField) {
                    firstErrorField = nationalId;
                }
            }


            // رقم الجوال

            const phone =
                document.getElementById('phone');

            if (
                !phone ||
                !phone.value ||
                !phone.value.trim()
            ) {

                errors.push('يرجى إدخال رقم الجوال');

                if (!firstErrorField) {
                    firstErrorField = phone;
                }

            } else if (
                !/^05\d{8}$/.test(phone.value)
            ) {

                errors.push(
                    'رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام'
                );

                if (!firstErrorField) {
                    firstErrorField = phone;
                }
            }


            // جوال ولي الأمر

            const parentPhone =
                document.getElementById('parent_phone');

            if (
                !parentPhone ||
                !parentPhone.value ||
                !parentPhone.value.trim()
            ) {

                errors.push(
                    'يرجى إدخال رقم جوال ولي الأمر'
                );

                if (!firstErrorField) {
                    firstErrorField = parentPhone;
                }

            } else if (
                !/^05\d{8}$/.test(parentPhone.value)
            ) {

                errors.push(
                    'رقم جوال ولي الأمر يجب أن يبدأ بـ 05 ويتكون من 10 أرقام'
                );

                if (!firstErrorField) {
                    firstErrorField = parentPhone;
                }

            } else if (
                phone &&
                phone.value &&
                phone.value === parentPhone.value
            ) {

                errors.push(
                    'يجب أن يكون رقم الجوال مختلفًا عن رقم ولي الأمر'
                );

                if (!firstErrorField) {
                    firstErrorField = parentPhone;
                }
            }


            // الجنس

            const gender =
                document.getElementById('gender');

            if (!gender || !gender.value) {

                errors.push('يرجى اختيار الجنس');

                if (!firstErrorField) {
                    firstErrorField = gender;
                }
            }


            // المنطقة

            const goverment =
                document.querySelector(
                    'select[name="goverment"]'
                );

            if (!goverment || !goverment.value) {

                errors.push('يرجى اختيار المنطقة');

                if (!firstErrorField) {
                    firstErrorField = goverment;
                }
            }


            // المرحلة الدراسية

            const category =
                document.getElementById('category');

            if (!category || !category.value) {

                errors.push(
                    'يرجى اختيار المرحلة الدراسية'
                );

                if (!firstErrorField) {
                    firstErrorField = category;
                }
            }


            // صورة الهوية

            const nationalImage =
                document.getElementById('national_image');

            const nationalImageRequired =
                {{ is_national_image_required() ? 'true' : 'false' }};

            if (
                nationalImageRequired &&
                (!nationalImage ||
                    !nationalImage.files ||
                    nationalImage.files.length === 0)
            ) {

                errors.push(
                    'يرجى رفع صورة الهوية الوطنية'
                );

                if (!firstErrorField) {
                    firstErrorField = nationalImage;
                }

            } else if (
                nationalImage &&
                nationalImage.files &&
                nationalImage.files.length > 0
            ) {

                const file = nationalImage.files[0];

                if (file) {

                    const allowedTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                        'image/webp'
                    ];

                    const maxSize =
                        50 * 1024 * 1024;

                    if (!allowedTypes.includes(file.type)) {

                        errors.push(
                            'يجب أن تكون الصورة بصيغة jpeg أو png أو jpg أو webp'
                        );

                        if (!firstErrorField) {
                            firstErrorField = nationalImage;
                        }

                    } else if (file.size > maxSize) {

                        errors.push(
                            'أقصى حجم مسموح للصورة هو 50 ميجابايت'
                        );

                        if (!firstErrorField) {
                            firstErrorField = nationalImage;
                        }
                    }
                }
            }


            // كلمة المرور

            const password =
                document.getElementById('password');

            if (!password || !password.value) {

                errors.push(
                    'يرجى إدخال كلمة المرور'
                );

                if (!firstErrorField) {
                    firstErrorField = password;
                }

            } else if (password.value.length < 8) {

                errors.push(
                    'كلمة المرور يجب أن تكون 8 أحرف على الأقل'
                );

                if (!firstErrorField) {
                    firstErrorField = password;
                }
            }


            // تأكيد كلمة المرور

            const passwordConfirmation =
                document.getElementById(
                    'password_confirmation'
                );

            if (
                !passwordConfirmation ||
                !passwordConfirmation.value
            ) {

                errors.push(
                    'يرجى تأكيد كلمة المرور'
                );

                if (!firstErrorField) {
                    firstErrorField = passwordConfirmation;
                }

            } else if (
                password &&
                password.value &&
                password.value !== passwordConfirmation.value
            ) {

                errors.push(
                    'كلمة المرور وتأكيدها غير متطابقين'
                );

                if (!firstErrorField) {
                    firstErrorField = passwordConfirmation;
                }
            }


            // الشروط والأحكام

            const agree =
                document.querySelector(
                    'input[name="agree"]'
                );

            if (!agree || !agree.checked) {

                errors.push(
                    'يرجى الموافقة على الشروط والأحكام'
                );

                if (!firstErrorField) {
                    firstErrorField = agree;
                }
            }


            return {
                errors,
                firstErrorField
            };

        } catch (error) {

            console.error(
                'Error in validateForm:',
                error
            );

            return {
                errors: [
                    'حدث خطأ أثناء التحقق من البيانات، يرجى المحاولة مرة أخرى.'
                ],
                firstErrorField: null
            };
        }
    }


    /*
    |--------------------------------------------------------------------------
    | عرض رسائل التحقق
    |--------------------------------------------------------------------------
    */

    function showValidationErrors(
        errors,
        firstErrorField
    ) {

        try {

            if (!errors || errors.length === 0) {
                return true;
            }

            if (typeof Swal === 'undefined') {

                alert(
                    'يرجى إكمال البيانات المطلوبة:\n' +
                    errors.join('\n')
                );

                if (
                    firstErrorField &&
                    firstErrorField.focus
                ) {

                    setTimeout(() => {
                        firstErrorField.focus();
                    }, 100);
                }

                return false;
            }


            let errorMessage =
                errors.length === 1
                    ? errors[0]
                    :
                    '<ul style="text-align:right;direction:rtl;list-style-type:none;padding-right:0;">' +
                    errors
                        .map(
                            err =>
                                '<li style="margin-bottom:8px;">' +
                                err +
                                '</li>'
                        )
                        .join('') +
                    '</ul>';


            Swal.fire({

                icon: 'error',

                title:
                    'يرجى إكمال البيانات المطلوبة',

                html: errorMessage,

                confirmButtonText:
                    'حسنًا',

                confirmButtonColor:
                    '#3085d6',

                didClose: () => {

                    if (firstErrorField) {

                        setTimeout(() => {

                            try {

                                if (
                                    firstErrorField.focus
                                ) {
                                    firstErrorField.focus();
                                }

                                if (
                                    firstErrorField.scrollIntoView
                                ) {

                                    firstErrorField.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });

                                }

                            } catch (e) {

                                console.error(
                                    'Error focusing field:',
                                    e
                                );
                            }

                        }, 100);
                    }
                }

            });

            return false;

        } catch (error) {

            console.error(
                'Error in showValidationErrors:',
                error
            );

            alert(
                'حدث خطأ أثناء التحقق من البيانات، يرجى المحاولة مرة أخرى.'
            );

            return false;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | إرسال النموذج مع reCAPTCHA
    |--------------------------------------------------------------------------
    */

    function onLoginSubmit(token) {

        try {

            const validationResult =
                validateForm();

            if (
                !validationResult ||
                !validationResult.errors ||
                validationResult.errors.length === 0
            ) {

                const form =
                    document.getElementById(
                        "login-form"
                    );

                if (form) {
                    form.submit();
                }

            } else {

                showValidationErrors(
                    validationResult.errors,
                    validationResult.firstErrorField
                );

                if (
                    typeof grecaptcha !== 'undefined' &&
                    grecaptcha.reset
                ) {

                    grecaptcha.reset();
                }
            }

        } catch (error) {

            console.error(
                'Error in onLoginSubmit:',
                error
            );

            alert(
                'حدث خطأ أثناء إرسال النموذج، يرجى المحاولة مرة أخرى.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | التحقق عند الإرسال
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            try {

                const form =
                    document.getElementById(
                        'login-form'
                    );

                if (form) {

                    form.addEventListener(
                        'submit',
                        function (e) {

                            e.preventDefault();

                            try {

                                const validationResult =
                                    validateForm();

                                if (
                                    !validationResult ||
                                    !validationResult.errors ||
                                    validationResult.errors.length === 0
                                ) {

                                    form.submit();

                                } else {

                                    showValidationErrors(
                                        validationResult.errors,
                                        validationResult.firstErrorField
                                    );
                                }

                            } catch (error) {

                                console.error(
                                    'Error in form submit handler:',
                                    error
                                );

                                alert(
                                    'حدث خطأ أثناء التحقق من البيانات، يرجى المحاولة مرة أخرى.'
                                );
                            }
                        }
                    );
                }

            } catch (error) {

                console.error(
                    'Error in DOMContentLoaded:',
                    error
                );
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | أخطاء السيرفر
    |--------------------------------------------------------------------------
    */

    @if ($errors->any())

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                try {

                    let errorMessages = [];

                    @foreach ($errors->all() as $error)

                        errorMessages.push(
                            @json($error)
                        );

                    @endforeach


                    if (errorMessages.length > 0) {

                        let errorMessage =
                            errorMessages.length === 1
                                ? errorMessages[0]
                                :
                                '<ul style="text-align:right;direction:rtl;list-style-type:none;padding-right:0;">' +
                                errorMessages
                                    .map(
                                        err =>
                                            '<li style="margin-bottom:8px;">' +
                                            err +
                                            '</li>'
                                    )
                                    .join('') +
                                '</ul>';


                        if (typeof Swal !== 'undefined') {

                            Swal.fire({

                                icon: 'error',

                                title:
                                    'يوجد خطأ في البيانات',

                                html:
                                    errorMessage,

                                confirmButtonText:
                                    'حسنًا',

                                confirmButtonColor:
                                    '#3085d6'
                            });

                        } else {

                            alert(
                                'يوجد خطأ في البيانات:\n' +
                                errorMessages.join('\n')
                            );
                        }
                    }

                } catch (error) {

                    console.error(
                        'Error showing server-side validation errors:',
                        error
                    );
                }
            }
        );

    @endif

</script>

@endpush

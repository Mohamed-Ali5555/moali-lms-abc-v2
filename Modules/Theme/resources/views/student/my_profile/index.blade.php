@extends('theme::layouts.master')

@push('title', get_phrase('حسابي'))
@push('meta')@endpush
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/profile-modern.css') }}">
@endpush

@section('content')
    @php
        $categoryTitle = optional($categories->firstWhere('id', $user_details->category))->title;
        $genderLabel = $user_details->gender == 1 ? get_phrase('ذكر') : ($user_details->gender == 2 ? get_phrase('أنثى') : null);
        $governorates = get_saudi_regions();
    @endphp

    <section class="course-content main_content pf-page" dir="rtl">
        <div class="profile-banner-area"></div>
        <div class="container profile-banner-area-container">
            <div class="row">
                @include('theme::student.left_sidebar')

                <div class="col-lg-9">
                    <div class="pf-header">
                        <div class="pf-header__intro">
                            <div class="pf-header__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="pf-header__title">{{ get_phrase('حسابي') }}</h1>
                                <p class="pf-header__sub">
                                    {{ get_phrase('حدّث بياناتك الشخصية والأكاديمية، وغيّر كلمة المرور بسهولة.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="my-panel message-panel edit_profile pf-panel">
                        <form action="{{ route('theme.update.profile', $user_details->id) }}" method="post"
                            enctype="multipart/form-data" class="pf-form" id="profile-form">
                            @csrf

                            <div class="pf-hero">
                                <div class="pf-hero__avatar">
                                    <img id="profile-avatar-preview"
                                        src="{{ get_image($user_details->photo) }}"
                                        alt="{{ $user_details->name }}">
                                    <label class="pf-hero__avatar-edit" for="photo" title="{{ get_phrase('تغيير الصورة') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>
                                        <span class="visually-hidden">{{ get_phrase('تغيير الصورة') }}</span>
                                    </label>
                                </div>
                                <div>
                                    <h2 class="pf-hero__name">{{ $user_details->name }}</h2>
                                    <p class="pf-hero__email">{{ $user_details->email }}</p>
                                    <div class="pf-hero__chips">
                                        @if ($categoryTitle)
                                            <span class="pf-chip">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                                                    <path d="M6 12v5c3 3 9 3 12 0v-5" />
                                                </svg>
                                                {{ $categoryTitle }}
                                            </span>
                                        @endif
                                        @if ($user_details->goverment)
                                            <span class="pf-chip">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                    <circle cx="12" cy="10" r="3" />
                                                </svg>
                                                {{ $user_details->goverment }}
                                            </span>
                                        @endif
                                        @if ($genderLabel)
                                            <span class="pf-chip">{{ $genderLabel }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- البيانات الشخصية --}}
                            <section class="pf-section">
                                <div class="pf-section__head">
                                    <div class="pf-section__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="pf-section__title">{{ get_phrase('البيانات الشخصية') }}</h3>
                                        <p class="pf-section__desc">{{ get_phrase('المعلومات الأساسية للتواصل والحساب') }}</p>
                                    </div>
                                </div>
                                <div class="pf-section__body">
                                    <div class="pf-grid">
                                        <div class="pf-field">
                                            <label for="name">{{ get_phrase('الإسم') }}</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                name="name" value="{{ old('name', $user_details->name) }}" id="name" required>
                                            @error('name')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="pf-field">
                                            @php $emailRequired = is_email_required(); @endphp
                                            <label for="email">
                                                {{ get_phrase('البريد الإلكترونى') }}
                                                @if ($emailRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email', $user_details->email) }}" id="email"
                                                @if ($emailRequired) required @endif>
                                            @error('email')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="pf-field">
                                            <label for="phone">{{ get_phrase('رقم الجوال') }}</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                name="phone" value="{{ old('phone', $user_details->phone) }}" id="phone"
                                                maxlength="10" inputmode="numeric" placeholder="05XXXXXXXX" required>
                                            @error('phone')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="pf-field">
                                            <label for="parent_phone">{{ get_phrase('رقم جوال ولي الأمر') }}</label>
                                            <input type="text" class="form-control @error('parent_phone') is-invalid @enderror"
                                                name="parent_phone"
                                                value="{{ old('parent_phone', $user_details->parent_phone) }}"
                                                id="parent_phone" maxlength="10" inputmode="numeric"
                                                placeholder="05XXXXXXXX" required>
                                            @error('parent_phone')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="pf-field">
                                            @php $nationalIdRequired = is_national_id_required(); @endphp
                                            <label for="national_id">
                                                {{ get_phrase('رقم الإقامة') }}
                                                @if ($nationalIdRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text"
                                                class="form-control @error('national_id') is-invalid @enderror"
                                                name="national_id"
                                                value="{{ old('national_id', $user_details->national_id) }}"
                                                id="national_id"
                                                maxlength="10" inputmode="numeric" pattern="[12][0-9]{9}"
                                                placeholder="{{ get_phrase('10 أرقام — يبدأ بـ 1 أو 2') }}"
                                                @if ($nationalIdRequired) required @endif>
                                            @error('national_id')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="pf-field">
                                            <label for="gender">{{ get_phrase('النوع') }}</label>
                                            <select class="form-control @error('gender') is-invalid @enderror"
                                                name="gender" id="gender" required>
                                                <option value="" disabled {{ old('gender', $user_details->gender) ? '' : 'selected' }}>
                                                    {{ get_phrase('اختر النوع') }}
                                                </option>
                                                <option value="1" @selected(old('gender', $user_details->gender) == 1)>ذكر</option>
                                                <option value="2" @selected(old('gender', $user_details->gender) == 2)>أنثى</option>
                                            </select>
                                            @error('gender')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="pf-field pf-grid--full">
                                            <label for="address">{{ get_phrase('العنوان') }}</label>
                                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                                name="address" value="{{ old('address', $user_details->address) }}"
                                                id="address"
                                                placeholder="{{ get_phrase('العنوان بالتفصيل') }}">
                                            @error('address')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- البيانات الأكاديمية --}}
                            <section class="pf-section">
                                <div class="pf-section__head">
                                    <div class="pf-section__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                                            <path d="M6 12v5c3 3 9 3 12 0v-5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="pf-section__title">{{ get_phrase('البيانات الأكاديمية') }}</h3>
                                        <p class="pf-section__desc">{{ get_phrase('الصف والمنطقة المرتبطة بحسابك') }}</p>
                                    </div>
                                </div>
                                <div class="pf-section__body">
                                    <div class="pf-grid">
                                        <div class="pf-field">
                                            <label for="goverment">{{ get_phrase('المنطقة') }}</label>
                                            <select class="form-control @error('goverment') is-invalid @enderror"
                                                id="goverment" name="goverment" required>
                                                <option value="" disabled {{ old('goverment', $user_details->goverment) ? '' : 'selected' }}>
                                                    {{ get_phrase('اختر المنطقة') }}
                                                </option>
                                                @foreach ($governorates as $gov)
                                                    <option value="{{ $gov }}"
                                                        @selected(old('goverment', $user_details->goverment) == $gov)>
                                                        {{ $gov }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('goverment')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="pf-field">
                                            <label for="category">{{ get_phrase('الصف الدراسى') }}</label>
                                            <select class="form-control @error('category') is-invalid @enderror"
                                                id="category" name="category" required>
                                                <option value="" disabled {{ old('category', $user_details->category) ? '' : 'selected' }}>
                                                    {{ get_phrase('اختر الصف') }}
                                                </option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        @selected(old('category', $user_details->category) == $category->id)>
                                                        {{ $category->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {{-- المستندات والصور --}}
                            @php
                                $nationalImageRequired = is_national_image_required();
                                $needsNationalImage = $nationalImageRequired && empty($user_details->national_image);
                            @endphp
                            <section class="pf-section">
                                <div class="pf-section__head">
                                    <div class="pf-section__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="12" y1="18" x2="12" y2="12" />
                                            <line x1="9" y1="15" x2="15" y2="15" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="pf-section__title">{{ get_phrase('المستندات والصور') }}</h3>
                                        <p class="pf-section__desc">{{ get_phrase('ارفع صورة شخصية أو شهادة الميلاد / البطاقة') }}</p>
                                    </div>
                                </div>
                                <div class="pf-section__body">
                                    <div class="pf-uploads">
                                        <label class="pf-upload {{ $user_details->photo ? 'has-preview' : '' }}"
                                            id="photo-upload-box" for="photo">
                                            <input type="file" id="photo" name="photo" accept="image/*">
                                            <div class="pf-upload__icon" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                                    <circle cx="12" cy="13" r="4" />
                                                </svg>
                                            </div>
                                            <p class="pf-upload__title">{{ get_phrase('الصورة الشخصية') }}</p>
                                            <p class="pf-upload__text">{{ get_phrase('اضغط للاختيار أو اسحب الصورة هنا') }}</p>
                                            @if ($user_details->photo)
                                                <div class="pf-upload__preview" id="photo-preview-box">
                                                    <img src="{{ get_image($user_details->photo) }}" alt="">
                                                    <span class="pf-upload__preview-label">{{ get_phrase('الصورة الحالية') }}</span>
                                                </div>
                                            @else
                                                <div class="pf-upload__preview d-none" id="photo-preview-box">
                                                    <img src="" alt="">
                                                    <span class="pf-upload__preview-label">{{ get_phrase('معاينة الصورة') }}</span>
                                                </div>
                                            @endif
                                        </label>

                                        <label class="pf-upload {{ $user_details->national_image ? 'has-preview' : '' }}"
                                            id="national-upload-box" for="national_image">
                                            <input type="file" id="national_image" name="national_image" accept="image/*"
                                                @if ($needsNationalImage) required @endif>
                                            <div class="pf-upload__icon" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <rect x="2" y="5" width="20" height="14" rx="2" />
                                                    <circle cx="8" cy="12" r="2" />
                                                    <path d="M14 10h4M14 14h4" />
                                                </svg>
                                            </div>
                                            <p class="pf-upload__title">
                                                {{ get_phrase('شهادة الميلاد / البطاقة') }}
                                                @if ($nationalImageRequired)
                                                    <span style="color:#dc2626;">*</span>
                                                @else
                                                    <span style="color:#64748b;font-weight:600;font-size:.8em;">({{ get_phrase('اختياري') }})</span>
                                                @endif
                                            </p>
                                            <p class="pf-upload__text">{{ get_phrase('ارفع صورة واضحة للمستند') }}</p>
                                            @error('national_image')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                            @if ($user_details->national_image)
                                                <div class="pf-upload__preview" id="national-preview-box">
                                                    <img src="{{ get_image($user_details->national_image) }}" alt="">
                                                    <span class="pf-upload__preview-label">{{ get_phrase('المستند الحالي') }}</span>
                                                </div>
                                            @else
                                                <div class="pf-upload__preview d-none" id="national-preview-box">
                                                    <img src="" alt="">
                                                    <span class="pf-upload__preview-label">{{ get_phrase('معاينة المستند') }}</span>
                                                </div>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            </section>

                            {{-- كلمة المرور --}}
                            <section class="pf-section">
                                <div class="pf-section__head">
                                    <div class="pf-section__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="pf-section__title">{{ get_phrase('تغيير كلمة المرور') }}</h3>
                                        <p class="pf-section__desc">{{ get_phrase('اترك الحقول فارغة إذا لم ترد تغييرها') }}</p>
                                    </div>
                                </div>
                                <div class="pf-section__body">
                                    <div class="pf-grid">
                                        <div class="pf-field">
                                            <label for="old_password">{{ get_phrase('كلمة السر القديمة') }}</label>
                                            <input type="password"
                                                class="form-control @error('old_password') is-invalid @enderror"
                                                name="old_password" value="{{ old('old_password') }}"
                                                id="old_password" autocomplete="current-password">
                                            @error('old_password')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="pf-field">
                                            <label for="new_password">{{ get_phrase('كلمة السر الحديثة') }}</label>
                                            <input type="password"
                                                class="form-control @error('new_password') is-invalid @enderror"
                                                name="new_password" value="{{ old('new_password') }}"
                                                id="new_password" autocomplete="new-password">
                                            <span class="pf-hint">{{ get_phrase('اختياري — فقط عند الرغبة في التغيير') }}</span>
                                            @error('new_password')
                                                <span class="pf-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <div class="pf-actions">
                                <button type="submit" class="pf-btn pf-btn--primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    {{ get_phrase('حفظ التغيرات') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
<script>
    function bindImagePreview(inputId, previewBoxId, uploadBoxId, alsoUpdateAvatar) {
        const input = document.getElementById(inputId);
        const previewBox = document.getElementById(previewBoxId);
        const uploadBox = document.getElementById(uploadBoxId);
        if (!input || !previewBox || !uploadBox) return;

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            const img = previewBox.querySelector('img');
            if (img) img.src = url;

            previewBox.classList.remove('d-none');
            uploadBox.classList.add('has-preview');

            if (alsoUpdateAvatar) {
                const avatar = document.getElementById('profile-avatar-preview');
                if (avatar) avatar.src = url;
            }
        });
    }

    bindImagePreview('photo', 'photo-preview-box', 'photo-upload-box', true);
    bindImagePreview('national_image', 'national-preview-box', 'national-upload-box', false);
</script>
@endpush

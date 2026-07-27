@extends('layouts.admin')
@push('title', get_phrase('Create course'))

@section('content')
    <div class="admin-page">
        <div class="tf-workspace">
            <div class="tf-hero">
                <div>
                    <div class="tf-hero__kicker">
                        <i class="fi-rr-e-learning"></i>
                        {{ get_phrase('Courses') }}
                    </div>
                    <h1 class="tf-hero__title">{{ get_phrase('Add new Course') }}</h1>
                    <p class="tf-hero__desc">{{ get_phrase('Fill the essentials below. You can edit curriculum after creating the course.') }}</p>
                </div>
                <div class="tf-hero__actions">
                    <a href="{{ route('admin.courses') }}" class="tf-btn tf-btn--ghost">
                        <i class="fi-rr-arrow-small-left"></i>
                        {{ get_phrase('Back') }}
                    </a>
                </div>
            </div>

            <div class="tf-steps">
                <span class="tf-step is-active"><span class="tf-step__num">1</span>{{ get_phrase('Basics') }}</span>
                <span class="tf-step"><span class="tf-step__num">2</span>{{ get_phrase('Pricing') }}</span>
                <span class="tf-step"><span class="tf-step__num">3</span>{{ get_phrase('Access') }}</span>
                <span class="tf-step"><span class="tf-step__num">4</span>{{ get_phrase('Media') }}</span>
            </div>

            <form action="{{ route('admin.course.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_type" value="general" required>
                <input type="hidden" name="instructors[]" value="{{ auth()->user()->id }}" required>

                <section class="tf-section">
                    <div class="tf-section__head">
                        <span class="tf-section__num">1</span>
                        <div>
                            <h2 class="tf-section__title">{{ get_phrase('Basic information') }}</h2>
                            <p class="tf-section__hint">{{ get_phrase('Title, description, category and status') }}</p>
                        </div>
                    </div>

                    <div class="tf-field">
                        <label class="tf-label" for="title">{{ get_phrase('Title') }} <span class="req">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="form-control ol-form-control"
                            placeholder="{{ get_phrase('Enter Course Title') }}" required>
                        <span class="tf-help">{{ get_phrase('Use a clear name students can recognize quickly') }}</span>
                    </div>

                    <div class="tf-field">
                        <label class="tf-label" for="description">{{ get_phrase('Description') }}</label>
                        <textarea name="description" id="description"
                            placeholder="{{ get_phrase('Enter Description') }}"
                            class="form-control ol-form-control text_editor">{{ old('description') }}</textarea>
                    </div>

                    <div class="tf-row tf-row--2">
                        <div class="tf-field">
                            <label class="tf-label" for="category_id">{{ get_phrase('Category') }} <span class="req">*</span></label>
                            <select class="ol-select2" name="category_id" id="category_id" required>
                                <option value="">{{ get_phrase('Select a category') }}</option>
                                @foreach (App\Models\Category::where('parent_id', 0)->orderBy('title', 'desc')->get() as $category)
                                    <optgroup label="{{ $category->title }}">
                                        @foreach ($category->childs as $sub_category)
                                            <option value="{{ $sub_category->id }}"
                                                {{ old('category_id') == $sub_category->id ? 'selected' : '' }}>
                                                {{ $sub_category->title }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="tf-field">
                            <span class="tf-label">{{ get_phrase('Create as') }} <span class="req">*</span></span>
                            <div class="tf-choice tf-choice--2">
                                <label for="status_active">
                                    <input type="radio" value="active" name="status" id="status_active" required checked>
                                    <span class="tf-choice__card">
                                        <span>
                                            <strong>{{ get_phrase('Active') }}</strong>
                                            <small>{{ get_phrase('Visible to students') }}</small>
                                        </span>
                                    </span>
                                </label>
                                <label for="status_inactive">
                                    <input type="radio" value="inactive" name="status" id="status_inactive" required>
                                    <span class="tf-choice__card">
                                        <span>
                                            <strong>{{ get_phrase('Inactive') }}</strong>
                                            <small>{{ get_phrase('Hidden from catalog') }}</small>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="tf-field">
                        <div class="admin-check-row mb-0">
                            <input type="checkbox" name="show_on_home" value="1" class="form-check-input"
                                id="show_on_home" {{ old('show_on_home') ? 'checked' : '' }}>
                            <label for="show_on_home" class="admin-check-row__label">
                                {{ get_phrase('إظهار في أحدث الكورسات بالصفحة الرئيسية') }}
                            </label>
                        </div>
                        <span class="tf-help">{{ get_phrase('يظهر الكورس في سايدبار أحدث الكورسات في الرئيسية') }}</span>
                    </div>
                </section>

                <section class="tf-section">
                    <div class="tf-section__head">
                        <span class="tf-section__num">2</span>
                        <div>
                            <h2 class="tf-section__title">{{ get_phrase('Pricing') }}</h2>
                            <p class="tf-section__hint">{{ get_phrase('Choose free or paid and set the price') }}</p>
                        </div>
                    </div>

                    <div class="tf-field">
                        <span class="tf-label">{{ get_phrase('Pricing type') }} <span class="req">*</span></span>
                        <div class="tf-choice tf-choice--2">
                            <label for="paid">
                                <input type="radio" name="is_paid" value="1" id="paid"
                                    onchange="$('#paid-section').slideDown(200)" checked>
                                <span class="tf-choice__card">
                                    <span>
                                        <strong>{{ get_phrase('Paid') }}</strong>
                                        <small>{{ get_phrase('Require payment to enroll') }}</small>
                                    </span>
                                </span>
                            </label>
                            <label for="free">
                                <input type="radio" name="is_paid" value="0" id="free"
                                    onchange="$('#paid-section').slideUp(200)">
                                <span class="tf-choice__card">
                                    <span>
                                        <strong>{{ get_phrase('Free') }}</strong>
                                        <small>{{ get_phrase('Open access for students') }}</small>
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="paid-section tf-panel" id="paid-section">
                        <div class="tf-field mb-0" style="padding-left:0;margin-bottom:14px;">
                            <label for="price" class="tf-label">
                                {{ get_phrase('Price') }} <small>({{ currency() }})</small> <span class="req">*</span>
                            </label>
                            <input type="number" name="price" value="{{ old('price') }}"
                                class="form-control ol-form-control" id="price"
                                min="1" step=".01"
                                placeholder="{{ get_phrase('Enter your course price') }} ({{ currency() }})">
                        </div>

                        <div class="admin-check-row" style="margin-bottom:12px;">
                            <input type="checkbox" name="discount_flag" value="1" class="form-check-input" id="discount_flag">
                            <label for="discount_flag" class="admin-check-row__label">
                                {{ get_phrase('Check if this course has discount') }}
                            </label>
                        </div>

                        <div class="tf-field mb-0" id="discount_price_row" style="display:none;padding-left:0;">
                            <label for="discount_price" class="tf-label">{{ get_phrase('Discounted price') }}</label>
                            <input type="number" name="discount_price" value="{{ old('discount_price') }}"
                                class="form-control ol-form-control" id="discount_price"
                                min="1" step=".01"
                                placeholder="{{ get_phrase('Enter your discount price') }} ({{ currency() }})">
                        </div>
                    </div>
                </section>

                <section class="tf-section">
                    <div class="tf-section__head">
                        <span class="tf-section__num">3</span>
                        <div>
                            <h2 class="tf-section__title">{{ get_phrase('Access') }}</h2>
                            <p class="tf-section__hint">{{ get_phrase('Expiry period and drip content') }}</p>
                        </div>
                    </div>

                    <div class="tf-field">
                        <span class="tf-label">{{ get_phrase('Expiry period') }}</span>
                        <div class="tf-choice tf-choice--2">
                            <label for="lifetime_expiry_period">
                                <input type="radio" id="lifetime_expiry_period" name="expiry_period" value="lifetime"
                                    onchange="checkExpiryPeriod(this)" checked>
                                <span class="tf-choice__card">
                                    <span><strong>{{ get_phrase('Lifetime') }}</strong></span>
                                </span>
                            </label>
                            <label for="limited_expiry_period">
                                <input type="radio" id="limited_expiry_period" name="expiry_period" value="limited_time"
                                    onchange="checkExpiryPeriod(this)">
                                <span class="tf-choice__card">
                                    <span><strong>{{ get_phrase('Limited time') }}</strong></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="tf-field" id="number_of_month" style="display:none">
                        <label class="tf-label" for="number_of_month_input">{{ get_phrase('Number of month') }}</label>
                        <input class="form-control ol-form-control" type="number" name="number_of_month"
                            id="number_of_month_input" value="{{ old('number_of_month') }}" min="1"
                            placeholder="{{ get_phrase('After purchase, students can access the course until your selected month.') }}">
                    </div>

                    <div class="tf-field">
                        <span class="tf-label">{{ get_phrase('Enable drip content') }} <span class="req">*</span></span>
                        <div class="tf-choice tf-choice--2">
                            <label for="drip_off">
                                <input type="radio" value="0" name="enable_drip_content" id="drip_off" required checked>
                                <span class="tf-choice__card"><span><strong>{{ get_phrase('Off') }}</strong></span></span>
                            </label>
                            <label for="drip_on">
                                <input type="radio" value="1" name="enable_drip_content" id="drip_on" required>
                                <span class="tf-choice__card"><span><strong>{{ get_phrase('On') }}</strong></span></span>
                            </label>
                        </div>
                    </div>
                </section>

                <section class="tf-section">
                    <div class="tf-section__head">
                        <span class="tf-section__num">4</span>
                        <div>
                            <h2 class="tf-section__title">{{ get_phrase('Thumbnail') }}</h2>
                            <p class="tf-section__hint">{{ get_phrase('Course cover image') }}</p>
                        </div>
                    </div>
                    <div class="tf-field">
                        <label class="tf-upload" for="thumbnail">
                            <span class="tf-upload__box" id="thumbnailPreview">
                                <i class="fi-rr-cloud-upload-alt"></i>
                                <strong>{{ get_phrase('Click to upload thumbnail') }}</strong>
                                <small>{{ get_phrase('PNG, JPG recommended') }}</small>
                            </span>
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*">
                        </label>
                    </div>
                </section>

                <div class="tf-actions">
                    <span class="tf-actions__hint">{{ get_phrase('You can edit lessons after saving') }}</span>
                    <div class="tf-actions__btns">
                        <a href="{{ route('admin.courses') }}" class="tf-btn tf-btn--ghost">{{ get_phrase('Cancel') }}</a>
                        <button type="submit" class="tf-btn tf-btn--primary">
                            <i class="fi-rr-check"></i>
                            {{ get_phrase('Submit') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        "use strict";

        function checkExpiryPeriod(e) {
            var expiryPeriod = $(e).val();
            if (expiryPeriod == 'lifetime') {
                $('#number_of_month').slideUp();
            } else {
                $('#number_of_month').slideDown();
            }
        }

        $(document).ready(function() {
            if ($('#discount_flag').is(':checked')) {
                $('#discount_price_row').show();
            }

            $('#discount_flag').change(function() {
                if ($(this).is(':checked')) {
                    $('#discount_price_row').fadeIn();
                } else {
                    $('#discount_price_row').fadeOut();
                }
            });

            $('#thumbnail').on('change', function(e) {
                const file = e.target.files && e.target.files[0];
                const preview = $('#thumbnailPreview');
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.html('<img src="' + ev.target.result + '" alt="thumbnail preview">');
                    preview.addClass('is-filled');
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endpush

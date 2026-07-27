@extends('layouts.admin')
@push('title', get_phrase('Edit Coupon'))
@push('meta')@endpush
@push('css')@endpush

@section('content')
@php
    // تحويل balance_handling من JSON إلى array إذا كان موجوداً
    $balance_handling = $coupon_details->balance_handling ? json_decode($coupon_details->balance_handling, true) : [];
@endphp

<div class="admin-page">
    <div class="tf-workspace tf-workspace--wide">
        <div class="tf-hero">
            <div>
                <div class="tf-hero__kicker">
                    <i class="fi-rr-ticket"></i>
                    {{ get_phrase('Coupons') }}
                </div>
                <h1 class="tf-hero__title">{{ get_phrase('Edit Coupon') }}: {{ $coupon_details->title }}</h1>
                <p class="tf-hero__desc">{{ get_phrase('Update discount settings, users and validity dates.') }}</p>
            </div>
            <div class="tf-hero__actions">
                <a href="{{ route('admin.coupons') }}" class="tf-btn tf-btn--ghost">
                    <i class="fi-rr-arrow-small-left"></i>
                    {{ get_phrase('Back') }}
                </a>
            </div>
        </div>

<style>
    .coupon-form-container {
        max-width: 1500px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #007bff;
    }
    
    .form-section h5 {
        color: #007bff;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-group-full {
        grid-column: 1 / -1;
    }
    
    .form-group-half {
        grid-column: span 1;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .form-check {
        margin-bottom: 10px;
        padding: 10px;
        background: white;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }
    
    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }
    
    .required {
        color: #dc3545;
        font-weight: bold;
    }
    
    .form-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .input-group .btn {
        border-radius: 0 8px 8px 0;
    }
    
    .input-group .form-control {
        border-radius: 8px 0 0 8px;
    }
    
    .select2-container {
        width: 100% !important;
    }
    
    .select2-container--default .select2-selection--multiple {
        border-radius: 8px;
        border: 1px solid #ced4da;
        min-height: 45px;
    }
    
    .select2-container--default .select2-selection--single {
        border-radius: 8px;
        border: 1px solid #ced4da;
        height: 45px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 43px;
        padding-left: 15px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }
    .row.d-flex {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
    }

    .row.d-flex > .col-6 {
        display: flex;
        flex-direction: column;
    }

    .form-section {
        height: 100%;
    }
</style>

<div class="coupon-form-container">
    <div class="ol-card" style="border:none;box-shadow:none;background:transparent;">
        <div class="ol-card-body">
            <form action="{{ route('admin.coupon.update', $coupon_details->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                
                @if($coupon_details->type != 'discount')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    {{ get_phrase('You are editing a') }} <strong>{{ $coupon_details->type }}</strong> {{ get_phrase('coupon. Only discount coupons can be edited here.') }}
                </div>
                @endif

                <div class="row d-flex">
                    <div class="col-6 mb-3">
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-info-circle"></i> {{ get_phrase('Basic Information') }}</h5>

                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="type">{{ get_phrase('Coupon Type') }}<span class="required">*</span></label>
                                    <select class="form-control" name="type" id="type" required disabled>
                                        <option value="discount" selected>{{ get_phrase('Discount Coupon') }}</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        {{ get_phrase('لا يمكن تغيير نوع الكوبون') }}
                                    </small>
                                    <input type="hidden" name="type" value="discount">
                                </div>
                            </div>

                            <div class="form-row d-flex">
                                <div class="col-6">
                                    <label class="form-label" for="code">{{ get_phrase('Coupon Code') }}<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="code" id="code" value="{{ $coupon_details->code }}" placeholder="{{ get_phrase('Enter coupon code') }}" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateRandomCode()">
                                            <i class="fas fa-random"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group-half">
                                        <label class="form-label" for="title">{{ get_phrase('Coupon Title') }}<span class="required">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            value="{{ $coupon_details->title }}" placeholder="{{ get_phrase('Enter coupon title') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 mb-3">
                        <!-- Discount Settings Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-percentage"></i> {{ get_phrase('Discount Settings') }}</h5>
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="discount">{{ get_phrase('Discount Percentage (%)') }}</label>
                                    <input type="number" max="100" min="0" class="form-control" name="value" id="discount"
                                        value="{{ $coupon_details->value }}" placeholder="{{ get_phrase('Enter discount percentage') }}">
                                    <small class="form-text">
                                        {{ get_phrase('إذا تم تعيين نسبة الخصم، سيتم استخدامها بدلاً من مبلغ الخصم الثابت') }}
                                    </small>
                                </div>
                                
                                <div class="form-group-half">
                                    <label class="form-label" for="discount_type">{{ get_phrase('Discount Type') }}</label>
                                    <select class="form-control ol-select2" name="discount_type" id="discount_type">
                                        <option value="all_purchases" {{ $coupon_details->discount_type == 'all_purchases' ? 'selected' : '' }}>{{ get_phrase('All Purchases') }}</option>
                                        <option value="first_purchase" {{ $coupon_details->discount_type == 'first_purchase' ? 'selected' : '' }}>{{ get_phrase('First Purchase Only') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="minimum_amount">{{ get_phrase('Minimum Purchase Amount') }}</label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="minimum_amount" id="minimum_amount"
                                        value="{{ $coupon_details->minimum_amount }}" placeholder="{{ get_phrase('Enter minimum amount') }}">
                                    <small class="form-text">
                                        {{ get_phrase('أقل مبلغ مطلوب لتفعيل الخصم') }}
                                    </small>
                                </div>
                                
                                <div class="form-group-half">
                                    <label class="form-label" for="maximum_discount">{{ get_phrase('Maximum Discount Amount') }}</label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="maximum_discount" id="maximum_discount"
                                        value="{{ $coupon_details->maximum_discount }}" placeholder="{{ get_phrase('Enter maximum discount') }}">
                                    <small class="form-text">
                                        {{ get_phrase('أقصى مخصم يمكن تطبيقه') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6 mb-3">
                        <!-- Course & User Settings Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-users"></i> {{ get_phrase('Course & User Settings') }}</h5>
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="course_id">{{ get_phrase('Course Selection') }}</label>
                                    <select class="form-control ol-select2" name="course_id" id="course_id">
                                        <option value="">{{ get_phrase('General (All Courses)') }}</option>
                                        @foreach ($courses as $course)
                                            <option value="{{$course->id}}" {{ $coupon_details->course_id == $course->id ? 'selected' : '' }}>{{$course->title}}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text">
                                        {{ get_phrase('اختر كورس معين أو اتركه عام لجميع الكورسات') }}
                                    </small>
                                </div>
                                
                                <div class="form-group-half">
                                    <label class="form-label" for="multiple_user_id">
                                        {{ get_phrase('Users') }}<span class="required">*</span>
                                    </label>
                                    <select class="ol-select2" name="user_id[]" multiple="multiple" id="users">
                                        @php
                                            $selectedUsers = json_decode($coupon_details->user_id, true) ?? [];
                                        @endphp
                                        <option value="0" id="allStudent" {{ in_array(0, $selectedUsers) ? 'selected' : '' }}>{{ get_phrase('All Students') }}</option>
                                        @foreach ($students as $student)
                                            <option class="onlyStudent" value="{{$student->id}}" {{ in_array($student->id, $selectedUsers) ? 'selected' : '' }}>
                                                {{$student->name}} | ({{$student->phone}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="limit">{{ get_phrase('Usage Limit') }}</label>
                                    <input type="number" class="form-control" name="limit" id="limit"
                                        value="{{ $coupon_details->limit }}" placeholder="{{ get_phrase('Enter usage limit') }}">
                                    <small class="form-text">
                                        {{ get_phrase('عدد مرات استخدام الكوبون') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 mb-3">
                        <!-- Date Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-calendar-alt"></i> {{ get_phrase('Date Settings') }}</h5>
                            
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="start_date">{{ get_phrase('Start Date') }}<span class="required">*</span></label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="{{ $coupon_details->start_date ? $coupon_details->start_date->format('Y-m-d') : '' }}">
                                </div>
                                
                                <div class="form-group-half">
                                    <label class="form-label" for="expiry">{{ get_phrase('Expiry Date') }}<span class="required">*</span></label>
                                    <input type="date" class="form-control" name="expiry" id="expiry"
                                        value="{{ $coupon_details->expiry ? $coupon_details->expiry->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group-full text-center">
                        <button type="submit" class="tf-btn tf-btn--primary">
                            <i class="fas fa-save"></i> {{ get_phrase('Update Coupon') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<script>
    function generateRandomCode() {
        var length = 8;
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomCode = '';

        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            randomCode += characters[randomIndex];
        }

        document.getElementById('code').value = randomCode;
    }

    // إدارة المستخدمين
    $('#users').on('change', function () {
        var selected = $(this).val() || [];
        if (selected.includes("0")) {
            $('.onlyStudent').prop('disabled', true).prop('selected', false);
        } else {
            $('#allStudent').prop('disabled', true).prop('selected', false);
            $('.onlyStudent').prop('disabled', false);
        }

        if (selected.length === 0) {
            $('#allStudent').prop('disabled', false);
            $('.onlyStudent').prop('disabled', false);
        }

        $('#users').trigger('change.select2');
    });

    $(document).ready(function() {
        // تهيئة Select2
        $('#users').select2({
            placeholder: '{{ get_phrase("Select users") }}',
            allowClear: true
        });
        
        $('#course_id').select2({
            placeholder: '{{ get_phrase("Select course") }}',
            allowClear: true
        });
        
        $('#discount_type').select2();
        $('#status').select2();
        
        // منع تغيير نوع الكوبون
        $('#type').prop('disabled', true);
    });
</script>
@endsection

@extends('layouts.admin')
@push('title', get_phrase('Create Coupon'))
@push('meta')@endpush
@push('css')@endpush

@section('content')
@php
        $students = \App\Models\User::where('role','student')->get();
        $courses = \App\Models\Course::all();
@endphp

<div class="admin-page">
    <div class="tf-workspace tf-workspace--wide">
        <div class="tf-hero">
            <div>
                <div class="tf-hero__kicker">
                    <i class="fi-rr-ticket"></i>
                    {{ get_phrase('Coupons') }}
                </div>
                <h1 class="tf-hero__title">{{ get_phrase('Create New Coupon') }}</h1>
                <p class="tf-hero__desc">{{ get_phrase('Configure type, amount, limits and validity dates.') }}</p>
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
        align-items: stretch; /* 👈 يخلي كل الأعمدة تاخد نفس الطول */
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
            <form action="{{ route('admin.coupon.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex">
                    <div class="col-6 mb-3">
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-info-circle"></i> {{ get_phrase('Basic Information') }}</h5>

                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="type">{{ get_phrase('Coupon Type') }}<span class="required">*</span></label>
                                    <select class="form-control" name="type" id="type" required onchange="toggleCouponFields()">
                                        <option value="" disabled>{{ get_phrase('Choose coupon type ...') }}</option>
                                        <option value="recharge" selected>{{ get_phrase('Recharge Coupon') }}</option>
                                        <option value="discount">{{ get_phrase('Discount Coupon') }}</option>
                                        <option value="payment">{{ get_phrase('Payment Coupon') }}</option>
                                    </select>
                                </div>
                                
                                <div class="form-group-half" id="input-value">
                                    <label class="form-label" for="value">{{ get_phrase('Recharge Amount') }}<span class="required">*</span></label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="value" id="value"
                                        placeholder="{{ get_phrase('enter recharge amount') }}">
                                </div>
                            </div>

                            <div class="form-row" id="coupon-setting" style="display: none;">
                                <div class="col-6">
                                    <div class="form-group-half">
                                        <label class="form-label" for="code">{{ get_phrase('Coupon Code') }}<span class="required">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="code" id="code"
                                                placeholder="{{ get_phrase('Enter coupon code') }}">
                                            <button type="button" class="btn btn-outline-secondary" onclick="generateRandomCode()">
                                                <i class="fas fa-random"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group-half">
                                        <label class="form-label" for="title">{{ get_phrase('Coupon Title') }}<span class="required">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            placeholder="{{ get_phrase('Enter coupon title') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3" id="discount-settings" style="display: none;">
                        <!-- Discount Settings Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-percentage"></i> {{ get_phrase('Discount Settings') }}</h5>
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="discount">{{ get_phrase('Discount Percentage (%)') }}</label>
                                    <input type="number" max="100" min="0" class="form-control" name="discount" id="discount"
                                        placeholder="{{ get_phrase('Enter discount percentage') }}">
                                </div>
                                
                                <div class="form-group-half">
                                    <label class="form-label" for="discount_type">{{ get_phrase('Discount Type') }}</label>
                                    <select class="form-control ol-select2" name="discount_type" id="discount_type">
                                        <option value="all_purchases">{{ get_phrase('All Purchases') }}</option>
                                        <option value="first_purchase">{{ get_phrase('First Purchase Only') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="minimum_amount">{{ get_phrase('Minimum Purchase Amount') }}</label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="minimum_amount" id="minimum_amount"
                                        placeholder="{{ get_phrase('Enter minimum amount') }}">
                                </div>
                                
                                <div class="form-group-half">
                                    <label class="form-label" for="maximum_discount">{{ get_phrase('Maximum Discount Amount') }}</label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="maximum_discount" id="maximum_discount"
                                        placeholder="{{ get_phrase('Enter maximum discount') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3" id="course-user-settings" style="display: none;">
                        <!-- Course & User Settings Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-users"></i> {{ get_phrase('Course & User Settings') }}</h5>
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="course_id">{{ get_phrase('Course Selection') }}</label>
                                    <select class="form-control ol-select2" name="course_id" id="course_id" onchange="toggleGeneralOption()">
                                        <option value="">{{ get_phrase('General (All Courses)') }}</option>
                                        @foreach ($courses as $course)
                                            <option value="{{$course->id}}">{{$course->title}}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text">
                                        {{ get_phrase('اختر كورس معين أو اتركه عام لجميع الكورسات') }}
                                    </small>
                                </div>
                                
                                <div class="form-group-half" id="usersView">
                                    <label class="form-label" for="multiple_user_id">
                                        {{ get_phrase('Users') }}<span class="required">*</span>
                                    </label>
                                    <select class="ol-select2" name="user_id[]" multiple="multiple" id="users">
                                        <option value="0" selected id="allStudent">{{ get_phrase('All Students') }}</option>
                                        @foreach ($students as $student)
                                            <option class="onlyStudent" value="{{$student->id}}">
                                                {{$student->name}} | ({{$student->phone}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row" id="limit-view">
                                <div class="form-group-half">
                                    <label class="form-label" for="limit">{{ get_phrase('Usage Limit') }}</label>
                                    <input type="number" class="form-control" name="limit" id="limit"
                                        placeholder="{{ get_phrase('Enter usage limit') }}">
                                    <small class="form-text">
                                        {{ get_phrase('عدد مرات استخدام الكوبون') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3" id="balance-handling-settings" style="display: none;">
                        <!-- Balance Handling Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-wallet"></i> {{ get_phrase('Balance Handling Options') }}</h5>
                            <div class="form-row">
                                <div class="form-group-full">
                                    
                                    <!-- ✅ الخيار 1: المستخدم يعيد استخدام الرصيد -->
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="balance_handling[]" id="balance_reuse" value="reuse" checked>
                                        <label class="form-check-label" for="balance_reuse">
                                            <strong>{{ get_phrase('User can reuse remaining balance') }}</strong>
                                            <br><small class="text-muted">{{ get_phrase('المستخدم يمكنه استخدام الرصيد المتبقي مرة أخرى') }}</small>
                                        </label>
                                    </div>
                        
                                    <!-- ✅ الخيار 2: مستخدم آخر يمكنه استخدام الرصيد -->
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="balance_handling[]" id="balance_other" value="reuse_others">
                                        <label class="form-check-label" for="balance_other">
                                            <strong>{{ get_phrase('Allow other users to use remaining balance') }}</strong>
                                            <br><small class="text-muted">{{ get_phrase('مستخدم آخر يمكنه استخدام الرصيد المتبقي') }}</small>
                                        </label>
                                    </div>
                        
                                    <!-- ✅ الخيار 3: الرصيد المتبقي يذهب للمحفظة -->
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="balance_handling[]" id="balance_wallet" value="wallet">
                                        <label class="form-check-label" for="balance_wallet">
                                            <strong>{{ get_phrase('Transfer remaining balance to user wallet') }}</strong>
                                            <br><small class="text-muted">{{ get_phrase('الرصيد المتبقي ينتقل لمحفظة المستخدم') }}</small>
                                        </label>
                                    </div>
                        
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3" id="recharge-settings">
                        <!-- Recharge Settings Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-plus-circle"></i> {{ get_phrase('Recharge Settings') }}</h5>
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="coupon_count">{{ get_phrase('Number of Coupons') }}</label>
                                    <input type="number" min="1" max="1000" class="form-control" name="coupon_count" id="coupon_count"
                                        placeholder="{{ get_phrase('Enter number of coupons to generate') }}">
                                    <small class="form-text">
                                        {{ get_phrase('سيتم إنشاء هذا العدد من الكوبونات تلقائياً') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <!-- Date & Status Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-calendar-alt"></i> {{ get_phrase('Date') }}</h5>
                            
                            <div class="form-row">
                                <div class="form-group-half">
                                    <label class="form-label" for="start_date">{{ get_phrase('Start Date') }}<span class="required">*</span></label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        placeholder="{{ get_phrase('Enter coupon start_date') }}"6>
                                </div>
                                
                                <div class="form-group-half">
                                    <label class="form-label" for="expiry">{{ get_phrase('Expiry Date') }}<span class="required">*</span></label>
                                    <input type="date" class="form-control" name="expiry" id="expiry"
                                        placeholder="{{ get_phrase('Enter coupon expiry') }}">
                                </div>
                            </div>

                            <div class="form-check mt-3 " id="download-excel">
                                <input type="checkbox" class="form-check-input" id="download_excel" name="download_excel" value="1">
                                <label class="form-check-label" for="download_excel">
                                    {{ get_phrase('After adding, download the coupons as an Excel file') }}
                                </label>
                            </div>

                            <div class="form-check mt-3 " id="printing-cards">
                                <input type="checkbox" class="form-check-input" id="printing" name="printing" value="1">
                                <label class="form-check-label" for="printing">
                                    {{ get_phrase('After adding, display the cards for printing') }}
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group-full text-center">
                        <button type="submit" class="tf-btn tf-btn--primary">
                            <i class="fas fa-plus"></i> {{ get_phrase('Create Coupon') }}
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
    document.addEventListener('DOMContentLoaded', function () {
        const walletCheckbox = document.getElementById('balance_wallet');
        const reuseCheckbox = document.getElementById('balance_reuse');
        const otherCheckbox = document.getElementById('balance_other');

        function handleBalanceCheckboxes() {
            if (walletCheckbox.checked) {
                // لو تم اختيار "المحفظة" → نلغي الاختيار عن الاتنين التانيين
                reuseCheckbox.checked = false;
                otherCheckbox.checked = false;
            } else if (reuseCheckbox.checked || otherCheckbox.checked) {
                // لو اختار واحد من الاتنين دول → نشيل العلامة من "المحفظة"
                walletCheckbox.checked = false;
            }
        }

        walletCheckbox.addEventListener('change', handleBalanceCheckboxes);
        reuseCheckbox.addEventListener('change', handleBalanceCheckboxes);
        otherCheckbox.addEventListener('change', handleBalanceCheckboxes);
    });
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

    function toggleCouponFields() {
        const type = document.getElementById('type').value;
        
        // الحصول على الأقسام
        const discountSettings        = document.getElementById('discount-settings');
        const courseUserSettings      = document.getElementById('course-user-settings');
        const balanceHandlingSettings = document.getElementById('balance-handling-settings');
        const rechargeSettings        = document.getElementById('recharge-settings');
        const couponSetting           = document.getElementById('coupon-setting');
        const downloadExcel           = document.getElementById('download-excel'); 
        const printingCards           = document.getElementById('printing-cards');
        const inputValue              = document.getElementById('input-value'); 
        const limitView               = document.getElementById('limit-view'); 
        const users                   = document.getElementById('usersView'); 
        
        
        // الحصول على الحقول الفردية
        const valueField = document.getElementById('value');
        const valueLabel = valueField.previousElementSibling;

        // إخفاء جميع الأقسام أولاً
        discountSettings.style.display        = 'none';
        courseUserSettings.style.display      = 'none';
        balanceHandlingSettings.style.display = 'none';
        rechargeSettings.style.display        = 'none';
        couponSetting.style.display           = 'none';


        // إظهار الأقسام المناسبة حسب نوع الكوبون
        if (type === 'discount') {
            // كوبون الخصم
            discountSettings.style.display    = 'block';
            courseUserSettings.style.display  = 'block';
            couponSetting.style.display       = 'flex';
            downloadExcel.style.display       = 'none';
            printingCards.style.display       = 'none';
            inputValue.style.display          = 'none';
            limitView.style.display           = 'block';
            users.style.display               = 'block';
            
            
        } else if (type === 'recharge') {
            // كوبون الشحن
            rechargeSettings.style.display    = 'block';
            couponSetting.style.display       = 'none';
            downloadExcel.style.display       = 'block';
            printingCards.style.display       = 'block';
            inputValue.style.display          = 'block';
            valueLabel.textContent            = '{{ get_phrase("Recharge Amount") }} *';
            valueField.placeholder            = '{{ get_phrase("Enter recharge amount") }}';
            
        } else if (type === 'payment') {
            // كوبون الدفع
            courseUserSettings.style.display      = 'block';
            couponSetting.style.display           = 'none';
            balanceHandlingSettings.style.display = 'block';
            downloadExcel.style.display           = 'block';
            rechargeSettings.style.display        = 'block';
            printingCards.style.display           = 'block';
            inputValue.style.display              = 'block';
            valueLabel.textContent                = '{{ get_phrase("Payment Amount") }} *';
            valueField.placeholder                = '{{ get_phrase("Enter payment amount") }}';
            limitView.style.display               = 'none';
            users.style.display                   = 'none';
        }
    }

    function toggleGeneralOption() {
        const courseId = document.getElementById('course_id').value;
        // إذا تم اختيار كورس معين، الكوبون ليس عام
        // إذا لم يتم اختيار كورس، الكوبون عام
        // هذا يتم التعامل معه في الـ Controller تلقائياً
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
        
        $('#type').select2({
            placeholder: '{{ get_phrase("Choose coupon type") }}'
        });
        
        $('#course_id').select2({
            placeholder: '{{ get_phrase("Select course") }}',
            allowClear: true
        });
        
        $('#discount_type').select2();
        
        // // تعيين التاريخ الحالي كتاريخ بداية افتراضي
        // const today = new Date().toISOString().split('T')[0];
        // document.getElementById('start_date').value = today;
        
        // // تعيين تاريخ انتهاء بعد شهر
        // const nextMonth = new Date();
        // nextMonth.setMonth(nextMonth.getMonth() + 1);
        // document.getElementById('expiry').value = nextMonth.toISOString().split('T')[0];
    });
</script>
@endsection

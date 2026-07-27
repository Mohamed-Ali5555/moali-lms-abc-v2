<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ get_phrase('Print Coupons') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            direction: rtl;
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .print-header button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
            transition: all 0.3s;
        }

        .print-header button:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
        }

        .coupons-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }

        .coupon-card {
            border-radius: 20px;
            border: 1px solid #ddd;
            padding: 30px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            min-height: 280px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        /* ألوان حسب قيمة الكوبون */
        /* من 1 ل 50 */
        .coupon-card.value-1-50 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* من 50 ل 100 */
        .coupon-card.value-50-100 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        /* من 100 ل 150 */
        .coupon-card.value-100-150 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        /* من 150 ل 200 */
        .coupon-card.value-150-200 {
            background: linear-gradient(135deg, #82e9a4 0%, #67eed5 100%);
        }

        /* من 200 ل 250 */
        .coupon-card.value-200-250 {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        /* من 250 ل 300 */
        .coupon-card.value-250-300 {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        }

        /* من 300 ل 350 */
        .coupon-card.value-300-350 {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        /* من 350 ل 400 */
        .coupon-card.value-350-400 {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        }

        /* من 400 ل 450 */
        .coupon-card.value-400-450 {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }

        /* من 450 ل 500 */
        .coupon-card.value-450-500 {
            background: linear-gradient(135deg, #ff8a80 0%, #ea4c89 100%);
        }

        /* أكثر من 500 */
        .coupon-card.value-500-plus {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        .coupon-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .coupon-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .logo-container {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            backdrop-filter: blur(10px);
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .teacher-name {
            text-align: right;
            flex: 1;
            margin-align: 15px;
        }

        .teacher-name h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .teacher-name p {
            font-size: 12px;
            opacity: 0.9;
        }

        .coupon-body {
            text-align: center;
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .coupon-title {
            font-size: 16px;
            margin-bottom: 15px;
            opacity: 0.95;
            font-weight: 500;
        }

        .coupon-value {
            font-size: 48px;
            font-weight: bold;
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .coupon-value span {
            font-size: 28px;
            margin-right: 5px;
        }

        .coupon-code {
            background: rgba(255, 255, 255, 0.25);
            padding: 12px 20px;
            border-radius: 10px;
            margin-top: 15px;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            backdrop-filter: blur(10px);
            border: 2px dashed rgba(255, 255, 255, 0.5);
        }

        .coupon-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 11px;
            opacity: 0.85;
            position: relative;
            z-index: 1;
        }

        .coupon-type {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .expiry-date {
            font-size: 11px;
        }

        /* إجبار المتصفح على طباعة الخلفيات والألوان */
        .coupon-card,
        .coupon-code,
        .logo-container,
        .coupon-type {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: white;
                padding: 0;
                margin-top: 35px;
            }

            .print-header {
                display: none;
            }

            .coupons-container {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important; /* 3 أعمدة */
                gap: 15px !important; /* مسافة بين الكروت */
                margin: 0 auto !important;
                width: 100%;
                justify-items: center; /* توسيط الكروت أفقياً */
                align-items: start;
            }

            .coupon-card {
                width: 6.5cm !important;
                min-height: 8.5cm !important; /* ارتفاع كافٍ لجميع العناصر */
                height: auto !important;
                padding: 12px !important;
                border: 1px solid #ccc !important;
                border-radius: 10px;
                box-shadow: none !important;
                overflow: visible !important; /* إظهار كل المحتوى */
                page-break-inside: avoid;
                transform: none !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* إخفاء العنصر الديكوري عند الطباعة - كان يغطي المحتوى */
            .coupon-card::before {
                display: none !important;
            }

            .coupon-header {
                margin-bottom: 10px !important;
                display: flex !important;
                flex-shrink: 0 !important;
            }

            .coupon-body {
                display: flex !important;
                flex: 1 !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .coupon-value {
                font-size: 24px !important;
                margin: 8px 0 !important;
                visibility: visible !important;
            }

            .coupon-value span {
                font-size: 18px !important;
            }

            .coupon-code {
                font-size: 16px !important;
                padding: 8px 12px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                visibility: visible !important;
                backdrop-filter: none !important; /* يسبب مشاكل في الطباعة */
            }

            .coupon-footer {
                display: flex !important;
                visibility: visible !important;
                flex-shrink: 0 !important;
            }

            .logo-container {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                width: 50px !important;
                height: 50px !important;
                backdrop-filter: none !important;
            }

            .coupon-type {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .teacher-name h3 {
                font-size: 12px !important;
            }

            .teacher-name p,
            .expiry-date {
                font-size: 9px !important;
            }

            /* التأكد من ظهور الصور عند الطباعة */
            .coupon-card img {
                max-width: 100% !important;
                height: auto !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4 portrait;
                margin: 0.6cm;
            }
        }



        /* تصميم للشاشات الصغيرة */
        @media (max-width: 768px) {
            .coupons-container {
                grid-template-columns: 1fr;
            }

            .coupon-card {
                min-height: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <p style="margin-bottom: 15px; color: #666; font-size: 14px;">
            💡 للحصول على أفضل جودة طباعة، فعّل خيار "الرسومات الخلفية" (Background graphics) في نافذة الطباعة
        </p>
        <button onclick="window.print()">
            🖨️ {{ get_phrase('Print') }}
        </button>
        <button onclick="window.location.href='{{ route('admin.coupons') }}'">
            ← {{ get_phrase('Back to Coupons') }}
        </button>
    </div>

    <div class="coupons-container">
        @foreach($coupons as $coupon)
            @php
                // تحديد الفئة حسب قيمة الكوبون
                $value = (float)$coupon->value;
                if ($value < 50) {
                    $valueClass = 'value-1-50';
                } elseif ($value < 100) {
                    $valueClass = 'value-50-100';
                } elseif ($value < 150) {
                    $valueClass = 'value-100-150';
                } elseif ($value < 200) {
                    $valueClass = 'value-150-200';
                } elseif ($value < 250) {
                    $valueClass = 'value-200-250';
                } elseif ($value < 300) {
                    $valueClass = 'value-250-300';
                } elseif ($value < 350) {
                    $valueClass = 'value-300-350';
                } elseif ($value < 400) {
                    $valueClass = 'value-350-400';
                } elseif ($value < 450) {
                    $valueClass = 'value-400-450';
                } elseif ($value < 500) {
                    $valueClass = 'value-450-500';
                } else {
                    $valueClass = 'value-500-plus';
                }
            @endphp
            <div class="coupon-card {{ $valueClass }}">
                <div class="coupon-header">

                    <div class="teacher-name">
                        <h3>{{ get_theme_settings('jop_title') }}  {{ get_theme_settings('name') }}</h3>
                    </div>

                    <div class="logo-container">
                        @php
                            $logo = get_theme_settings('logo') ?: get_frontend_settings('dark_logo') ?: get_frontend_settings('light_logo');
                            $logoUrl = $logo ? get_image($logo) : null;
                        @endphp
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo">
                        @else
                            <span style="font-size: 30px;">🎫</span>
                        @endif
                    </div>
                </div>

                <div class="coupon-body">
                    <div class="coupon-value">
                        <span>{{ $coupon->value }}</span>
                        {{ get_phrase('EGP') }}
                    </div>
                    <div class="coupon-code">
                        {{ $coupon->code }}
                    </div>
                </div>

                <div class="coupon-footer">
                    <div class="coupon-type">
                        {{ get_phrase('Recharge Coupon') }}
                    </div>
                    @if($coupon->expiry)
                        <div class="expiry-date">
                            {{ get_phrase('Valid until') }}: {{ date('Y/m/d', strtotime($coupon->expiry)) }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // طباعة تلقائية عند فتح الصفحة (اختياري - يمكن إزالته)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // }
    </script>
</body>
</html>


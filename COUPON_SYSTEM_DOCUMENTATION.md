# نظام الكوبونات المطور

## نظرة عامة
تم تطوير نظام الكوبونات ليدعم 3 أنواع مختلفة من الكوبونات مع خيارات متقدمة للتحكم في الاستخدام والتعامل مع الرصيد المتبقي.

## أنواع الكوبونات

### 1️⃣ Recharge Coupon (كوبون الشحن)
- **الوظيفة**: يشحن رصيد المحفظة بمبلغ ثابت
- **الاستخدام**: يمكن استخدامه لزيادة رصيد المستخدم في المحفظة
- **المثال**: كوبون بقيمة 100 جنيه يشحن المحفظة بـ 100 جنيه

### 2️⃣ Discount Coupon (كوبون الخصم)
- **الوظيفة**: يخصم مبلغ أو نسبة من سعر الكورس
- **الاستخدام**: يستخدم أثناء عملية الشراء لتقليل السعر
- **الخيارات**:
  - خصم بنسبة مئوية (مثل 20%)
  - خصم بمبلغ ثابت (مثل 50 جنيه)
  - حد أدنى للشراء
  - حد أقصى للخصم

### 3️⃣ Payment Coupon (كوبون الدفع)
- **الوظيفة**: وسيلة دفع مستقلة لشراء كورس
- **الاستخدام**: يمكن استخدامه كوسيلة دفع كاملة أو جزئية
- **المثال**: كوبون بقيمة 500 جنيه لشراء كورس بـ 300 جنيه

## الخيارات المتقدمة

### تحديد الكورس
- **عام**: يمكن استخدام الكوبون لجميع الكورسات
- **محدد**: يمكن استخدام الكوبون لكورس معين فقط

### التعامل مع الرصيد المتبقي
- **Forfeit (فقدان)**: الرصيد المتبقي يُفقد
- **Reuse (إعادة استخدام)**: يمكن استخدام الكوبون مرة أخرى
- **Transfer to Wallet (تحويل للمحفظة)**: الرصيد المتبقي يُحول للمحفظة

### خيارات الاستخدام
- **إعادة الاستخدام من نفس المستخدم**: السماح للمستخدم باستخدام الكوبون أكثر من مرة
- **النقل لمستخدم آخر**: السماح بنقل الكوبون لمستخدم آخر
- **تحويل الرصيد للمحفظة**: تحويل الرصيد المتبقي للمحفظة تلقائياً

## قاعدة البيانات

### جدول coupons (محدث)
```sql
-- الحقول الجديدة المضافة:
type ENUM('recharge', 'discount', 'payment') DEFAULT 'discount'
value DECIMAL(10,2) NULL
course_id BIGINT UNSIGNED NULL
is_general BOOLEAN DEFAULT TRUE
balance_handling ENUM('forfeit', 'reuse', 'wallet') DEFAULT 'forfeit'
allow_reuse_same_user BOOLEAN DEFAULT FALSE
allow_transfer_to_other BOOLEAN DEFAULT FALSE
transfer_balance_to_wallet BOOLEAN DEFAULT FALSE
description TEXT NULL
minimum_amount DECIMAL(10,2) NULL
maximum_discount DECIMAL(10,2) NULL
```

## API Endpoints

### التحقق من صحة الكوبون
```
POST /api/coupons/validate
{
    "coupon_code": "DISCOUNT20",
    "course_id": 1 (اختياري)
}
```

### تطبيق كوبون الشحن
```
POST /api/coupons/recharge
{
    "coupon_code": "RECHARGE100"
}
```

### تطبيق كوبون الخصم
```
POST /api/coupons/discount
{
    "coupon_code": "DISCOUNT20",
    "course_id": 1,
    "course_price": 500
}
```

### تطبيق كوبون الدفع
```
POST /api/coupons/payment
{
    "coupon_code": "PAYMENT500",
    "course_id": 1,
    "course_price": 300
}
```

### نقل كوبون
```
POST /api/coupons/transfer
{
    "coupon_code": "PAYMENT500",
    "to_user_id": 2
}
```

### الحصول على كوبونات المستخدم
```
GET /api/coupons/user
```

## أمثلة على الاستخدام

### مثال 1: كوبون شحن
- **النوع**: Recharge Coupon
- **القيمة**: 200 جنيه
- **النتيجة**: زيادة رصيد المحفظة بـ 200 جنيه

### مثال 2: كوبون خصم
- **النوع**: Discount Coupon
- **الخصم**: 25%
- **الحد الأقصى**: 100 جنيه
- **سعر الكورس**: 500 جنيه
- **النتيجة**: خصم 100 جنيه (وليس 125 جنيه بسبب الحد الأقصى)

### مثال 3: كوبون دفع مع رصيد متبقي
- **النوع**: Payment Coupon
- **القيمة**: 500 جنيه
- **سعر الكورس**: 300 جنيه
- **خيار الرصيد المتبقي**: تحويل للمحفظة
- **النتيجة**: دفع 300 جنيه للكورس + 200 جنيه للمحفظة

## الملفات المحدثة

### Controllers
- `app/Http/Controllers/CouponController.php` - محدث لدعم الأنواع الجديدة
- `app/Http/Controllers/Api/CouponController.php` - جديد للـ API

### Models
- `app/Models/Coupon.php` - محدث مع دوال جديدة

### Services
- `app/Services/CouponService.php` - جديد للتعامل مع منطق الكوبونات

### Views
- `resources/views/admin/coupon/create.blade.php` - محدث
- `resources/views/admin/coupon/edit.blade.php` - محدث

### Database
- `database/migrations/2025_01_27_045052_enhance_coupons_table.php` - جديد

### Routes
- `routes/api.php` - محدث مع routes جديدة

## كيفية التشغيل

1. تشغيل Migration:
```bash
php artisan migrate
```

2. إنشاء كوبون جديد من لوحة الإدارة

3. استخدام API endpoints للتطبيق

## ملاحظات مهمة

- جميع API endpoints تتطلب مصادقة (auth:sanctum)
- الكوبونات لها تواريخ صلاحية وحدود استخدام
- يمكن تخصيص الكوبونات لمستخدمين معينين أو جميع المستخدمين
- النظام يدعم التعامل مع الرصيد المتبقي بطرق مختلفة
- يمكن نقل الكوبونات بين المستخدمين إذا تم تفعيل هذه الميزة

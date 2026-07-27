<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponLog;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * تطبيق كوبون شحن المحفظة
     */
    public function applyRechargeCoupon($couponCode, $userId)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (!$coupon || $coupon->type !== 'recharge') {
            return ['success' => false, 'message' => 'كوبون الشحن غير صالح'];
        }

        if (!$coupon->canBeUsedBy($userId)) {
            return ['success' => false, 'message' => 'لا يمكن استخدام هذا الكوبون'];
        }

        DB::beginTransaction();
        try {
            // شحن المحفظة
            $user = User::find($userId);
            $user->increment('wallet', $coupon->value);

            // تسجيل استخدام الكوبون
            CouponLog::create([
                'coupon_id' => $coupon->id,
                'user_id' => $userId,
            ]);

            DB::commit();
            return ['success' => true, 'message' => 'تم شحن المحفظة بنجاح', 'amount' => $coupon->value];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'حدث خطأ أثناء شحن المحفظة'];
        }
    }

    /**
     * تطبيق كوبون الخصم
     */
    public function applyDiscountCoupon($couponCode, $userId, $courseId, $coursePrice)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (!$coupon || $coupon->type !== 'discount') {
            return ['success' => false, 'message' => 'كوبون الخصم غير صالح'];
        }

        if (!$coupon->canBeUsedBy($userId, $courseId)) {
            return ['success' => false, 'message' => 'لا يمكن استخدام هذا الكوبون لهذا الكورس'];
        }

        // التحقق من الحد الأدنى للشراء
        if ($coupon->minimum_amount && $coursePrice < $coupon->minimum_amount) {
            return ['success' => false, 'message' => 'يجب أن يكون سعر الكورس أكبر من ' . $coupon->minimum_amount];
        }

        $discountAmount = $coupon->calculateDiscount($coursePrice);
        $finalPrice = $coursePrice - $discountAmount;

        return [
            'success' => true, 
            'message' => 'تم تطبيق الخصم بنجاح',
            'original_price' => $coursePrice,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'coupon_id' => $coupon->id
        ];
    }

    /**
     * تطبيق كوبون الدفع المباشر
     */
    public function applyPaymentCoupon($couponCode, $userId, $courseId, $coursePrice)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (!$coupon || $coupon->type !== 'payment') {
            return ['success' => false, 'message' => 'كوبون الدفع غير صالح'];
        }

        if (!$coupon->canBeUsedBy($userId, $courseId)) {
            return ['success' => false, 'message' => 'لا يمكن استخدام هذا الكوبون لهذا الكورس'];
        }

        if ($coupon->value < $coursePrice) {
            // إذا كان قيمة الكوبون أقل من سعر الكورس
            if ($coupon->balance_handling === 'forfeit') {
                return ['success' => false, 'message' => 'قيمة الكوبون لا تكفي لشراء هذا الكورس'];
            } elseif ($coupon->balance_handling === 'wallet' && $coupon->transfer_balance_to_wallet) {
                // تحويل الرصيد المتبقي للمحفظة
                $remainingBalance = $coupon->value;
                $user = User::find($userId);
                $user->increment('wallet', $remainingBalance);
                
                return [
                    'success' => true,
                    'message' => 'تم تحويل الرصيد المتبقي للمحفظة',
                    'remaining_balance' => $remainingBalance,
                    'coupon_id' => $coupon->id
                ];
            }
        }

        DB::beginTransaction();
        try {
            // تسجيل استخدام الكوبون
            CouponLog::create([
                'coupon_id' => $coupon->id,
                'user_id' => $userId,
            ]);

            // إذا كان هناك رصيد متبقي وتم تفعيل التحويل للمحفظة
            if ($coupon->value > $coursePrice && $coupon->canTransferToWallet()) {
                $remainingBalance = $coupon->value - $coursePrice;
                $user = User::find($userId);
                $user->increment('wallet', $remainingBalance);
            }

            DB::commit();
            return [
                'success' => true, 
                'message' => 'تم الدفع بالكوبون بنجاح',
                'paid_amount' => min($coupon->value, $coursePrice),
                'remaining_balance' => $coupon->value > $coursePrice ? $coupon->value - $coursePrice : 0,
                'coupon_id' => $coupon->id
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'حدث خطأ أثناء الدفع بالكوبون'];
        }
    }

    /**
     * التحقق من صحة الكوبون
     */
    public function validateCoupon($couponCode, $userId, $courseId = null)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (!$coupon) {
            return ['valid' => false, 'message' => 'الكوبون غير موجود'];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => 'الكوبون منتهي الصلاحية أو غير نشط'];
        }

        if (!$coupon->canBeUsedBy($userId, $courseId)) {
            return ['valid' => false, 'message' => 'لا يمكن استخدام هذا الكوبون'];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'message' => 'الكوبون صالح للاستخدام'
        ];
    }

    /**
     * الحصول على معلومات الكوبون
     */
    public function getCouponInfo($couponCode)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (!$coupon) {
            return null;
        }

        return [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $coupon->discount,
            'description' => $coupon->description,
            'is_valid' => $coupon->isValid(),
            'expiry' => $coupon->expiry,
            'usage_count' => $coupon->coupons_log()->count(),
            'limit' => $coupon->limit,
            'course' => $coupon->course ? $coupon->course->title : null,
            'is_general' => $coupon->is_general,
            'balance_handling' => $coupon->balance_handling,
            'allow_reuse_same_user' => $coupon->allow_reuse_same_user,
            'allow_transfer_to_other' => $coupon->allow_transfer_to_other,
            'transfer_balance_to_wallet' => $coupon->transfer_balance_to_wallet,
        ];
    }

    /**
     * نقل كوبون من مستخدم لآخر
     */
    public function transferCoupon($couponCode, $fromUserId, $toUserId)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (!$coupon) {
            return ['success' => false, 'message' => 'الكوبون غير موجود'];
        }

        if (!$coupon->allow_transfer_to_other) {
            return ['success' => false, 'message' => 'هذا الكوبون لا يمكن نقله لمستخدم آخر'];
        }

        // التحقق من أن المستخدم الأصلي استخدم الكوبون
        $couponLog = CouponLog::where('coupon_id', $coupon->id)
                              ->where('user_id', $fromUserId)
                              ->first();

        if (!$couponLog) {
            return ['success' => false, 'message' => 'لم يتم استخدام هذا الكوبون من قبل المستخدم الأصلي'];
        }

        // التحقق من أن المستخدم الجديد يمكنه استخدام الكوبون
        if (!$coupon->canBeUsedBy($toUserId)) {
            return ['success' => false, 'message' => 'لا يمكن للمستخدم الجديد استخدام هذا الكوبون'];
        }

        DB::beginTransaction();
        try {
            // حذف السجل القديم
            $couponLog->delete();

            // إنشاء سجل جديد للمستخدم الجديد
            CouponLog::create([
                'coupon_id' => $coupon->id,
                'user_id' => $toUserId,
            ]);

            DB::commit();
            return ['success' => true, 'message' => 'تم نقل الكوبون بنجاح'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'حدث خطأ أثناء نقل الكوبون'];
        }
    }
}

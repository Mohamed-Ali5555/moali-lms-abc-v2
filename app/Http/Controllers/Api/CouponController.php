<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * التحقق من صحة الكوبون
     */
    public function validateCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->couponService->validateCoupon(
            $request->coupon_code,
            Auth::id(),
            $request->course_id
        );

        return response()->json($result);
    }

    /**
     * الحصول على معلومات الكوبون
     */
    public function getCouponInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 400);
        }

        $couponInfo = $this->couponService->getCouponInfo($request->coupon_code);

        if (!$couponInfo) {
            return response()->json([
                'success' => false,
                'message' => 'الكوبون غير موجود'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $couponInfo
        ]);
    }

    /**
     * تطبيق كوبون الشحن
     */
    public function applyRechargeCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->couponService->applyRechargeCoupon(
            $request->coupon_code,
            Auth::id()
        );

        return response()->json($result);
    }

    /**
     * تطبيق كوبون الخصم
     */
    public function applyDiscountCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'course_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->couponService->applyDiscountCoupon(
            $request->coupon_code,
            Auth::id(),
            $request->course_id,
            $request->course_price
        );

        return response()->json($result);
    }

    /**
     * تطبيق كوبون الدفع المباشر
     */
    public function applyPaymentCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'course_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->couponService->applyPaymentCoupon(
            $request->coupon_code,
            Auth::id(),
            $request->course_id,
            $request->course_price
        );

        return response()->json($result);
    }

    /**
     * نقل كوبون من مستخدم لآخر
     */
    public function transferCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
            'to_user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->couponService->transferCoupon(
            $request->coupon_code,
            Auth::id(),
            $request->to_user_id
        );

        return response()->json($result);
    }

    /**
     * الحصول على كوبونات المستخدم
     */
    public function getUserCoupons()
    {
        $user = Auth::user();
        $coupons = \App\Models\Coupon::where('status', 1)
            ->where('expiry', '>', now())
            ->where('start_date', '<=', now())
            ->where(function($query) use ($user) {
                $query->whereJsonContains('user_id', 0)
                      ->orWhereJsonContains('user_id', $user->id);
            })
            ->with(['course'])
            ->get()
            ->map(function($coupon) use ($user) {
                $canUse = $coupon->canBeUsedBy($user->id);
                $usageCount = $coupon->coupons_log()->where('user_id', $user->id)->count();
                
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'title' => $coupon->title,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'discount' => $coupon->discount,
                    'description' => $coupon->description,
                    'expiry' => $coupon->expiry,
                    'course' => $coupon->course ? $coupon->course->title : null,
                    'is_general' => $coupon->is_general,
                    'can_use' => $canUse,
                    'usage_count' => $usageCount,
                    'allow_reuse' => $coupon->allow_reuse_same_user,
                    'allow_transfer' => $coupon->allow_transfer_to_other,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $coupons
        ]);
    }
}

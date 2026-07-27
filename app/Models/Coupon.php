<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Coupon extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    protected $casts = [
        'expiry' => 'datetime',
        'start_date' => 'datetime',
        'is_general' => 'boolean',
        'discount_type' => 'string',
    ];

    
    public function coupons_log(){
        return $this->hasMany(CouponLog::class);
    }

    public function payments(){
        return $this->hasMany(OfflinePayment::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function addedBy(){
        return $this->belongsTo(User::class, 'added_by');
    }

    // تحديد ما إذا كان الكوبون صالح للاستخدام
    public function isValid(){
        if($this->status == 0){
            return false;
        }
        if($this->expiry && $this->expiry < now()){
            return false;
        }
        if($this->start_date && $this->start_date > now()){
            return false;
        }

        if($this->type != 'payment'){
            if($this->coupons_log()->where('user_id', auth()->user()->id)->where('coupon_id', $this->id)->exists()){
                return false;
            }

            if($this->limit && $this->limit > 0 && $this->coupons_log()->count() >= $this->limit){
                return false;
            }
        }else{
            if ($this->is_partially_used && $this->remaining_value <= 0) {
                return false;
            }
            $balance_handling = json_decode($this->balance_handling, true) ?? [];
            if($this->used_by && $this->used_by == auth()->id()){
                if (!in_array('reuse', $balance_handling)) {
                    return false;
                }
            }
            if($this->used_by && $this->used_by != auth()->id()){
                if (!in_array('reuse_others', $balance_handling)) {
                    return false;
                }
            }
        }

        return true;
    }

    // تحديد ما إذا كان الكوبون يمكن استخدامه لكورس معين
    public function canBeUsedForCourse($courseId){
        return $this->is_general || $this->course_id == $courseId;
    }

    public function minimumAmount($coursePrice){
        if($this->minimum_amount && $coursePrice < $this->minimum_amount){
            return false;
        }
        return true;
    }

    // حساب قيمة الخصم
    public function calculateDiscount($coursePrice){
        if($this->type == 'discount'){
            $discountAmount = ($coursePrice * $this->value) / 100;
            if($this->maximum_discount && $discountAmount > $this->maximum_discount){
                $discountAmount = $this->maximum_discount;
            }
            return $discountAmount;
        }
        return 0;
    }

    public function canBeUsedForUser($userId){
        if(in_array(0, json_decode($this->user_id, true)) || in_array($userId, json_decode($this->user_id, true))){
            return true;
        }
        return false;
    }

    public function checkCouponFirstPurchase($userId){
        if($this->discount_type == "first_purchase"){
            if(CouponLog::where('user_id', $userId)->exists()){
                return false;
            }
            return true;
        }
        return true;
    }

    public function validateForDiscount($user, $totalPrice): array
    {
        // 1️⃣ تحقق من صلاحية الكوبون
        if ($this->type != 'discount') {
            return [false, get_phrase('الكوبون غير صالح.')];
        }

        if (!$this->isValid()) {
            return [false, get_phrase('الكوبون غير صالح.')];
        }

        // 2️⃣ تحقق من أن المستخدم مسموح له باستخدامه
        if (!$this->canBeUsedForUser($user)) {
            return [false, get_phrase('غير مسموح لك باستخدام هذا الكوبون.')];
        }

        // 3️⃣ تحقق من شرط أول عملية شراء
        if (!$this->checkCouponFirstPurchase($user)) {
            return [false, get_phrase('هذا الكوبون متاح لأول عملية شراء فقط.')];
        }

        // 4️⃣ تحقق من الحد الأدنى للفاتورة
        if (!$this->minimumAmount($totalPrice)) {
            return [false, get_phrase('يجب أن تكون الفاتورة أكبر من ' . $this->minimum_amount)];
        }

        // ✅ كل شيء تمام
        return [true, null];
    }

}

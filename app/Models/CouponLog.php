<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function coupons(){
        return $this->belongsTo(Coupon::class,'coupon_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

   
}

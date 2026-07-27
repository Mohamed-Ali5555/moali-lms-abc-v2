<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomEmailVerificationNotification;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail

{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'goverment',
        'category',
        'parent_phone',
        'national_id',
        'role',
        'status',
        'email_verified_at',
        'address',
        'number_devices',
        'national_image',
        'gender',
        'wallet',
        'photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomEmailVerificationNotification());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function get_category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category','id');
    }

    // public function enrollment(){
    //     return $this->belongsToMany()
    // }

    public function enrollments() {
        return $this->hasMany(\App\Models\Enrollments::class);
    }
    public function courses() {
        return $this->hasMany(\App\Models\Course::class);
    }
    public function payments(){
        return $this->hasMany(OfflinePayment::class);
    }

    // public function couponLogs(){
    //     return $this->belongsToMany(CouponLog::class,'coupon_logs','user_id','coupon_id');
    // }
    public function couponLogs()
    {
        return $this->hasMany(CouponLog::class, 'user_id');
    }
}

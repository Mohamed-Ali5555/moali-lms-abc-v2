<?php

namespace Modules\Wallet\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Wallet\Database\Factories\WalletLogFactory;

class WalletLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['balance','type','student_id','note','payment_id','uuid','wallet_category_id','added_by','status'];

    // protected static function newFactory(): WalletLogFactory
    // {
    //     // return WalletLogFactory::new();
    // }

    public function WalletCategory()
    {
        return $this->belongsTo(WalletLogCategory::class);
    }

    public function student()
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }

    public function added()
    {
        return $this->belongsTo(\App\Models\User::class, 'added_by');
    }
}

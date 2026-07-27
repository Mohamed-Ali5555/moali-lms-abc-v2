<?php

namespace Modules\Wallet\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Wallet\Database\Factories\WalletLogCategoryFactory;

class WalletLogCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['balance','type','added_by','status','note'];

    // protected static function newFactory(): WalletLogCategoryFactory
    // {
    //     // return WalletLogCategoryFactory::new();
    // }

    public function WalletStudentLog(){
        return $this->hasMany(WalletLog::class,'wallet_category_id');
    }


    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }
    
}

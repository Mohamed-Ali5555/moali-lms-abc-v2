<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InvoceItems;
class Payment_history extends Model
{
    use HasFactory;
    public function items(){
        return $this->hasMany(InvoceItems::class);
    }
      public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
}

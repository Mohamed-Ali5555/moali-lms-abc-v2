<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoceItems extends Model
{
    protected $table = 'invoce_items';
    protected $guarded = [];
    public function item() {
        return $this->morphTo(null, 'productable_type', 'productable_id');
    }
}

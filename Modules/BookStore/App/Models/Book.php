<?php

namespace Modules\BookStore\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\BookStore\Database\Factories\BooksFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Models\InvoceItems;
use App\Models\Payment_history;
class Book extends Model
{
    use HasFactory;
    protected $table    = 'books';
    protected $fillable = [
        'category_id',
        'added_by',
        'status',
        'price',
        'title',
        'thumbnail',
        'slug',
        'keywords',
        'disc',
        'sort',
        'discount_price',
        'if_discount',
        'file_type',
        'file_path',
        'file_url',
    ];


        public function getContentUrlAttribute(): ?string
    {
        if ($this->file_type === 'link' && !empty($this->file_url)) {
            return $this->file_url;
        }

        if ($this->file_type === 'file' && !empty($this->file_path)) {
            return asset($this->file_path);
        }

        return null;
    }

   public function hasReadableContent(): bool
    {
        return !empty($this->content_url);
    }
   public function creator() {
        return $this->belongsTo(\App\Models\User::class, 'added_by', 'id');
    }



    public function category(){
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function invoice(): MorphToMany
    {
        return $this->morphToMany(Payment_history::class, 'productable', 'invoce_items', 'productable_id', 'payment_history_id')->withPivot([
            'amount'
        ]);;
    }
}

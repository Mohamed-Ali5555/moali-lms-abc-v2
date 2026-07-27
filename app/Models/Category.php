<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BankQuestions\App\Models\BankQuestionsCategory;
class Category extends Model
{
    use HasFactory;



    public function childs() {
        return $this->hasMany(Category::class,'parent_id');
    }
    public function parent() {
        return $this->belongsTo(Category::class,'parent_id');
    }
    public function courses() {
        return $this->hasMany(Course::class);
    }

    public function bank_category() {
        return $this->hasMany(BankQuestionsCategory::class,'category_id','id');
    }

}

<?php

namespace Modules\BankQuestions\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\BankQuestions\Database\Factories\BankQuestionsCategoryFactory;
use App\Models\Category;
class BankQuestionsCategory extends Model
{
    use HasFactory;
    protected $table   = 'bank_quizs_categories';
    protected $guarded = ['id'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function quizs() {
        return $this->hasMany(BankQuizs::class,'category_id','id');
    }

    public function questions() {
        return $this->hasMany(BankQuestions::class,'category_id','id');
    }
}

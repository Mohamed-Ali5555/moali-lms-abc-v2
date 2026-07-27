<?php

namespace Modules\BankQuestions\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\BankQuestions\Database\Factories\BankQuestionsFactory;

class BankQuestions extends Model
{
    use HasFactory;
    protected $table   = 'bank_questions';
    protected $guarded = ['id'];

    public function quizs() {
        return $this->belongsToMany(BankQuizs::class,'quize_quiestions','question_id','quiz_id');
    }

    public function category()
    {
        return $this->belongsTo(BankQuestionsCategory::class);
    }

}

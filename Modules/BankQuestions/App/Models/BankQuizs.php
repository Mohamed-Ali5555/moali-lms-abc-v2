<?php

namespace Modules\BankQuestions\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\BankQuestions\Database\Factories\BankQuizsFactory;

class BankQuizs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table   = 'bank_quizs';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(BankQuestionsCategory::class);
    }

    public function questions() {
        return $this->belongsToMany(BankQuestions::class,'quize_quiestions','quiz_id','question_id');
    }
}

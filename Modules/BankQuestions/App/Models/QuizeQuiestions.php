<?php

namespace Modules\BankQuestions\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizeQuiestions extends Model
{
    use HasFactory;

    protected $guarded= ['id'];
    protected $table = 'quize_quiestions';

}

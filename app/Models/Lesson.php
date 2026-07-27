<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lesson extends Model
{
    use HasFactory;

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class,'quiz_id','id');
    }

    public function deliverables(){
        return $this->hasMany(QuizSubmission::class,'quiz_id','id');
    }

    protected $fillable = [
        'title',
        'user_id',
        'course_id',
        'section_id',
        'lesson_type',
        'duration',
        'lesson_src',
        'attachment',
        'attachment_type',
        'video_type',
        'thumbnail',
        'is_free',
        'sort',
        'description',
        'summary',
        'status',
        'total_mark',
        'pass_mark',
        'retake',
        'show_answer',
        'bank_id',
        'type',
        'start_time',
        'end_time'
    ];

    public function scopeActive($query, $dateTime = null)
    {
        if(!auth()->check()){
            return $query;

        }
        if(auth()->user()->role != 'student'){
            return $query;
        }

        $dateTime = $dateTime ?? Carbon::now();

        return $query->where(function ($q) use ($dateTime) {
            $q->where(function ($q2) use ($dateTime) {
                $q2->where('start_time', '<=', $dateTime)
                   ->where(function ($q3) use ($dateTime) {
                       $q3->where('end_time', '>=', $dateTime)
                          ->orWhereNull('end_time');
                   });
            })
            ->orWhere(function ($q2) {
                $q2->whereNull('start_time')
                   ->whereNull('end_time');
            });
        });
    }
}

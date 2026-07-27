<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Section extends Model
{
    use HasFactory;


    public function course()
    {
        return $this->belongsTo(Course::class);
    }



    public function allLesson()
    {


    $dateTime =  Carbon::now();

    return $this->hasMany(Lesson::class)->where(function ($q) use ($dateTime) {
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
    })->orderBy('sort', 'asc');


}
}

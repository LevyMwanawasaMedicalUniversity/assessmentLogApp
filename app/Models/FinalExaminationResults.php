<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalExaminationResults extends Model
{
    use HasFactory;

    protected $primaryKey = 'final_examination_results_id';    

    protected $fillable = [
        'student_id',
        'course_id',
        'final_examinations_id',
        'status',
        'course_id',
        'academic_year',
        'cas_score',
        'basic_information_id',
        'delivery_mode',
        'study_id',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MismatchedSenateResults extends Model
{
    protected $table = 'mismatched_senate_results';
    protected $fillable = [
        'student_id',
        'academic_year',
        'course_code',
        'senate_ca_score',
        'edurole_ca_score',
        'senate_exam_score',
        'edurole_exam_score',
        'senate_grade',
        'edurole_grade',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentsContinousAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'ca_marks',
        'ca_type',
        'academic_year',
    ];
}

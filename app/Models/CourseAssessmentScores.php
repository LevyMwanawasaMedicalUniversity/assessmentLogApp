<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAssessmentScores extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_assessment_id',
        'student_id',
        'score',
        'grade',
        'status',
        'created_by',
        'updated_by'
    ]; 
}

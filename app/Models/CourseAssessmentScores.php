<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class CourseAssessmentScores extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $primaryKey = 'course_assessment_scores_id';

    protected $fillable = [
        'course_assessment_id',
        'student_id',
        'course_code',
        'cas_score',
        'created_by',
        'updated_by'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class StudentsContinousAssessment extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $primaryKey = 'students_continous_assessment_id';

    protected $fillable = [
        'student_id',
        'course_id',
        'ca_marks',
        'ca_type',
        'academic_year',
        'course_assessment_id',
        'delivery_mode'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class CourseAssessment extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $primaryKey = 'course_assessments_id';

    protected $fillable = [
        'course_id',
        'description',
        'ca_type',
        'basic_information_id',
        'academic_year'
    ];
}

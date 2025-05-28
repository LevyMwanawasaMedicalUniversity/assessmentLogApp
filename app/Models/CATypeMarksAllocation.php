<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class CATypeMarksAllocation extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'user_id',
        'course_id',
        'assessment_type_id',
        'total_marks',
        'delivery_mode',
        'study_id',
        'component_id',
        'assessment_count'
    ];
}

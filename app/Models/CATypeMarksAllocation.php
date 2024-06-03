<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CATypeMarksAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'assessment_type_id',
        'total_marks'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseComponentAllocation extends Model
{
    use HasFactory;

    protected $primaryKey = 'course_component_allocations_id';

    protected $fillable = [
        'user_id',
        'course_id',
        'course_component_id',
        'study_id',
        'delivery_mode',
        'academic_year'
    ];
}

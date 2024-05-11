<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'ca_type',
        'basic_information_id',
        'academic_year'
    ];
}

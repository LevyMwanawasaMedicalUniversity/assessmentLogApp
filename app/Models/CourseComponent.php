<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseComponent extends Model
{
    use HasFactory;

    protected $primaryKey = 'course_components_id';

    protected $fillable = [
        'component_name'
    ];
}

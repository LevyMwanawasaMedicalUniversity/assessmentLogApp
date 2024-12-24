<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaAndExamUpload extends Model
{
    use HasFactory;
    protected $primaryKey = 'ca_and_exam_uploads_id';

    protected $fillable = [
        'student_id',
        'grade',
        'course_code',
        'delivery_mode',
        'study_id',
        'type_of_exam',
        'status',
        'course_id',
        'basic_information_id',
        'academic_year',
        'ca',
        'exam',
    ];


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'assesment_type_name'
    ];
}

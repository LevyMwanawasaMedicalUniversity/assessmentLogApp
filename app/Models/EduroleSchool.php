<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EduroleSchool extends Model
{
    use HasFactory;

    protected $connection = 'edurole_database';
    protected $table = 'schools';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisReportsStudent extends Model
{
    use HasFactory;

    protected $connection = 'sis_reports_database';
    protected $table = 'students';
}

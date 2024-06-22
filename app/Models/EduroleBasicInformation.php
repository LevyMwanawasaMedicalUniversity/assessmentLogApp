<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class EduroleBasicInformation extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $connection = 'edurole_database';
    protected $table = 'basic-information';

    // Define any fillable attributes if needed
    protected $fillable = [
        // Add your fillable attributes here
    ];
}

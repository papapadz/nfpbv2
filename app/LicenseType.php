<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
 
    protected $fillable = [
        'type',
        'government',
        'remarks',
    
    ];
    
}

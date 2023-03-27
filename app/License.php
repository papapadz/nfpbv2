<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'employee_id',
        'license_type_id',
        'license_no',
        'date_issued',
        'date_expired',
    ];
    
    
    protected $dates = [
        'date_issued',
        'date_expired'
    
    ];

    public function type() {
        return $this->hasOne(LicenseType::class,'id','license_type_id');
    }
}

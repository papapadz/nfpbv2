<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    protected $fillable = [
        'employee_id',
        'salary_id',
        'status',
        'date_hired',
        'date_expired',
        'company',
        'is_active'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'employee_id';
    protected $casts = ['employee_id'=>'text']; 

    protected $fillable = [
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        //'position',
        'birthdate',
        'gender',
        'civil_stat',
        'address',
        //'date_hired',
        'date_expired',
        'email',
        'citizenship',
        'height',
        'weight',
        'bloodType'
    ];
}

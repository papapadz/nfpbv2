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
        'bloodType',
        'img'
    ];

    public function education() {
        return $this->hasMany(Education::class,'employee_id','employee_id');
    }

    public function employment() {
        return $this->hasMany(Employment::class,'employee_id','employee_id')->with('salary');
    }

    public function family() {
        return $this->hasMany(Family::class,'employee_id','employee_id');
    }

    public function licenses() {
        return $this->hasMany(License::class,'employee_id','employee_id')->with('type');
    }

    public function avatar() {
        return $this->hasOne(FileHandler::class,'id','img');
    }
}

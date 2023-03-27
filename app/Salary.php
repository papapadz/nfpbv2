<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'salary';

    protected $fillable = [
        'position_id',
        'amount',
        'date_effective',
        'monthly'
    ];
    
    
    protected $dates = [
        'date_effective'
    ];

    public function position() {
        return $this->hasOne(Position::class,'id','position_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $primaryKey = 'rateId';
	 protected $fillable = [
        'establishmentId',
        'tolerance',
        'charge_1',
        'is_double',
        'charge_2',
        'subsequent',
        'from',
        'to',
        'one_time_payment',
        'userId'
    ];
    //
    public $timestamps = false;
}

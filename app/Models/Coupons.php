<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
	protected $primaryKey = 'couponId';
	 protected $fillable = [
        'couponName',
        'couponCode',
        'couponType',
        'discount',
        'description',
        'user_id',
        'date',
        'expDate',
				'forUser'

    ];
    //
    public $timestamps = false;
}

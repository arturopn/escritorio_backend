<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsedCoupons extends Model
{
  protected $primaryKey = 'usedCouponsId';
   protected $fillable = [
        'couponId',
        'userId',
        'date'
    ];
    //
    public $timestamps = false;
}

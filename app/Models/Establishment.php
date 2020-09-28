<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
	protected $primaryKey = 'establishmentId';

	    protected $fillable = [
        'location',
        'address',
        'name',
        'logo',
        'capacity',
        'discount',
        'clabe',
        'ownerId'
    ];
    //
}

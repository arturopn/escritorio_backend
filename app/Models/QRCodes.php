<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QRCodes extends Model
{
	protected $table = 'qr_codes';
	protected $primaryKey = 'qrId';
    protected $fillable = [
        'image',
        'establishmentId',
        'door',
        'qrToken',
        'inUse',
        'location',
        'userId',
        'qrcode',
        'type'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitations extends Model
{
	
	 protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'initation_id',
        'account_Id',
        'sender_Id',
        'email',
        'message',
		    'role_Id'

    ];
    //
    public $timestamps = false;
}

<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UsersLawyerChat
 * @package App\Models
 * @version January 26, 2021, 7:08 pm UTC
 *
 * @property integer $user_id
 * @property string $lawyer_id
 * @property string $firebase_chatId
 * @property string $firebase_userId
 * @property string $firebase_lawyerId
 */
class UsersLawyerChat extends Model
{
    use SoftDeletes;

    public $table = 'users_lawyer_chats';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'user_id',
        'lawyer_id',
        'firebase_chatId',
        'firebase_userId',
        'firebase_lawyerId',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'lawyer_id' => 'string',
        'firebase_chatId' => 'string',
        'firebase_userId' => 'string',
        'firebase_lawyerId' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

}

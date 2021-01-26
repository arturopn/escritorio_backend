<?php

namespace App\Repositories;

use App\Models\UsersLawyerChat;
use App\Repositories\BaseRepository;

/**
 * Class UsersLawyerChatRepository
 * @package App\Repositories
 * @version January 26, 2021, 7:08 pm UTC
*/

class UsersLawyerChatRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'lawyer_id',
        'firebase_chatId',
        'firebase_userId',
        'firebase_lawyerId'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UsersLawyerChat::class;
    }
}

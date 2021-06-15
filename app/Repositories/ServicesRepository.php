<?php

namespace App\Repositories;

use App\Models\Services;
use App\Repositories\BaseRepository;

/**
 * Class ServicesRepository
 * @package App\Repositories
 * @version October 21, 2020, 3:19 pm UTC
*/

class ServicesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'price',
        'status'
    ];

    protected $orderable = [
        'price'
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
        return Services::class;
    }
}

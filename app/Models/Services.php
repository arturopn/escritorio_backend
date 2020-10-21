<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Services
 * @package App\Models
 * @version October 21, 2020, 3:19 pm UTC
 *
 * @property string $name
 * @property string $description
 * @property number $price
 * @property boolean $status
 */
class Services extends Model
{
    use SoftDeletes;

    public $table = 'services';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'description',
        'price',
        'image',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'price' => 'decimal:2',
        'image' => 'string',
        'status' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}

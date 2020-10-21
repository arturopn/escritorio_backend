<?php

namespace App\Http\Requests;

use App\Models\Services;
use InfyOm\Generator\Request\APIRequest;

class UpdateServicesAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = Services::$rules;
        
        return $rules;
    }
}

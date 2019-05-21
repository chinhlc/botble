<?php

namespace Botble\Api\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ApiClientRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author Sang Nguyen
     */
    public function rules()
    {
        return [
            'name' => 'required|max:120|min:2',
        ];
    }
}

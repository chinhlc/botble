<?php

namespace Botble\Block\Http\Requests;

use Botble\Support\Http\Requests\Request;

class BlockRequest extends Request
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
            'name' => 'required|max:120',
            'alias' => 'required|max:120',
        ];
    }
}

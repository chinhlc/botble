<?php

namespace Botble\Gallery\Http\Requests;

use Botble\Support\Http\Requests\Request;

class GalleryRequest extends Request
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
            'description' => 'required|max:400',
            'order' => 'required|integer|min:0',
        ];
    }
}

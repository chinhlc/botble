<?php

namespace Botble\Blog\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CategoryRequest extends Request
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
            'description' => 'max:400',
            'slug' => 'required',
            'order' => 'required|integer|min:0',
        ];
    }
}

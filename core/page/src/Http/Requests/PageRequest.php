<?php

namespace Botble\Page\Http\Requests;

use Botble\Support\Http\Requests\Request;

class PageRequest extends Request
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
            'content' => 'required',
            'slug' => 'required',
        ];
    }
}

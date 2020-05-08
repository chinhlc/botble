<?php

namespace Botble\Base\Forms;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{
    /**
     * @param string $formClass
     * @param array $options
     * @param array $data
     * @return FormAbstract|Form
     */
    public function create($formClass, array $options = [], array $data = [])
    {
        return parent::create($formClass, $options, $data);
    }
}

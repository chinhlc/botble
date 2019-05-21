<?php

namespace Botble\ACL\Forms;

use Botble\Api\Http\Requests\ApiClientRequest;
use Botble\Base\Forms\FormAbstract;

class ApiClientForm extends FormAbstract
{
    /**
     * @return mixed|void
     */
    public function buildForm()
    {
        $this
            ->setFormOption('template', 'core.base::forms.form-modal')
            ->setFormOption('class', 'form-xs')
            ->setValidatorClass(ApiClientRequest::class)
            ->add('name', 'text', [
                'label' => trans('plugins.api::api.name'),
                'label_attr' => [
                    'class' => 'control-label required',
                ],
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('close', 'button', [
                'label' => trans('plugins.api::api.cancel'),
                'attr' => [
                    'class' => 'btn btn-warning',
                    'data-fancybox-close' => true,
                ],
            ])
            ->add('submit', 'submit', [
                'label' => trans('plugins.api::api.save'),
                'attr' => [
                    'class' => 'btn btn-info float-right',
                ],
            ]);
    }
}
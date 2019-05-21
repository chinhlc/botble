<?php

namespace Botble\Menu\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Menu\Http\Requests\MenuRequest;

class MenuForm extends FormAbstract
{

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function buildForm()
    {
        $this
            ->setModuleName(MENU_MODULE_SCREEN_NAME)
            ->setFormOption('class', 'form-save-menu')
            ->setValidatorClass(MenuRequest::class)
            ->add('name', 'text', [
                'label' => $this->getModel() ? trans('core.menu::menu.key_name', ['key' => $this->getModel()->slug]) : trans('core.base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core.base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->addMetaBoxes([
                'structure' => [
                    'wrap' => false,
                    'content' => view('core.menu::menu-structure', ['menu' => $this->getModel()])->render(),
                ],
            ]);
    }
}
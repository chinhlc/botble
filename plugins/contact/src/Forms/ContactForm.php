<?php

namespace Botble\Contact\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Contact\Http\Requests\EditContactRequest;

class ContactForm extends FormAbstract
{

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function buildForm()
    {
        $this
            ->setModuleName(CONTACT_MODULE_SCREEN_NAME)
            ->setValidatorClass(EditContactRequest::class)
            ->add('is_read', 'checkbox', [
                'label' => trans('plugins.contact::contact.form.is_read'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'hrv-checkbox',
                ],
                'value' => 1,
                'checked' => $this->getModel() ? $this->getModel()->is_read == 1 : false,
            ])
            ->setBreakFieldPoint('is_read')
            ->addMetaBoxes([
                'information' => [
                    'title' => trans('plugins.contact::contact.contact_information'),
                    'content' => view('plugins.contact::contact-info', ['contact' => $this->getModel()])->render(),
                    'attributes' => [
                        'style' => 'margin-top: 0',
                    ],
                ],
            ]);
    }
}

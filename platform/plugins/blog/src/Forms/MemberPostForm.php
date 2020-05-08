<?php

namespace Botble\Blog\Forms;

use Assets;
use Botble\Blog\Models\Post;
use Botble\Member\Forms\Fields\CustomEditorField;
use Botble\Member\Forms\Fields\CustomImageField;
use Botble\Blog\Http\Requests\MemberPostRequest;

class MemberPostForm extends PostForm
{

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function buildForm()
    {
        parent::buildForm();

        Assets::addScriptsDirectly('vendor/core/libraries/tinymce/tinymce.min.js')
            ->addScripts(['bootstrap-tagsinput', 'typeahead'])
            ->addStyles(['bootstrap-tagsinput'])
            ->addScriptsDirectly('vendor/core/js/tags.js');

        if (!$this->formHelper->hasCustomField('customEditor')) {
            $this->formHelper->addCustomField('customEditor', CustomEditorField::class);
        }

        if (!$this->formHelper->hasCustomField('customImage')) {
            $this->formHelper->addCustomField('customImage', CustomImageField::class);
        }

        $tags = null;

        if ($this->getModel()) {
            $tags = $this->getModel()->tags()->pluck('name')->all();
            $tags = implode(',', $tags);
        }

        $this->setModuleName(MEMBER_POST_MODULE_SCREEN_NAME)
            ->setupModel(new Post)
            ->setFormOption('template', 'plugins/member::forms.base')
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(MemberPostRequest::class)
            ->setActionButtons(view('plugins/member::forms.actions')->render())
            ->remove('status')
            ->remove('is_featured')
            ->remove('content')
            ->remove('image')
            ->addAfter('description', 'content', 'customEditor', [
                'label'      => trans('core/base::forms.content'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->addBefore('tag', 'image', 'customImage', [
                'label'      => trans('core/base::forms.image'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->modify('tag', 'text', [
                'label'      => trans('plugins/blog::posts.form.tags'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'id'          => 'tags',
                    'data-role'   => 'tagsinput',
                    'placeholder' => trans('plugins/blog::posts.form.tags_placeholder'),
                ],
                'value'      => $tags,
                'help_block' => [
                    'text' => 'Tag route',
                    'tag'  => 'div',
                    'attr' => [
                        'data-tag-route' => route('public.member.tags.all'),
                        'class'          => 'hidden',
                    ],
                ],
            ], true)
            ->setBreakFieldPoint('format_type');
    }
}

<?php

namespace Botble\Blog\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Blog\Forms\Fields\CategoryMultiField;
use Botble\Blog\Http\Requests\PostRequest;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;

class PostForm extends FormAbstract
{

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function buildForm()
    {
        $selected_categories = [];
        if ($this->getModel()) {
            $selected_categories = $this->getModel()->categories()->pluck('category_id')->all();
        }

        if (empty($selected_categories)) {
            $selected_categories = app(CategoryInterface::class)->getModel()->where('is_default', 1)->pluck('id')->all();
        }

        $tags = null;

        if ($this->getModel()) {
            $tags = $this->getModel()->tags()->pluck('name')->all();
            $tags = implode(',', $tags);
        }

        $this->addCustomField('categoryMulti', CategoryMultiField::class);

        $this
            ->setModuleName(POST_MODULE_SCREEN_NAME)
            ->setValidatorClass(PostRequest::class)
            ->withCustomFields()
            ->hasTabs()
            ->add('name', 'text', [
                'label' => trans('core.base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core.base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core.base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core.base::forms.description_placeholder'),
                    'data-counter' => 400,
                ],
            ])
            ->add('featured', 'onOff', [
                'label' => trans('core.base::forms.featured'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->add('content', 'editor', [
                'label' => trans('core.base::forms.content'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core.base::forms.description_placeholder'),
                    'with-short-code' => true,
                ],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core.base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => [
                    1 => trans('core.base::system.activated'),
                    0 => trans('core.base::system.deactivated'),
                ],
            ])
            ->add('format_type', 'customRadio', [
                'label' => trans('plugins.blog::posts.form.format_type'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => get_post_formats(true),
            ])
            ->add('categories[]', 'categoryMulti', [
                'label' => trans('plugins.blog::posts.form.categories'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => get_categories_with_children(),
                'value' => old('categories', $selected_categories),
            ])
            ->add('image', 'mediaImage', [
                'label' => trans('core.base::forms.image'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->add('tag', 'text', [
                'label' => trans('plugins.blog::posts.form.tags'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'tags',
                    'data-role' => 'tagsinput',
                    'placeholder' => trans('plugins.blog::posts.form.tags_placeholder'),
                ],
                'value' => $tags,
                'help_block' => [
                    'text' => 'Tag route',
                    'tag' => 'div',
                    'attr' => [
                        'data-tag-route' => route('tags.all'),
                        'class' => 'hidden',
                    ],
                ],
            ])
            ->setBreakFieldPoint('status');
    }
}
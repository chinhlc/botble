<?php

namespace Botble\Base\Forms;

use Assets;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\CustomRadioField;
use Botble\Base\Forms\Fields\CustomSelectField;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\TimeField;
use Botble\Slug\Forms\Fields\PermalinkField;
use JsValidator;
use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;

abstract class FormAbstract extends Form
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $module_name = '';

    /**
     * @var string
     */
    protected $validatorClass = '';

    /**
     * @var array
     */
    protected $meta_boxes = [];

    /**
     * @var string
     */
    protected $action_buttons = '';

    /**
     * @var string
     */
    protected $break_field_point = '';

    /**
     * @var bool
     */
    protected $useInlineJs = false;

    /**
     * @var string
     */
    protected $wrapper_class = 'form-body';

    /**
     * FormAbstract constructor.
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function __construct()
    {
        $this->setMethod('POST');
        $this->setFormOption('template', 'core.base::forms.form');
        $this->action_buttons = view('core.base::elements.form-actions')->render();
        $this->setFormOption('id', 'form_' . md5(get_class($this)));
    }

    /**
     * @return array
     * @author Sang Nguyen
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     * @author Sang Nguyen
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->module_name;
    }

    /**
     * @param string $module
     * @return $this
     */
    public function setModuleName($module): self
    {
        $this->module_name = $module;
        return $this;
    }

    /**
     * @return array
     */
    public function getMetaBoxes(): array
    {
        uasort($this->meta_boxes, function ($before, $after) {
            if (array_get($before, 'priority', 0) > array_get($after, 'priority', 0)) {
                return 1;
            } elseif (array_get($before, 'priority', 0) < array_get($after, 'priority', 0)) {
                return -1;
            }
            return 0;
        });
        return $this->meta_boxes;
    }


    /**
     * @param string $name
     * @return string
     * @throws \Throwable
     */
    public function getMetaBox($name): string
    {
        if (!array_get($this->meta_boxes, $name)) {
            return '';
        }

        $meta_box = $this->meta_boxes[$name];
        return view('core.base::forms.partials.meta-box', compact('meta_box'))->render();
    }

    /**
     * @param array $boxes
     * @return $this
     */
    public function addMetaBoxes($boxes): self
    {
        if (!is_array($boxes)) {
            $boxes = [$boxes];
        }
        $this->meta_boxes = array_merge($this->meta_boxes, $boxes);
        return $this;
    }

    /**
     * @param string $name
     * @return FormAbstract
     */
    public function removeMetaBox($name): self
    {
        array_forget($this->meta_boxes, $name);
        return $this;
    }

    /**
     * @return string
     */
    public function getActionButtons(): string
    {
        return $this->action_buttons;
    }

    /**
     * @return $this
     */
    public function removeActionButtons(): self
    {
        $this->action_buttons = '';
        return $this;
    }

    /**
     * @param string $action_buttons
     * @return $this
     */
    public function setActionButtons($action_buttons): self
    {
        $this->action_buttons = $action_buttons;
        return $this;
    }

    /**
     * @return string
     */
    public function getValidatorClass(): string
    {
        return $this->validatorClass;
    }

    /**
     * @param string $validatorClass
     * @return $this
     */
    public function setValidatorClass($validatorClass): self
    {
        $this->validatorClass = $validatorClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getBreakFieldPoint(): string
    {
        return $this->break_field_point;
    }

    /**
     * @param string $break_field_point
     * @return $this
     */
    public function setBreakFieldPoint(string $break_field_point): self
    {
        $this->break_field_point = $break_field_point;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseInlineJs(): bool
    {
        return $this->useInlineJs;
    }

    /**
     * @param bool $useInlineJs
     * @return $this
     */
    public function setUseInlineJs(bool $useInlineJs): self
    {
        $this->useInlineJs = $useInlineJs;
        return $this;
    }

    /**
     * @return string
     */
    public function getWrapperClass(): string
    {
        return $this->wrapper_class;
    }

    /**
     * @param string $wrapper_class
     * @return $this
     */
    public function setWrapperClass(string $wrapper_class): self
    {
        $this->wrapper_class = $wrapper_class;
        return $this;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel($model): self
    {
        parent::setupModel($model);
        $this->rebuildForm();
        return $this;
    }

    /**
     * @author Sang Nguyen
     * @return $this
     */
    public function withCustomFields(): self
    {
        if (!$this->formHelper->hasCustomField('customSelect')) {
            $this->addCustomField('customSelect', CustomSelectField::class);
        }

        if (!$this->formHelper->hasCustomField('editor')) {
            $this->addCustomField('editor', EditorField::class);
        }
        if (!$this->formHelper->hasCustomField('onOff')) {
            $this->addCustomField('onOff', OnOffField::class);
        }
        if (!$this->formHelper->hasCustomField('customRadio')) {
            $this->addCustomField('customRadio', CustomRadioField::class);
        }
        if (!$this->formHelper->hasCustomField('mediaImage')) {
            $this->addCustomField('mediaImage', MediaImageField::class);
        }
        if (!$this->formHelper->hasCustomField('color')) {
            $this->addCustomField('color', ColorField::class);
        }
        if (!$this->formHelper->hasCustomField('time')) {
            $this->addCustomField('time', TimeField::class);
        }
        if (!$this->formHelper->hasCustomField('permalink')) {
            $this->addCustomField('permalink', PermalinkField::class);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function hasTabs(): self
    {
        $this->setFormOption('template', 'core.base::forms.form-tabs');
        return $this;
    }

    /**
     * @return int
     * @author Sang Nguyen
     */
    public function hasMainFields()
    {
        if (!$this->break_field_point) {
            return count($this->fields);
        }

        $main_fields = [];

        /**
         * @var FormField $field
         */
        foreach ($this->fields as $field) {
            if ($field->getName() == $this->break_field_point) {
                break;
            }

            $main_fields[] = $field;
        }

        return count($main_fields);
    }

    /**
     * @return $this
     */
    public function disableFields()
    {
        parent::disableFields();
        return $this;
    }

    /**
     * @param array $options
     * @param bool $showStart
     * @param bool $showFields
     * @param bool $showEnd
     * @return string
     * @author Sang Nguyen
     */
    public function renderForm(array $options = [], $showStart = true, $showFields = true, $showEnd = true): string
    {
        Assets::addAppModule(['form-validation'])
            ->addJavascript(['are-you-sure']);

        apply_filters(BASE_FILTER_BEFORE_RENDER_FORM, $this, $this->module_name, $this->getModel());

        return parent::renderForm($options, $showStart, $showFields, $showEnd);
    }

    /**
     * @return string
     * @throws \Exception
     * @author Sang Nguyen
     */
    public function renderValidatorJs(): string
    {
        $element = null;
        if ($this->getFormOption('id')) {
            $element = '#' . $this->getFormOption('id');
        } elseif ($this->getFormOption('class')) {
            $element = '.' . $this->getFormOption('class');
        }

        return JsValidator::formRequest($this->getValidatorClass(), $element);
    }

    /**
     * @param $name
     * @param $class
     * @return $this|Form
     */
    public function addCustomField($name, $class)
    {
        parent::addCustomField($name, $class);
        return $this;
    }
}
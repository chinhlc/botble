<?php

namespace Botble\Base\Supports;

use Assets;

class Editor
{
    /**
     * Editor constructor.
     */
    public function __construct()
    {
        add_action(BASE_ACTION_ENQUEUE_SCRIPTS, [$this, 'registerAssets'], 12, 1);
    }

    /**
     * Register Editor's assets
     * @author Sang Nguyen
     */
    public function registerAssets()
    {
        Assets::addJavascriptDirectly(
            config('core.base.general.editor.' .
                setting('rich_editor', config('core.base.general.editor.primary')) . '.js')
        )
        ->addAppModule(['editor']);
    }

    /**
     * @param $name
     * @param null $value
     * @param bool $with_short_code
     * @param array $attributes
     * @return string
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function render($name, $value = null, $with_short_code = false, array $attributes = [])
    {
        $attributes['class'] = array_get($attributes, 'class', '') .
            ' form-control editor-' .
            setting('rich_editor', config('core.base.general.editor.primary'));
        $attributes['id'] = array_has($attributes, 'id') ? $attributes['id'] : $name;
        $attributes['with-short-code'] = $with_short_code;
        $attributes['rows'] = array_get($attributes, 'rows', 10);

        return view('core.base::elements.forms.editor', compact('name', 'value', 'with-short-code', 'attributes'))
            ->render();
    }
}

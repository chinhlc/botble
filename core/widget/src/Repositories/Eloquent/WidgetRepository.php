<?php

namespace Botble\Widget\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Widget\Repositories\Interfaces\WidgetInterface;

class WidgetRepository extends RepositoriesAbstract implements WidgetInterface
{
    /**
     * @var string
     */
    protected $screen = WIDGET_MANAGER_MODULE_SCREEN_NAME;
    
    /**
     * Get all theme widgets
     * @param string $theme
     * @return mixed
     * @author Sang Nguyen
     */
    public function getByTheme($theme)
    {
        $data = $this->model->where('theme', '=', $theme)->get();
        $this->resetModel();
        return $data;
    }
}

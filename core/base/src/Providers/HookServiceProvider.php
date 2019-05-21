<?php

namespace Botble\Base\Providers;

use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Auth;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Boot the service provider.
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function boot()
    {
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addStatsWidgets'], 15, 2);
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function addStatsWidgets($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('plugins.list')) {
            return $widgets;
        }

        $widget = $widget_settings->where('name', 'widget_total_plugins')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_total_plugins']);
        }

        $widget->title = trans('core.base::system.total_plugins');
        $widget->icon = 'fa fa-plug';
        $widget->color = '#8e44ad';

        $plugins = count(scan_folder(base_path('plugins')));

        $widgets['plugins'] = [
            'id' => $widget->id,
            'view' => view('core.base::widgets.stats', compact('plugins', 'widget_setting'))->render(),
        ];

        return $widgets;
    }
}

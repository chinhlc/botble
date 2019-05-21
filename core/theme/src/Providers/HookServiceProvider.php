<?php

namespace Botble\Theme\Providers;

use Botble\Base\Supports\Collection;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
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
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addStatsWidgets'], 29, 2);
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
        if (!Auth::user()->hasPermission('theme.list')) {
            return $widgets;
        }

        $widget = $widget_settings->where('name', 'widget_total_themes')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_total_themes']);
        }

        $widget->title = trans('core.theme::theme.total_themes');
        $widget->icon = 'fa fa-paint-brush';
        $widget->color = '#e7505a';

        $themes = count(scan_folder(public_path('themes')));

        $widgets['themes'] = [
            'id' => $widget->id,
            'view' => view('core.theme::widgets.stats', compact('themes', 'widget_setting'))->render(),
        ];

        return $widgets;
    }
}

<?php

namespace Botble\RequestLog\Providers;

use Assets;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\RequestLog\Events\RequestHandlerEvent;
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
     */
    public function boot()
    {
        add_action(BASE_ACTION_SITE_ERROR, [$this, 'handleSiteError'], 125, 1);
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 125, 2);
    }

    /**
     * Fire event log
     *
     * @param $code
     * @author Sang Nguyen
     */
    public function handleSiteError($code)
    {
        event(new RequestHandlerEvent($code));
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function registerDashboardWidgets($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('request-log.list')) {
            return $widgets;
        }

        Assets::addJavascriptDirectly(['vendor/core/plugins/request-log/js/request-log.js']);

        $widget = $widget_settings->where('name', 'widget_request_errors')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_request_errors']);
        }

        $widget->title = trans('plugins.request-log::request-log.widget_request_errors');
        $widget->icon = 'fas fa-unlink';
        $widget->color = '#e7505a';

        $data = [
            'id' => $widget->id,
            'view' => view('plugins.request-log::widgets.base', compact('widget', 'widget_setting'))->render(),
        ];

        if (empty($widget_setting) || array_key_exists($widget_setting->order, $widgets)) {
            $widgets[] = $data;
        } else {
            $widgets[$widget_setting->order] = $data;
        }
        return $widgets;
    }
}

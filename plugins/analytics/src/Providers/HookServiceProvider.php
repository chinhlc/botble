<?php

namespace Botble\Analytics\Providers;

use Assets;
use Auth;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Bootstrap the application events.
     * @author Sang Nguyen
     */
    public function boot()
    {
        if (env('ANALYTICS_ENABLE_DASHBOARD_WIDGETS', true)) {
            add_action(DASHBOARD_ACTION_REGISTER_SCRIPTS, [$this, 'registerScripts'], 18);
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addGeneralWidget'], 18, 2);
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addPageWidget'], 19, 2);
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addBrowserWidget'], 20, 2);
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addReferrerWidget'], 22, 2);
            add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addAnalyticsSetting'], 99, 1);
        }
    }

    /**
     * @return void
     * @author Sang Nguyen
     */
    public function registerScripts()
    {
        if (Auth::user()->hasAnyPermission([
            'analytics.general',
            'analytics.page',
            'analytics.browser',
            'analytics.referrer',
        ])) {
            Assets::addJavascript(['jvectormap', 'raphael', 'morris'])
                ->addStylesheets(['jvectormap', 'raphael', 'morris'])
                ->addJavascriptDirectly(['/vendor/core/plugins/analytics/js/analytics.js']);
        }
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function addGeneralWidget($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('analytics.general')) {
            return $widgets;
        }

        $widget = $widget_settings->where('name', 'widget_analytics_general')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_analytics_general']);
        }

        $widget->title = trans('plugins.analytics::analytics.widget_analytics_general');
        $widget->icon = 'fas fa-chart-line';
        $widget->color = '#f2784b';

        $data = [
            'id' => $widget->id,
            'view' => view('plugins.analytics::widgets.general.base', compact('widget', 'widget_setting'))->render(),
        ];

        if (empty($widget_setting) || array_key_exists($widget_setting->order, $widgets)) {
            $widgets[] = $data;
        } else {
            $widgets[$widget_setting->order] = $data;
        }
        return $widgets;
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function addPageWidget($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('analytics.page')) {
            return $widgets;
        }

        $widget = $widget_settings->where('name', 'widget_analytics_page')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_analytics_page']);
        }

        $widget->title = trans('plugins.analytics::analytics.widget_analytics_page');
        $widget->icon = 'far fa-newspaper';
        $widget->color = '#3598dc';

        $data = [
            'id' => $widget->id,
            'view' => view('plugins.analytics::widgets.page.base', compact('widget', 'widget_setting'))->render(),
        ];

        if (empty($widget_setting) || array_key_exists($widget_setting->order, $widgets)) {
            $widgets[] = $data;
        } else {
            $widgets[$widget_setting->order] = $data;
        }
        return $widgets;
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function addBrowserWidget($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('analytics.browser')) {
            return $widgets;
        }

        $widget = $widget_settings->where('name', 'widget_analytics_browser')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_analytics_browser']);
        }

        $widget->title = trans('plugins.analytics::analytics.widget_analytics_browser');
        $widget->icon = 'fab fa-safari';
        $widget->color = '#8e44ad';

        $data = [
            'id' => $widget->id,
            'view' => view('plugins.analytics::widgets.browser.base', compact('widget', 'widget_setting'))->render(),
        ];

        if (empty($widget_setting) || array_key_exists($widget_setting->order, $widgets)) {
            $widgets[] = $data;
        } else {
            $widgets[$widget_setting->order] = $data;
        }
        return $widgets;
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function addReferrerWidget($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('analytics.referrer')) {
            return $widgets;
        }

        $widget = $widget_settings->where('name', 'widget_analytics_referrer')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_analytics_referrer']);
        }

        $widget->title = trans('plugins.analytics::analytics.widget_analytics_referrer');
        $widget->icon = 'fas fa-user-friends';
        $widget->color = '#3598dc';

        $data = [
            'id' => $widget->id,
            'view' => view('plugins.analytics::widgets.referrer.base', compact('widget', 'widget_setting'))->render(),
        ];

        if (empty($widget_setting) || array_key_exists($widget_setting->order, $widgets)) {
            $widgets[] = $data;
        } else {
            $widgets[$widget_setting->order] = $data;
        }
        return $widgets;
    }

    /**
     * @param null $data
     * @return string
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function addAnalyticsSetting($data = null)
    {
        return $data . view('plugins.analytics::setting')->render();
    }
}

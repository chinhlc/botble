<?php

namespace Botble\ACL\Providers;

use Botble\ACL\Repositories\Interfaces\UserInterface;
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
     * @author Sang Nguyen
     */
    public function boot()
    {
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addUserStatsWidget'], 12, 2);
    }

    /**
     * @param array $widgets
     * @param Collection $widget_settings
     * @return array
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function addUserStatsWidget($widgets, $widget_settings)
    {
        if (!Auth::user()->hasPermission('users.list')) {
            return $widgets;
        }

        $widget = $widget_settings->where('name', 'widget_total_users')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_total_users']);
        }

        $widget->title = trans('core.acl::users.total_users');
        $widget->icon = 'fas fa-users';
        $widget->color = '#3598dc';

        $users = $this->app->make(UserInterface::class)->count();

        $widgets['users'] = [
            'id' => $widget->id,
            'view' => view('core.acl::partials.widgets.user-stats', compact('users', 'widget_setting'))->render(),
        ];
        return $widgets;
    }
}

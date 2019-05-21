<?php

namespace Botble\AuditLog\Providers;

use Assets;
use AuditLog;
use Auth;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Botble\AuditLog\Events\AuditHandlerEvent;
use Illuminate\Http\Request;

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
        add_action(AUTH_ACTION_AFTER_LOGOUT_SYSTEM, [$this, 'handleLogout'], 45, 3);

        add_action(USER_ACTION_AFTER_UPDATE_PASSWORD, [$this, 'handleUpdatePassword'], 45, 3);
        add_action(USER_ACTION_AFTER_UPDATE_PASSWORD, [$this, 'handleUpdateProfile'], 45, 3);

        if (defined('BACKUP_ACTION_AFTER_BACKUP')) {
            add_action(BACKUP_ACTION_AFTER_BACKUP, [$this, 'handleBackup'], 45, 1);
            add_action(BACKUP_ACTION_AFTER_RESTORE, [$this, 'handleRestore'], 45, 1);
        }

        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 28, 2);
    }

    /**
     * @param string $screen
     * @param Request $request
     * @param \stdClass $data
     * @author Sang Nguyen
     */
    public function handleLogin($screen, Request $request, $data)
    {
        event(new AuditHandlerEvent('to the system', 'logged in', $data->id, AuditLog::getReferenceName($screen, $data), 'info'));
    }

    /**
     * @param string $screen
     * @param Request $request
     * @param \stdClass $data
     * @author Sang Nguyen
     */
    public function handleLogout($screen, Request $request, $data)
    {
        event(new AuditHandlerEvent('of the system', 'logged out', $data->id, AuditLog::getReferenceName($screen, $data), 'info'));
    }

    /**
     * @param string $screen
     * @param Request $request
     * @param \stdClass $data
     * @author Sang Nguyen
     */
    public function handleUpdateProfile($screen, Request $request, $data)
    {
        event(new AuditHandlerEvent($screen, 'updated profile', $data->id, AuditLog::getReferenceName($screen, $data), 'info'));
    }

    /**
     * @param string $screen
     * @param Request $request
     * @param \stdClass $data
     * @author Sang Nguyen
     */
    public function handleUpdatePassword($screen, Request $request, $data)
    {
        event(new AuditHandlerEvent($screen, 'changed password', $data->id, AuditLog::getReferenceName($screen, $data), 'danger'));
    }

    /**
     * @param string $screen
     * @author Sang Nguyen
     */
    public function handleBackup($screen)
    {
        event(new AuditHandlerEvent($screen, 'backup', 0, '', 'info'));
    }

    /**
     * @param string $screen
     * @author Sang Nguyen
     */
    public function handleRestore($screen)
    {
        event(new AuditHandlerEvent($screen, 'restored', 0, '', 'info'));
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
        if (!Auth::user()->hasPermission('audit-log.list')) {
            return $widgets;
        }

        Assets::addJavascriptDirectly(['/vendor/core/plugins/audit-log/js/audit-log.js']);

        $widget = $widget_settings->where('name', 'widget_audit_logs')->first();
        $widget_setting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = $this->app->make(DashboardWidgetInterface::class)->firstOrCreate(['name' => 'widget_audit_logs']);
        }

        $widget->title = trans('plugins.audit-log::history.widget_audit_logs');
        $widget->icon = 'fas fa-history';
        $widget->color = '#44b6ae';

        $data = [
            'id' => $widget->id,
            'view' => view('plugins.audit-log::widgets.base', compact('widget', 'widget_setting'))->render(),
        ];

        if (empty($widget_setting) || array_key_exists($widget_setting->order, $widgets)) {
            $widgets[] = $data;
        } else {
            $widgets[$widget_setting->order] = $data;
        }
        return $widgets;
    }
}

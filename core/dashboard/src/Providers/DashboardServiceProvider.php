<?php

namespace Botble\Dashboard\Providers;

use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Dashboard\Models\DashboardWidget;
use Botble\Dashboard\Models\DashboardWidgetSetting;
use Botble\Dashboard\Repositories\Caches\DashboardWidgetCacheDecorator;
use Botble\Dashboard\Repositories\Caches\DashboardWidgetSettingCacheDecorator;
use Botble\Dashboard\Repositories\Eloquent\DashboardWidgetRepository;
use Botble\Dashboard\Repositories\Eloquent\DashboardWidgetSettingRepository;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetSettingInterface;
use Botble\Support\Services\Cache\Cache;
use Illuminate\Support\ServiceProvider;

/**
 * Class DashboardServiceProvider
 * @package Botble\Dashboard
 * @author Sang Nguyen
 * @since 02/07/2016 09:50 AM
 */
class DashboardServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @author Sang Nguyen
     */
    public function register()
    {
        if (setting('enable_cache', false)) {
            $this->app->singleton(DashboardWidgetInterface::class, function () {
                return new DashboardWidgetCacheDecorator(new DashboardWidgetRepository(new DashboardWidget()), new Cache($this->app['cache'], DashboardWidgetRepository::class));
            });

            $this->app->singleton(DashboardWidgetSettingInterface::class, function () {
                return new DashboardWidgetSettingCacheDecorator(new DashboardWidgetSettingRepository(new DashboardWidgetSetting()), new Cache($this->app['cache'], DashboardWidgetSettingRepository::class));
            });
        } else {
            $this->app->singleton(DashboardWidgetInterface::class, function () {
                return new DashboardWidgetRepository(new DashboardWidget());
            });

            $this->app->singleton(DashboardWidgetSettingInterface::class, function () {
                return new DashboardWidgetSettingRepository(new DashboardWidgetSetting());
            });
        }

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * Boot the service provider.
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('core/dashboard')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssetsFolder()
            ->publishPublicFolder()
            ->loadMigrations();
    }
}

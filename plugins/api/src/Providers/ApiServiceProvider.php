<?php

namespace Botble\Api\Providers;

use Illuminate\Support\ServiceProvider;
use Botble\Base\Supports\Helper;
use Botble\Base\Events\SessionStarted;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class ApiServiceProvider extends ServiceProvider
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
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        config([
            'auth.guards.api' => [
                'driver' => 'passport',
                'provider' => 'users',
            ],
        ]);

        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('plugins/api')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes(['web', 'api']);

        Event::listen(SessionStarted::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-core-api-clients',
                'priority' => 5,
                'parent_id' => 'cms-core-settings',
                'name' => trans('plugins.api::api.api_clients'),
                'icon' => null,
                'url' => route('api.clients'),
                'permissions' => ['api.clients'],
            ]);
        });
    }
}

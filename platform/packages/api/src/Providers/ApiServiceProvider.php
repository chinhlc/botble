<?php

namespace Botble\Api\Providers;

use Barryvdh\Cors\HandleCors;
use Botble\Api\Http\Middleware\ForceJsonResponseMiddleware;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class ApiServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @throws BindingResolutionException
     */
    public function register()
    {
        $router = $this->app->make('router');

        $router->pushMiddlewareToGroup('api', ForceJsonResponseMiddleware::class);
        $router->pushMiddlewareToGroup('api', HandleCors::class);
    }

    public function boot()
    {
        $this->setNamespace('packages/api')
            ->publishAssets();

        $this->app->booted(function () {
            config([
                'apidoc.routes.0.match.prefixes' => ['api/*'],
                'apidoc.routes.0.apply.headers'  => [
                    'Authorization' => 'Bearer {token}',
                    'Api-Version'   => 'v1',
                ],
            ]);
        });
    }
}

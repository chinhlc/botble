<?php

namespace Botble\DevTool\Providers;

use Illuminate\Support\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class DevToolServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot()
    {
        $this->setNamespace('packages/dev-tool');

        $this->app->register(CommandServiceProvider::class);
    }
}

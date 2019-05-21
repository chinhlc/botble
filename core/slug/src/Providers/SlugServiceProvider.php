<?php

namespace Botble\Slug\Providers;

use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Slug\Models\Slug;
use Botble\Slug\Repositories\Caches\SlugCacheDecorator;
use Botble\Slug\Repositories\Eloquent\SlugRepository;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Support\Services\Cache\Cache;
use Illuminate\Support\ServiceProvider;

class SlugServiceProvider extends ServiceProvider
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
            $this->app->singleton(SlugInterface::class, function () {
                return new SlugCacheDecorator(new SlugRepository(new Slug()), new Cache($this->app['cache'], SlugRepository::class));
            });
        } else {
            $this->app->singleton(SlugInterface::class, function () {
                return new SlugRepository(new Slug());
            });
        }

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('core/slug')
            ->loadAndPublishConfigurations(['general'])
            ->loadAndPublishViews()
            ->loadRoutes()
            ->loadAndPublishTranslations()
            ->loadMigrations();

        $this->app->register(FormServiceProvider::class);
        $this->app->register(HookServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);
    }
}

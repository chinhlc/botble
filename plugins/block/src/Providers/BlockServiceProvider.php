<?php

namespace Botble\Block\Providers;

use Botble\Base\Events\SessionStarted;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Block\Models\Block;
use Event;
use Illuminate\Support\ServiceProvider;
use Botble\Block\Repositories\Caches\BlockCacheDecorator;
use Botble\Block\Repositories\Eloquent\BlockRepository;
use Botble\Block\Repositories\Interfaces\BlockInterface;
use Botble\Support\Services\Cache\Cache;
use Botble\Base\Supports\Helper;
use Language;

class BlockServiceProvider extends ServiceProvider
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
            $this->app->singleton(BlockInterface::class, function () {
                return new BlockCacheDecorator(new BlockRepository(new Block()), new Cache($this->app['cache'], BlockRepository::class));
            });
        } else {
            $this->app->singleton(BlockInterface::class, function () {
                return new BlockRepository(new Block());
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
            ->setNamespace('plugins/block')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();

        Event::listen(SessionStarted::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-plugins-block',
                'priority' => 6,
                'parent_id' => null,
                'name' => trans('plugins.block::block.menu'),
                'icon' => 'fa fa-code',
                'url' => route('block.list'),
                'permissions' => ['block.list'],
            ]);
        });

        $this->app->register(HookServiceProvider::class);

        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            Language::registerModule([BLOCK_MODULE_SCREEN_NAME]);
        }
    }
}

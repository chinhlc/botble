<?php

namespace Botble\Page\Providers;

use Botble\Base\Events\SessionStarted;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Page\Models\Page;
use Botble\Page\Repositories\Caches\PageCacheDecorator;
use Botble\Page\Repositories\Eloquent\PageRepository;
use Botble\Page\Repositories\Interfaces\PageInterface;
use Botble\Shortcode\View\View;
use Botble\Support\Services\Cache\Cache;
use Event;
use Illuminate\Support\ServiceProvider;

/**
 * Class PageServiceProvider
 * @package Botble\Page
 * @author Sang Nguyen
 * @since 02/07/2016 09:50 AM
 */
class PageServiceProvider extends ServiceProvider
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
            $this->app->singleton(PageInterface::class, function () {
                return new PageCacheDecorator(new PageRepository(new Page()), new Cache($this->app['cache'], PageRepository::class));
            });
        } else {
            $this->app->singleton(PageInterface::class, function () {
                return new PageRepository(new Page());
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
            ->setNamespace('core/page')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations();

        $this->app->register(HookServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        Event::listen(SessionStarted::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-core-page',
                'priority' => 2,
                'parent_id' => null,
                'name' => trans('core.page::pages.menu_name'),
                'icon' => 'fa fa-book',
                'url' => route('pages.list'),
                'permissions' => ['pages.list'],
            ]);

            admin_bar()->registerLink('Page', route('pages.list'), 'add-new');
        });

        view()->composer(['core.page::themes.page'], function(View $view) {
            $view->withShortcodes();
        });
    }
}

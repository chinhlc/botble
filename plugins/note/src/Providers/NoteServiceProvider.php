<?php

namespace Botble\Note\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Note\Facades\NoteFacade;
use Botble\Support\Services\Cache\Cache;
use Botble\Base\Supports\Helper;
use Botble\Note\Models\Note;
use Botble\Note\Repositories\Caches\NoteCacheDecorator;
use Botble\Note\Repositories\Eloquent\NoteRepository;
use Botble\Note\Repositories\Interfaces\NoteInterface;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

/**
 * Class NoteServiceProvider
 * @package Botble\Note
 * @author Sang Nguyen
 * @since 07/02/2016 09:50 AM
 */
class NoteServiceProvider extends ServiceProvider
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
            $this->app->singleton(NoteInterface::class, function () {
                return new NoteCacheDecorator(new NoteRepository(new Note()), new Cache($this->app['cache'], NoteRepository::class));
            });
        } else {
            $this->app->singleton(NoteInterface::class, function () {
                return new NoteRepository(new Note());
            });
        }

        Helper::autoload(__DIR__ . '/../../helpers');

        AliasLoader::getInstance()->alias('Note', NoteFacade::class);
    }

    /**
     * Boot the service provider.
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('plugins/note')
            ->loadAndPublishViews()
            ->loadAndPublishConfigurations(['general'])
            ->loadMigrations();

        $this->app->register(HookServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }
}

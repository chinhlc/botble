<?php

namespace Botble\Translation\Providers;

use Botble\Base\Events\SessionStarted;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Translation\Console\CleanCommand;
use Botble\Translation\Console\ExportCommand;
use Botble\Translation\Console\FindCommand;
use Botble\Translation\Console\ImportCommand;
use Botble\Translation\Console\ResetCommand;
use Botble\Translation\Manager;
use Event;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
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
        $this->app->bind('translation-manager', Manager::class);

        $this->commands([
            ImportCommand::class,
            FindCommand::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ResetCommand::class,
                ExportCommand::class,
                CleanCommand::class
            ]);
        }
    }

    /**
     * @author Sang Nguyen
     */
    public function boot()
    {
        $this->setIsInConsole($this->app->runningInConsole())
            ->setNamespace('plugins/translation')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadMigrations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssetsFolder()
            ->publishPublicFolder();

        Event::listen(SessionStarted::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-plugin-translation',
                'priority' => 6,
                'parent_id' => 'cms-core-platform-administration',
                'name' => trans('plugins.translation::translation.menu_name'),
                'icon' => null,
                'url' => route('translations.list'),
                'permissions' => ['translations.list'],
            ]);
        });
    }
}

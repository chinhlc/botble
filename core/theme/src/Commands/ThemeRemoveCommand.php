<?php

namespace Botble\Theme\Commands;

use Botble\Setting\Repositories\Interfaces\SettingInterface;
use Botble\Setting\Supports\SettingStore;
use Botble\Theme\Events\ThemeRemoveEvent;
use Botble\Widget\Repositories\Interfaces\WidgetInterface;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;

class ThemeRemoveCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'cms:theme:remove 
        {name : The theme that you want to remove} 
        {--force : Force to remove theme without confirmation} 
        {--path= : Path to theme directory}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove an existing theme';

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var File
     */
    protected $files;

    /**
     * @var WidgetInterface
     */
    protected $widgetRepository;

    /**
     * @var SettingInterface
     */
    protected $settingRepository;

    /**
     * @var SettingStore
     */
    protected $settingStore;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param WidgetInterface $widgetRepository
     * @param SettingInterface $settingRepository
     * @param SettingStore $settingStore
     * @author Teepluss <admin@laravel.in.th>
     */
    public function __construct(
        Repository $config,
        File $files,
        WidgetInterface $widgetRepository,
        SettingInterface $settingRepository,
        SettingStore $settingStore
    )
    {
        $this->config = $config;

        $this->files = $files;
        $this->widgetRepository = $widgetRepository;
        $this->settingRepository = $settingRepository;
        $this->settingStore = $settingStore;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @author Teepluss <admin@laravel.in.th>
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return false;
        }

        // The theme is not exists.
        if (!$this->files->isDirectory($this->getPath(null))) {
            $this->error('Theme "' . $this->getTheme() . '" is not exists.');
            return false;
        }

        if ($this->settingStore->get('theme') == $this->getTheme()) {
            $this->error('Cannot remove activated theme, please activate another theme before removing "' . $this->getTheme() . '"!');
            return false;
        }

        $themePath = $this->getPath(null);

        if (app()->runningInConsole() &&
            $this->confirm('Are you sure you want to permanently delete? [yes|no]', $this->hasOption('force'))
        ) {
            return $this->processRemove($themePath);
        }
        return $this->processRemove($themePath);
    }

    /**
     * Get root writable path.
     *
     * @param  string $path
     * @return string
     * @author Teepluss <admin@laravel.in.th>
     */
    protected function getPath($path)
    {
        $rootPath = public_path($this->config->get('core.theme.general.themeDir'));
        if ($this->option('path')) {
            $rootPath = $this->option('path');
        }

        return $rootPath . '/' . strtolower($this->getTheme()) . '/' . $path;
    }

    /**
     * Get the theme name.
     *
     * @return string
     * @author Teepluss <admin@laravel.in.th>
     */
    protected function getTheme()
    {
        return strtolower($this->argument('name'));
    }

    /**
     * @param $themePath
     * @return boolean
     */
    protected function processRemove($themePath)
    {
        // Delete permanent.
        $this->files->deleteDirectory($themePath, false);
        $this->widgetRepository->deleteBy(['theme' => $this->getTheme()]);
        $this->settingRepository->getModel()
            ->where('key', 'like', 'theme-' . $this->getTheme() . '-%')
            ->delete();

        event(new ThemeRemoveEvent($this->getTheme()));

        $this->info('Theme "' . $this->getTheme() . '" has been destroyed.');
        return true;
    }
}

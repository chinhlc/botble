<?php

namespace Botble\Theme\Commands;

use Botble\Setting\Supports\SettingStore;
use Illuminate\Console\Command;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem as File;

class ThemeActivateCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'cms:theme:activate 
        {name : The theme that you want to activate} 
        {--path= : Path to theme directory}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a theme';

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var File
     */
    protected $files;

    /**
     * @var SettingStore
     */
    protected $settingStore;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param SettingStore $settingStore
     * @author Teepluss <admin@laravel.in.th>
     */
    public function __construct(Repository $config, File $files, SettingStore $settingStore)
    {
        $this->config = $config;
        $this->files = $files;
        $this->settingStore = $settingStore;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @author Sang Nguyen
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return false;
        }

        if (!$this->files->isDirectory($this->getPath(null))) {
            $this->error('Theme "' . $this->getTheme() . '" is not exists.');
            return false;
        }

        $this->settingStore
            ->set('theme', $this->argument('name'))
            ->save();
        $this->info('Activate theme ' . $this->argument('name') . ' successfully!');
        $this->call('cache:clear');
        return true;
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
}

<?php

namespace Botble\Theme\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;

class ThemeAssetsPublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = '
        cms:theme:assets:publish
        {--name= : The theme that you want to publish assets}
        {--path= : Path to theme directory}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish assets for a theme';

    /**
     * @var File
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param File $files
     */
    public function __construct(File $files)
    {
        $this->files = $files;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return bool
     *
     */
    public function handle()
    {
        if ($this->option('name') && !preg_match('/^[a-z0-9\-]+$/i', $this->option('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return false;
        }

        if ($this->option('name') && !$this->files->isDirectory($this->getPath(null, $this->getTheme()))) {
            $this->error('Theme "' . $this->getTheme() . '" is not exists.');
            return false;
        }

        if ($this->option('name')) {
            $themes = [$this->getTheme()];
        } else {
            $themes = scan_folder(theme_path());
        }

        foreach ($themes as $theme) {
            $resourcePath = $this->getPath('public', $theme);
            $publishPath = public_path('themes/' . $theme);

            if (!$this->files->isDirectory($publishPath)) {
                $this->files->makeDirectory($publishPath, 0755, true);
            }

            $this->files->copyDirectory($resourcePath, $publishPath);
            $this->files->copy($this->getPath('screenshot.png', $theme), $publishPath . '/screenshot.png');

            $this->info('Publish assets for theme ' . $theme . ' successfully!');
        }
        return true;
    }

    /**
     * Get the theme name.
     *
     * @return string
     */
    protected function getTheme()
    {
        return strtolower($this->option('name'));
    }

    /**
     * Get root writable path.
     *
     * @param string $path
     * @param string $theme
     * @return string
     */
    protected function getPath($path, string $theme)
    {
        $rootPath = theme_path();
        if ($this->option('path')) {
            $rootPath = $this->option('path');
        }

        return rtrim($rootPath, '/') . '/' . rtrim(ltrim(strtolower($theme), '/'), '/') . '/' . $path;
    }
}

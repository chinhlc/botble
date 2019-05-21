<?php

namespace Botble\Theme\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Composer;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

class ThemeCreateCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'cms:theme:create 
        {name : The theme that you want to create} 
        {--path= : Path to theme directory}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate theme structure';

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var File
     */
    protected $files;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param Composer $composer
     * @author Teepluss <admin@laravel.in.th>
     */
    public function __construct(Repository $config, File $files, Composer $composer)
    {
        $this->config = $config;

        $this->files = $files;

        $this->composer = $composer;

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
        // The theme is already exists.
        if ($this->files->isDirectory($this->getPath(null))) {
            $this->error('Theme "' . $this->getTheme() . '" is already exists.');
            return false;
        }

        // Directories.
        $this->publishStubs();

        $this->searchAndReplaceInFiles();
        $this->renameFiles($this->getPath(null));

        $this->composer
            ->setWorkingPath($this->getPath(null))
            ->dumpAutoloads();

        $this->info('Theme "' . $this->getTheme() . '" has been created.');
        return true;
    }

    /**
     * Generate the module in Modules directory.
     * @author Sang Nguyen
     */
    protected function publishStubs()
    {
        $from = base_path('core/theme/stubs');

        if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $this->getPath(null));
            $screenshot = base_path('core/theme/resources/assets/images/' . rand(1, 12) . '.png');
            $this->files->copy($screenshot, $this->getPath(null) . '/screenshot.png');
        } else {
            $this->error('Can’t locate path: <' . $from . '>');
        }
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param string $from
     * @param string $to
     * @return void
     * @author Sang Nguyen
     */
    protected function publishDirectory($from, $to)
    {
        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to' => new Flysystem(new LocalAdapter($to)),
        ]);

        foreach ($manager->listContents('from://', true) as $file) {
            if ($file['type'] === 'file' && (!$manager->has('to://' . $file['path']) || $this->option('force'))) {
                $manager->put('to://' . $file['path'], $manager->read('from://' . $file['path']));
            }
        }
    }

    /**
     * Search and replace all occurrences of ‘Module’
     * in all files with the name of the new module.
     * @author Sang Nguyen
     */
    public function searchAndReplaceInFiles()
    {
        $path = $this->getPath(null);

        $manager = new MountManager([
            'directory' => new Flysystem(new LocalAdapter($path)),
        ]);

        foreach ($manager->listContents('directory://', true) as $file) {
            if ($file['type'] === 'file') {
                $content = str_replace(['{theme}', '{Theme}',], [strtolower($this->getTheme()), studly_case($this->getTheme())], $manager->read('directory://' . $file['path']));
                $manager->put('directory://' . $file['path'], $content);
            }
        }
    }

    /**
     * Rename models and repositories.
     * @param $location
     * @return boolean
     * @author Sang Nguyen
     */
    public function renameFiles($location)
    {
        $paths = scan_folder($location);
        if (empty($paths)) {
            return false;
        }
        foreach ($paths as $path) {
            $path = $location . DIRECTORY_SEPARATOR . $path;

            $newPath = $this->transformFilename($path);
            rename($path, $newPath);

            $this->renameFiles($newPath);
        }
        return true;
    }

    /**
     * Rename file in path.
     * @param $path
     * @return string
     * @author Sang Nguyen
     */
    public function transformFilename($path)
    {
        return str_replace(
            ['{theme}', '{Theme}', '.stub'],
            [$this->getTheme(), studly_case($this->getTheme()), '.php',],
            $path
        );
    }

    /**
     * Make directory.
     *
     * @param  string $directory
     * @return void
     * @author Teepluss <admin@laravel.in.th>
     */
    protected function makeDir($directory)
    {
        if (!$this->files->isDirectory($this->getPath($directory))) {
            $this->files->makeDirectory($this->getPath($directory), 0777, true);
        }
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
     * Get default template.
     *
     * @param  string $template
     * @return string
     * @author Teepluss <admin@laravel.in.th>
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getTemplate($template)
    {
        $path = realpath(__DIR__ . '/../../stubs/' . $template . '.stub');

        return $this->files->get($path);
    }
}

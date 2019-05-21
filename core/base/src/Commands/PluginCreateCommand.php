<?php

namespace Botble\Base\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

class PluginCreateCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The name of the module uppercase first character.
     *
     * @var string
     */
    protected $plugin;

    /**
     * @var string
     */
    protected $location;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:plugin:create {name : The module that you want to create} {--force : Overwrite any existing files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a plugin in the /plugins directory.';

    /**
     * Create a new key generator command.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @author Sang Nguyen
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     * @author Sang Nguyen
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return false;
        }

        $this->plugin = strtolower($this->argument('name'));
        $this->location = config('core.base.general.plugin_path') . '/' . $this->plugin;

        if ($this->files->isDirectory($this->location)) {
            $this->error('A plugin named [' . $this->plugin . '] already exists.');
            return false;
        }

        $this->publishStubs();
        $this->renameModelsAndRepositories($this->location);
        $this->searchAndReplaceInFiles();
        $this->line('------------------');
        $this->line('<info>The plugin</info> <comment>' . $this->plugin . '</comment> <info>was created in</info> <comment>' . $this->location . '</comment><info>, customize it!</info>');
        $this->line('------------------');
        $this->call('cache:clear');

        return true;
    }

    /**
     * Generate the module in Modules directory.
     * @author Sang Nguyen
     */
    protected function publishStubs()
    {
        $from = base_path('core/base/stubs/plugin');

        if ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $this->location);
        } else {
            $this->error('Can’t locate path: <' . $from . '>');
        }
    }

    /**
     * Search and replace all occurrences of ‘Module’
     * in all files with the name of the new module.
     * @author Sang Nguyen
     */
    public function searchAndReplaceInFiles()
    {
        $manager = new MountManager([
            'directory' => new Flysystem(new LocalAdapter($this->location)),
        ]);

        foreach ($manager->listContents('directory://', true) as $file) {
            if ($file['type'] === 'file') {
                $content = str_replace(
                    [
                        '{-plugin}',
                        '{plugin}',
                        '{+plugin}',
                        '{-plugins}',
                        '{plugins}',
                        '{Plugin}',
                        '{PLUGIN}',
                        '{migrate_date}',
                    ],
                    [
                        strtolower($this->plugin),
                        snake_case(str_replace('-', '_', $this->plugin)),
                        camel_case($this->plugin),
                        str_plural($this->plugin),
                        str_plural(snake_case(str_replace('-', '_', $this->plugin))),
                        ucfirst(camel_case($this->plugin)),
                        strtoupper(snake_case(str_replace('-', '_', $this->plugin))),
                        Carbon::now(config('app.timezone'))->format('Y_m_d_His'),
                    ],
                    $manager->read('directory://' . $file['path'])
                );
                $manager->put('directory://' . $file['path'], $content);
            }
        }
    }

    /**
     * Rename models and repositories.
     * @param string $location
     * @return boolean
     * @author Sang Nguyen
     */
    public function renameModelsAndRepositories($location)
    {
        $paths = scan_folder($location);
        if (empty($paths)) {
            return false;
        }
        foreach ($paths as $path) {
            $path = $location . DIRECTORY_SEPARATOR . $path;

            $newPath = $this->transformFilename($path);
            rename($path, $newPath);

            $this->renameModelsAndRepositories($newPath);
        }
        return true;
    }

    /**
     * Rename file in path.
     *
     * @param string $path
     * @return string
     * @author Sang Nguyen
     */
    public function transformFilename($path)
    {
        return str_replace(
            [
                '{-plugin}',
                '{plugin}',
                '{+plugin}',
                '{plugins}',
                '{Plugin}',
                '.stub',
                '{migrate_date}',
            ],
            [
                strtolower($this->plugin),
                snake_case(str_replace('-', '_', $this->plugin)),
                camel_case($this->plugin),
                str_plural(snake_case(str_replace('-', '_', $this->plugin))),
                ucfirst(camel_case($this->plugin)), '.php',
                Carbon::now(config('app.timezone'))->format('Y_m_d_His'),
            ],
            $path
        );
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
     * Create the directory to house the published files if needed.
     *
     * @param string $directory
     * @return void
     * @author Sang Nguyen
     */
    protected function createParentDirectory($directory)
    {
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }
}

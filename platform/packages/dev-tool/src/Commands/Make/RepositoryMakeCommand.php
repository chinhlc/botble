<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;

class RepositoryMakeCommand extends BaseMakeCommand
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:make:repository {name : The table that you want to create} {module : module name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a repository';

    /**
     * Create a new key generator command.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-\_]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return false;
        }

        $name = $this->argument('name');

        $this->publishFile($this->getStub() . '/Interfaces/{Module}Interface.stub', $name, 'Interfaces',
            'Interface.php');
        $this->publishFile($this->getStub() . '/Eloquent/{Module}Repository.stub', $name, 'Eloquent', 'Repository.php');
        $this->publishFile($this->getStub() . '/Caches/{Module}CacheDecorator.stub', $name, 'Caches',
            'CacheDecorator.php');

        $this->line('------------------');

        $this->info('Create successfully!');

        return true;
    }

    /**
     * @param string $stub
     * @param string $name
     * @param string $prefix
     * @param string $extension
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws FileNotFoundException
     */
    protected function publishFile($stub, $name, $prefix, $extension)
    {
        $path = package_path(strtolower($this->argument('module')) . '/src/Repositories/' . $prefix . '/' . ucfirst(Str::studly($name)) . $extension);

        $this->publishStubs($stub, $path);
        $this->renameFiles($stub, $path);
        $this->searchAndReplaceInFiles($name, $path, File::get($stub));
    }

    /**
     * @return string
     */
    public function getStub()
    {
        return __DIR__ . '/../../../stubs/module/src/Repositories';
    }

    /**
     * @param string $replaceText
     * @return array
     */
    public function getReplacements(string $replaceText)
    {
        return [];
    }
}

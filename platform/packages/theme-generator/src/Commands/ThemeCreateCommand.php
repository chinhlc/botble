<?php

namespace Botble\ThemeGenerator\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\Theme\Commands\Traits\ThemeTrait;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class ThemeCreateCommand extends BaseMakeCommand
{

    use ThemeTrait;

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
     * @param File $files
     * @param Composer $composer
     */
    public function __construct(File $files, Composer $composer)
    {
        $this->files = $files;

        $this->composer = $composer;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \League\Flysystem\FileNotFoundException
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $theme = $this->getTheme();
        $path = $this->getPath(null);

        if ($this->files->isDirectory($path)) {
            $this->error('Theme "' . $theme . '" is already exists.');
            return false;
        }

        $this->publishStubs($this->getStub(), $path);

        if ($this->files->isDirectory($this->getStub())) {
            $screenshot = __DIR__ . '/../../resources/assets/images/' . rand(1, 12) . '.png';
            $this->files->copy($screenshot, $path . '/screenshot.png');
        }

        $this->searchAndReplaceInFiles($theme, $path);
        $this->renameFiles($theme, $path);

        $this->composer
            ->setWorkingPath($path)
            ->dumpAutoloads();

        $this->info('Theme "' . $theme . '" has been created.');
        return true;
    }

    /**
     * @param string $replaceText
     * @return array
     */
    public function getReplacements(string $replaceText)
    {
        return [
            '{theme}' => strtolower($replaceText),
            '{Theme}' => Str::studly($replaceText),
        ];
    }

    /**
     * @return string
     */
    public function getStub()
    {
        return __DIR__ . '/../../stubs';
    }
}

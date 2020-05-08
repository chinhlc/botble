<?php

namespace Botble\PluginManagement\Commands;

use Botble\Base\Supports\Helper;
use Composer\Autoload\ClassLoader;
use DB;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Schema;

class PluginRemoveCommand extends Command
{
    use ConfirmableTrait;

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
    protected $signature = 'cms:plugin:remove {name : The plugin that you want to remove} {--force : Force to remove plugin without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a plugin in the /platform/plugins directory.';

    /**
     * @var PluginDeactivateCommand
     */
    protected $deactivateCommand;

    /**
     * PluginRemoveCommand constructor.
     * @param Filesystem $files
     * @param PluginDeactivateCommand $deactivateCommand
     */
    public function __construct(Filesystem $files, PluginDeactivateCommand $deactivateCommand)
    {
        parent::__construct();

        $this->files = $files;
        $this->deactivateCommand = $deactivateCommand;
    }

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function handle()
    {
        if (!$this->confirmToProceed('Are you sure you want to permanently delete?', true)) {
            return false;
        }

        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return false;
        }

        $plugin = ucfirst(strtolower($this->argument('name')));
        $location = plugin_path(strtolower($plugin));

        if (!$this->files->isDirectory($location)) {
            $this->line('This plugin is not exists!');
            return false;
        }

        return $this->processRemove($plugin, $location);
    }

    /**
     * @param $plugin
     * @param $location
     * @return boolean
     * @throws Exception
     * @throws FileNotFoundException
     */
    protected function processRemove($plugin, $location)
    {
        $this->call($this->deactivateCommand->getName(), ['name' => strtolower($plugin)]);

        if ($this->files->exists($location . '/plugin.json')) {
            $content = get_file_data($location . '/plugin.json');

            if (!empty($content)) {
                if (!class_exists($content['provider'])) {
                    $loader = new ClassLoader;
                    $loader->setPsr4($content['namespace'], plugin_path($plugin . '/src'));
                    $loader->register(true);
                }

                Schema::disableForeignKeyConstraints();
                if (class_exists($content['namespace'] . 'Plugin')) {
                    call_user_func([$content['namespace'] . 'Plugin', 'remove']);
                }
                Schema::enableForeignKeyConstraints();
                $this->line('<info>Remove plugin successfully!</info>');
            }
        }

        $migrations = [];
        foreach (scan_folder($location . '/database/migrations') as $file) {
            $migrations[] = pathinfo($file, PATHINFO_FILENAME);
        }

        DB::table('migrations')->whereIn('migration', $migrations)->delete();

        $this->files->deleteDirectory($location);

        if (empty($this->files->directories(plugin_path()))) {
            $this->files->deleteDirectory(plugin_path());
        }

        Helper::removePluginData($plugin);

        $this->call('cache:clear');

        $this->info('Plugin is removed!');

        return true;
    }
}

<?php

namespace Botble\Translation\Console;

use Illuminate\Console\Command;
use Botble\Translation\Manager;

class ResetCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cms:translations:reset';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Delete all languages records in database';

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * ResetCommand constructor.
     * @param Manager $manager
     * @author Barry vd. Heuvel <barryvdh@gmail.com>
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @author Barry vd. Heuvel <barryvdh@gmail.com>
     */
    public function handle()
    {
        $this->manager->truncateTranslations();
        $this->info('All translations are deleted');
    }
}

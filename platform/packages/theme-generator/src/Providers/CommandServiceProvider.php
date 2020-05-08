<?php

namespace Botble\ThemeGenerator\Providers;

use Botble\ThemeGenerator\Commands\ThemeCreateCommand;
use Botble\ThemeGenerator\Commands\ThemeInstallSampleDataCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ThemeCreateCommand::class,
            ]);
        }

        $this->commands([
            ThemeInstallSampleDataCommand::class,
        ]);
    }
}

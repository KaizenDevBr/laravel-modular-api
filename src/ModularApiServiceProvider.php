<?php

namespace KaizenDev\ModularApi;

use Illuminate\Support\ServiceProvider;
use KaizenDev\ModularApi\Commands\InstallCommand;
use KaizenDev\ModularApi\Commands\MakeModuleCommand;

class ModularApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                MakeModuleCommand::class,
            ]);
        }
    }
}

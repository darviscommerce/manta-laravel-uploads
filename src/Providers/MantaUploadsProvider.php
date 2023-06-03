<?php

namespace Manta\LaravelUploads\Providers;

use Manta\LaravelUploads\Console\InstallMantaLaravelUploads;
use Illuminate\Support\ServiceProvider;

class MantaUploadsProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // * Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // * Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallMantaLaravelUploads::class,
            ]);
        }
    }
}

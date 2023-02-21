<?php

namespace Manta\LaravelUploads\Providers;

use Manta\LaravelUploads\Console\InstallMantaLaravelUploads;
use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsCreate;
use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsList;
use Manta\LaravelUploads\Http\Livewire\Uploads\UploadsUpdate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class MantaUploadsProvider extends ServiceProvider
{


    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {

        // * Routes
        $this->registerRoutes();

        // * Laravel components
        Livewire::component('uploads-create', UploadsCreate::class);
        Livewire::component('uploads-update', UploadsUpdate::class);
        Livewire::component('uploads-list', UploadsList::class);

        // * Views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'manta-laravel-uploads');

        // * Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewComponentsAs('manta-laravel-uploads', [
            // MantaFooter::class,
        ]);

        // * Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallMantaLaravelUploads::class,
            ]);
        }

        // * Publish options
        if ($this->app->runningInConsole()) {
            // Publish view components
            $this->publishes([
                // __DIR__ . '/../public/libs/' => public_path('libs'),
                // __DIR__ . '/../public/images/' => public_path('images'),
                // __DIR__ . '/../View/Components/' => app_path('View/Components'),
                // __DIR__ . '/../Traits/' => app_path('Traits'),
                // __DIR__ . '/../resources/' => resource_path(''),
                // __DIR__ . '/../resources/views/' => resource_path('views'),
                // __DIR__ . '/../resources/views/layouts/' => resource_path('views/layouts'),
                // __DIR__ . '/../resources/views/components/' => resource_path('views/components'),
                // __DIR__ . '/../database/seeders/' => resource_path('/../database/seeders'),
            ], 'view-components');


            $this->publishes([
              __DIR__.'/../config/config.php' => config_path('manta-uploads.php'),
            ], 'config');

          }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'manta-uploads');
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    protected function routeConfiguration()
    {
        // dd(config('manta-uploads.prefix'));
        return [
            'prefix' => config('manta-uploads.prefix'),
            'middleware' => config('manta-uploads.middleware'),
        ];
    }
}

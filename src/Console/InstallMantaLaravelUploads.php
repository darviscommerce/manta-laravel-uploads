<?php

namespace Manta\LaravelUploads\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InstallMantaLaravelUploads extends Command
{
    protected $signature = 'manta-uploads:install';

    protected $description = 'Install Manta Laravel Uploads';

    public function handle()
    {
        $this->info('Installing Manta Uploads module...');

        $this->info('Migrate...');
        $this->call('migrate');

        $this->info('Publishing configuration...');

        if (! $this->configExists('manta-users.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }

        (new Filesystem)->copyDirectory(__DIR__.'/../stubs/app/Models', app_path('Models'));
        (new Filesystem)->copyDirectory(__DIR__.'/../stubs/app/Http', app_path('Http'));
        (new Filesystem)->copyDirectory(__DIR__.'/../stubs/app/View', app_path('View'));
        (new Filesystem)->copyDirectory(__DIR__.'/../stubs/app/Services', app_path('Services'));
        (new Filesystem)->copyDirectory(__DIR__.'/../stubs/resources/views', resource_path('views'));
        (new Filesystem)->copyDirectory(__DIR__.'/../stubs/resources/lang', resource_path('lang'));

        if (! Str::contains(file_get_contents(base_path('routes/web.php')), "'manta.uploads.list'")) {
            $this->info('web.php update...');
            (new Filesystem)->append(base_path('routes/web.php'), file_get_contents(__DIR__.'/../stubs/routes/web.php'));
        }
        if (! Str::contains(file_get_contents(base_path('.env')), "AZURE_STORAGE_NAME")) {
            $this->info('.env update...');
            (new Filesystem)->append(base_path('.env'), file_get_contents(__DIR__.'/../stubs/env.txt'));
        }

        $this->info('Installed Manta Uploads module');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Manta\LaravelUploads\Providers\MantaUploadsProvider",
            '--tag' => "config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

       $this->call('vendor:publish', $params);
    }
}

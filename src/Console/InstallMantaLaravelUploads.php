<?php

namespace Manta\LaravelUploads\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallMantaLaravelUploads extends Command
{
    protected $signature = 'mantalaravelusers:install';

    protected $description = 'Install Manta Laravel Uploads';

    public function handle()
    {
        $this->info('Installing Manta Laravel Bootstra...');

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

        $this->info('Installed Manta Laravel Bootstra');
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

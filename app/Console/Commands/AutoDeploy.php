<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:auto-deploy')]
#[Description('Command description')]
class AutoDeploy extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        chdir(base_path());
        exec('git fetch origin');
        exec('git pull origin dev');
        exec('php artisan migrate');
        exec('php artisan optimize:clear');
        return self::SUCCESS;
    }
}

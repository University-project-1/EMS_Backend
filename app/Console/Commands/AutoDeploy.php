<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('app:auto-deploy')]
#[Description('automatic pull')]
class AutoDeploy extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        chdir(base_path());

        $commands = [
            'git fetch origin',
            'git reset --hard origin/dev',
            'php artisan migrate:fresh --seed --force',
            'php artisan optimize:clear',
            'php artisan queue:restart',
            'php artisan queue:work',
        ];
        Log::info('starting');
        foreach ($commands as $command) {

            Log::info("Running: {$command}");

            $output = [];
            $exitCode = 0;

            exec($command . ' 2>&1', $output, $exitCode);
            Log::info($output);

            dump($output);

            if ($exitCode !== 0) {
                $this->error("FAILED: {$command}");
                Log::info('error');
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}

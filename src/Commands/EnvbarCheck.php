<?php

namespace Gillobis\Envbar\Commands;

use Illuminate\Console\Command;

class EnvbarCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envbar:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if Envbar is properly configured and can be displayed (not in production)';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $enabled = config('envbar.enabled', true);
        $environment = app()->environment();
        $allowedEnvironments = config('envbar.environments', []);

        if (! $enabled) {
            $this->error('Envbar is disabled in the configuration.');
            return self::FAILURE;
        }

        if (! in_array($environment, $allowedEnvironments)) {
            $this->error("Current environment '{$environment}' is not in the allowed environments list.");
            return self::FAILURE;
        }

        if( in_array('production', $allowedEnvironments)  ) {
            if (!config('envbar.show.production', false)) {
                $this->warn("Envbar is allowed in production environment, but 'envbar.show.production' config is not enabled.");
            } else {
                $this->warn("Envbar is enabled in production environment. Make sure this is intentional and does not leak sensitive information.");
            }
        }

        $this->info('Envbar is properly configured and can be displayed.');

        return self::SUCCESS;
    }
}

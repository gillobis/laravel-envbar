<?php

namespace Gillobis\Envbar\Commands;

use Illuminate\Console\Command;

class EnvbarStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envbar:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the status of the Envbar (enabled/disabled, current environment, metadata)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $enabled = config('envbar.enabled', true) ? 'enabled' : 'disabled';
        $environment = app()->environment();
        $metadata = config('envbar.environments_config.'.$environment, []);
        $environments = config('envbar.environments', []);

        $this->info("Envbar is {$enabled}");
        $this->info("Current environment: {$environment}");
        $this->info('Metadata: '.json_encode($metadata, JSON_PRETTY_PRINT));
        $this->info('Allowed environments: '.implode(', ', $environments));
    }
}

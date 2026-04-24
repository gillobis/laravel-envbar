<?php

namespace Gillobis\Envbar;

use Gillobis\Envbar\Interfaces\EnvbarDataProvider;
use Illuminate\Contracts\Container\BindingResolutionException;

class EnvbarManager
{
    /** @var array<int, class-string<EnvbarDataProvider>> */
    protected array $providers = [];

    /**
     * @param  class-string<EnvbarDataProvider>  $providerClass
     */
    public function registerProvider(string $providerClass): void
    {
        $this->providers[] = $providerClass;
    }

    /**
     * @return array<int, EnvbarDataProvider>
     */
    public function resolveProviders(): array
    {
        $classes = array_merge(
            config('envbar.providers', []),
            $this->providers,
        );

        $resolved = [];

        foreach ($classes as $class) {
            try {
                $instance = app()->make($class);
            } catch (\Throwable) {
                continue;
            }

            if ($instance instanceof EnvbarDataProvider) {
                $resolved[] = $instance;
            }
        }

        return $resolved;
    }

    /**
     * checks config + gate + environment
     *
     * @throws BindingResolutionException
     */
    public function isEnabled(): bool
    {
        // Check if the bar is enabled in general
        if (! config('envbar.enabled', true)) {
            return false;
        }

        // Check if the current environment is among the configured ones
        $environments = config('envbar.environments', ['local', 'staging']);
        if (! in_array(app()->environment(), $environments)) {
            return false;
        }

        // check if it's production and if we allow in production
        if (app()->environment('production') && ! config('envbar.allow_in_production', false)) {
            return false;
        }

        // Check the gate (if configured)
        $gate = config('envbar.gate');
        if ($gate && ! app()->make('Illuminate\Contracts\Auth\Access\Gate')->allows($gate)) {
            return false;
        }

        return true;
    }

    public function getCurrentEnvironment(): string
    {
        return app()->environment();
    }

    /**
     * @return array<string>
     *
     * @throws BindingResolutionException
     */
    public function getEnvironmentConfig(): array
    {
        return config('envbar.environments', ['local', 'staging']);
    }

    /**
     * @return array<mixed>
     *
     * @throws BindingResolutionException
     */
    public function getMetadata(): array
    {
        return [
            'git_branch' => $this->getGitBranch(),
            'git_commit' => $this->getGitCommit(),
            'php_version' => phpversion(),
        ];
    }

    public function getGitBranch(): ?string
    {
        $result = $this->execGit('git rev-parse --abbrev-ref HEAD');

        return $result !== null ? trim($result) : null;
    }

    public function getGitCommit(): ?string
    {
        $result = $this->execGit('git rev-parse HEAD');

        return $result !== null ? trim($result) : null;
    }

    private function execGit(string $command): ?string
    {
        $output = [];
        $exitCode = 0;
        exec($command.' 2>&1', $output, $exitCode);

        return $exitCode === 0 ? implode('\n', $output) : null;
    }

    public function render(): string
    {
        // Returns the HTML of the bar with all the data
        return view('envbar::envbar', [
            'enabled' => $this->isEnabled(),
            'environment' => $this->getCurrentEnvironment(),
            'config' => $this->getEnvironmentConfig(),
            'metadata' => $this->getMetadata(),
            'providers' => $this->resolveProviders(),
        ])->render();
    }
}

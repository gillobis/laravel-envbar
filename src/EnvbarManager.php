<?php

namespace Gillobis\Envbar;

use Illuminate\Contracts\Container\BindingResolutionException;

class EnvbarManager
{
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
        return trim(shell_exec('git rev-parse --abbrev-ref HEAD'));
    }

    public function getGitCommit(): ?string
    {
        return trim(shell_exec('git rev-parse HEAD'));
    }

    public function render(): string
    {
        // Returns the HTML of the bar with all the data
        return view('envbar::envbar', [
            'enabled' => $this->isEnabled(),
            'environment' => $this->getCurrentEnvironment(),
            'config' => $this->getEnvironmentConfig(),
            'metadata' => $this->getMetadata(),
        ])->render();
    }
}

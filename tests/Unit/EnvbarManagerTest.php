<?php

use Gillobis\Envbar\EnvbarManager;
use Gillobis\Envbar\Interfaces\EnvbarDataProvider;

it('reports enabled when environment is in the allowed list', function () {
    config(['envbar.enabled' => true]);
    config(['envbar.environments' => ['staging']]);

    $manager = app(EnvbarManager::class);

    expect($manager->isEnabled())->toBeTrue();
});

it('reports disabled when envbar.enabled is false', function () {
    config(['envbar.enabled' => false]);

    $manager = app(EnvbarManager::class);

    expect($manager->isEnabled())->toBeFalse();
});

it('reports disabled when environment is not in the allowed list', function () {
    config(['envbar.enabled' => true]);
    config(['envbar.environments' => ['local']]);
    app()->detectEnvironment(fn () => 'staging');

    $manager = app(EnvbarManager::class);

    expect($manager->isEnabled())->toBeFalse();
});

it('reports disabled in production even if listed', function () {
    config(['envbar.enabled' => true]);
    config(['envbar.environments' => ['production']]);
    app()->detectEnvironment(fn () => 'production');

    $manager = app(EnvbarManager::class);

    expect($manager->isEnabled())->toBeFalse();
});

it('returns the current environment name', function () {
    $manager = app(EnvbarManager::class);

    expect($manager->getCurrentEnvironment())->toBe('staging');
});

it('returns metadata with php_version', function () {
    $manager = app(EnvbarManager::class);
    $metadata = $manager->getMetadata();

    expect($metadata)->toHaveKeys(['git_branch', 'git_commit', 'php_version']);
    expect($metadata['php_version'])->toBe(phpversion());
});

it('renders HTML output', function () {
    config(['envbar.enabled' => true]);

    $manager = app(EnvbarManager::class);
    $html = $manager->render();

    expect($html)->toBeString();
    expect($html)->toContain('id="envbar"');
});

it('renders empty output when disabled', function () {
    config(['envbar.enabled' => false]);

    $manager = app(EnvbarManager::class);
    $html = $manager->render();

    expect($html)->not->toContain('id="envbar"');
});

// ---------------------------------------------------------------------------
// Custom data providers
// ---------------------------------------------------------------------------

it('resolves a valid provider from config', function () {
    $providerClass = new class implements EnvbarDataProvider
    {
        public function label(): string
        {
            return 'Version';
        }

        public function value(): string|array
        {
            return '1.0.0';
        }

        public function icon(): ?string
        {
            return '🚀';
        }
    };

    app()->bind('test-provider', fn () => $providerClass);
    config(['envbar.providers' => ['test-provider']]);

    $manager = app(EnvbarManager::class);
    $providers = $manager->resolveProviders();

    expect($providers)->toHaveCount(1);
    expect($providers[0]->label())->toBe('Version');
    expect($providers[0]->value())->toBe('1.0.0');
    expect($providers[0]->icon())->toBe('🚀');
});

it('resolves a provider registered programmatically', function () {
    $providerClass = new class implements EnvbarDataProvider
    {
        public function label(): string
        {
            return 'Build';
        }

        public function value(): string|array
        {
            return '42';
        }

        public function icon(): ?string
        {
            return null;
        }
    };

    app()->bind('programmatic-provider', fn () => $providerClass);

    $manager = app(EnvbarManager::class);
    $manager->registerProvider('programmatic-provider');
    $providers = $manager->resolveProviders();

    expect($providers)->toHaveCount(1);
    expect($providers[0]->label())->toBe('Build');
});

it('merges config and programmatic providers in order', function () {
    $configProvider = new class implements EnvbarDataProvider
    {
        public function label(): string
        {
            return 'Config';
        }

        public function value(): string|array
        {
            return 'from-config';
        }

        public function icon(): ?string
        {
            return null;
        }
    };

    $programmaticProvider = new class implements EnvbarDataProvider
    {
        public function label(): string
        {
            return 'Programmatic';
        }

        public function value(): string|array
        {
            return 'from-code';
        }

        public function icon(): ?string
        {
            return null;
        }
    };

    app()->bind('config-prov', fn () => $configProvider);
    app()->bind('prog-prov', fn () => $programmaticProvider);
    config(['envbar.providers' => ['config-prov']]);

    $manager = app(EnvbarManager::class);
    $manager->registerProvider('prog-prov');
    $providers = $manager->resolveProviders();

    expect($providers)->toHaveCount(2);
    expect($providers[0]->label())->toBe('Config');
    expect($providers[1]->label())->toBe('Programmatic');
});

it('skips a class that does not implement EnvbarDataProvider', function () {
    app()->bind('not-a-provider', fn () => new stdClass);
    config(['envbar.providers' => ['not-a-provider']]);

    $manager = app(EnvbarManager::class);
    $providers = $manager->resolveProviders();

    expect($providers)->toBeEmpty();
});

it('skips a class that cannot be resolved', function () {
    config(['envbar.providers' => ['App\\NonExistent\\FakeProvider']]);

    $manager = app(EnvbarManager::class);
    $providers = $manager->resolveProviders();

    expect($providers)->toBeEmpty();
});

it('handles a provider returning an array value', function () {
    $providerClass = new class implements EnvbarDataProvider
    {
        public function label(): string
        {
            return 'Tags';
        }

        public function value(): string|array
        {
            return ['v1', 'v2', 'v3'];
        }

        public function icon(): ?string
        {
            return null;
        }
    };

    app()->bind('array-provider', fn () => $providerClass);
    config(['envbar.providers' => ['array-provider']]);

    $manager = app(EnvbarManager::class);
    $providers = $manager->resolveProviders();

    expect($providers)->toHaveCount(1);
    expect($providers[0]->value())->toBe(['v1', 'v2', 'v3']);
});

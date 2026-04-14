<?php

use Gillobis\Envbar\EnvbarManager;

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

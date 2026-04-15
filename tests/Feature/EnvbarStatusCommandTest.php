<?php

it('outputs that envbar is enabled', function () {
    $this->artisan('envbar:status')
        ->expectsOutputToContain('Envbar is enabled')
        ->assertSuccessful();
});

it('outputs that envbar is disabled when globally disabled', function () {
    config(['envbar.enabled' => false]);

    $this->artisan('envbar:status')
        ->expectsOutputToContain('Envbar is disabled')
        ->assertSuccessful();
});

it('outputs the current environment', function () {
    app()->detectEnvironment(fn () => 'staging');

    $this->artisan('envbar:status')
        ->expectsOutputToContain('Current environment: staging')
        ->assertSuccessful();
});

it('outputs the metadata for the current environment', function () {
    app()->detectEnvironment(fn () => 'local');
    config(['envbar.environments_config.local' => ['label' => 'LOCAL', 'background_color' => '#6c757d']]);

    $this->artisan('envbar:status')
        ->expectsOutputToContain('LOCAL')
        ->assertSuccessful();
});

it('outputs an empty metadata object when no config exists for the environment', function () {
    app()->detectEnvironment(fn () => 'unknown-env');

    $this->artisan('envbar:status')
        ->expectsOutputToContain('Metadata: []')
        ->assertSuccessful();
});

it('outputs the allowed environments list', function () {
    config(['envbar.environments' => ['local', 'staging', 'testing']]);

    $this->artisan('envbar:status')
        ->expectsOutputToContain('local, staging, testing')
        ->assertSuccessful();
});

it('outputs the data displayed when some show options are enabled', function () {
    config(['envbar.show' => [
        'app_name' => true,
        'php_version' => true,
        'laravel_version' => false,
        'git_branch' => false,
    ]]);

    $this->artisan('envbar:status')
        ->expectsOutputToContain('app_name')
        ->assertSuccessful();

    $this->artisan('envbar:status')
        ->expectsOutputToContain('php_version')
        ->assertSuccessful();
});

it('outputs an empty data displayed list when all show options are disabled', function () {
    config(['envbar.show' => [
        'app_name' => false,
        'php_version' => false,
        'laravel_version' => false,
    ]]);

    $this->artisan('envbar:status')
        ->expectsOutputToContain('Data displayed: []')
        ->assertSuccessful();
});

it('does not include disabled show options in the data displayed output', function () {
    config(['envbar.show' => [
        'app_name' => true,
        'git_commit' => false,
        'hostname' => false,
    ]]);

    $this->artisan('envbar:status')
        ->expectsOutputToContain('app_name')
        ->doesntExpectOutputToContain('git_commit')
        ->doesntExpectOutputToContain('hostname')
        ->assertSuccessful();
});

<?php

it('outputs success when envbar is properly configured', function () {
    app()->detectEnvironment(fn () => 'staging');
    config(['envbar.enabled' => true, 'envbar.environments' => ['local', 'staging', 'testing']]);

    $this->artisan('envbar:check')
        ->expectsOutputToContain('Envbar is properly configured and can be displayed.')
        ->assertSuccessful();
});

it('outputs error when envbar is globally disabled', function () {
    config(['envbar.enabled' => false]);

    $this->artisan('envbar:check')
        ->expectsOutputToContain('Envbar is disabled in the configuration.')
        ->assertFailed();
});

it('outputs error when current environment is not in the allowed list', function () {
    app()->detectEnvironment(fn () => 'production');
    config(['envbar.enabled' => true, 'envbar.environments' => ['local', 'staging', 'testing']]);

    $this->artisan('envbar:check')
        ->expectsOutputToContain("Current environment 'production' is not in the allowed environments list.")
        ->assertFailed();
});

it('warns when production is in the allowed environments list but show.production is disabled', function () {
    app()->detectEnvironment(fn () => 'staging');
    config([
        'envbar.enabled' => true,
        'envbar.environments' => ['local', 'staging', 'production'],
        'envbar.show.production' => false,
    ]);

    $this->artisan('envbar:check')
        ->expectsOutputToContain("'envbar.show.production' config is not enabled")
        ->assertSuccessful();
});

it('warns when production is in the allowed environments and show.production is explicitly enabled', function () {
    app()->detectEnvironment(fn () => 'staging');
    config([
        'envbar.enabled' => true,
        'envbar.environments' => ['local', 'staging', 'production'],
        'envbar.show.production' => true,
    ]);

    $this->artisan('envbar:check')
        ->expectsOutputToContain('Envbar is enabled in production environment.')
        ->assertSuccessful();
});

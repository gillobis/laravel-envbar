<?php

use Gillobis\Envbar\Http\Middleware\InjectEnvbar;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(InjectEnvbar::class)
        ->get('/', fn () => '<html><head></head><body><h1>Test</h1></body></html>');
});

// ---------------------------------------------------------------------------
// Injection basics
// ---------------------------------------------------------------------------

it('injects the bar into the HTML when enabled', function () {
    $response = $this->get('/');
    $response->assertSuccessful();
    $response->assertSee('id="envbar"', false);
});

it('injects the bar before the closing body tag', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('</body>');
    expect(strpos($content, 'id="envbar"'))->toBeLessThan(strpos($content, '</body>'));
});

it('does not inject the bar when globally disabled', function () {
    config(['envbar.enabled' => false]);

    $response = $this->get('/');
    $response->assertSuccessful();
    $response->assertDontSee('id="envbar"', false);
});

it('does not inject the bar in production', function () {
    app()->detectEnvironment(fn () => 'production');
    config(['envbar.environments' => ['local', 'staging']]);

    $response = $this->get('/');
    $response->assertSuccessful();
    $response->assertDontSee('id="envbar"', false);
});

it('does not inject the bar for environments not in the allowed list', function () {
    app()->detectEnvironment(fn () => 'custom-env');
    config(['envbar.environments' => ['local', 'staging']]);

    $response = $this->get('/');
    $response->assertDontSee('id="envbar"', false);
});

it('does not inject the bar into non-HTML responses', function () {
    Route::middleware(InjectEnvbar::class)
        ->get('/api-json', fn () => response()->json(['ok' => true]));

    $response = $this->get('/api-json');
    $response->assertSuccessful();
    $response->assertDontSee('id="envbar"', false);
});

it('does not inject the bar into responses without a body tag', function () {
    Route::middleware(InjectEnvbar::class)
        ->get('/partial', fn () => '<div>Partial HTML</div>');

    $response = $this->get('/partial');
    $response->assertSuccessful();
    $response->assertDontSee('id="envbar"', false);
});

it('does not inject the bar for AJAX requests', function () {
    $response = $this->get('/', ['X-Requested-With' => 'XMLHttpRequest']);
    $response->assertSuccessful();
    $response->assertDontSee('id="envbar"', false);
});

// ---------------------------------------------------------------------------
// Environment label & appearance
// ---------------------------------------------------------------------------

it('shows the correct environment name', function () {
    $response = $this->get('/');
    $response->assertSuccessful();
    $response->assertSee('STAGING', false);
});

it('shows the correct label for each configured environment', function (string $env, string $expectedLabel) {
    app()->detectEnvironment(fn () => $env);
    config(['envbar.environments' => [$env]]);

    $response = $this->get('/');
    $response->assertSee($expectedLabel, false);
})->with([
    'local' => ['local', 'LOCAL'],
    'development' => ['development', 'DEVELOPMENT'],
    'staging' => ['staging', 'STAGING'],
    'testing' => ['testing', 'TESTING'],
]);

it('applies the correct background color from config', function () {
    config(['envbar.environments_config.staging.background_color' => '#ff5500']);

    $response = $this->get('/');
    $response->assertSee('#ff5500', false);
});

it('applies the correct text color from config', function () {
    config(['envbar.environments_config.staging.text_color' => '#00ff00']);

    $response = $this->get('/');
    $response->assertSee('#00ff00', false);
});

it('falls back to defaults for an unconfigured environment', function () {
    app()->detectEnvironment(fn () => 'preview');
    config(['envbar.environments' => ['preview']]);

    $response = $this->get('/');
    $response->assertSee('PREVIEW', false);
    $response->assertSee('#6c757d', false); // default bg
});

// ---------------------------------------------------------------------------
// Position
// ---------------------------------------------------------------------------

it('renders at the top by default', function () {
    $response = $this->get('/');
    $response->assertSee('top: 0', false);
});

it('renders at the bottom when configured', function () {
    config(['envbar.position' => 'bottom']);

    $response = $this->get('/');
    $response->assertSee('bottom: 0', false);
});

// ---------------------------------------------------------------------------
// Info segments (show options)
// ---------------------------------------------------------------------------

it('shows the app name when enabled', function () {
    config(['app.name' => 'TestApp']);
    config(['envbar.show.app_name' => true]);

    $response = $this->get('/');
    $response->assertSee('TestApp', false);
});

it('hides the app name when disabled', function () {
    config(['app.name' => 'TestApp']);
    config(['envbar.show.app_name' => false]);

    $response = $this->get('/');
    $response->assertDontSee('TestApp', false);
});

it('shows the PHP version when enabled', function () {
    config(['envbar.show.php_version' => true]);

    $response = $this->get('/');
    $response->assertSee('PHP '.phpversion(), false);
});

it('hides the PHP version when disabled', function () {
    config(['envbar.show.php_version' => false]);

    $response = $this->get('/');
    $response->assertDontSee('PHP '.phpversion(), false);
});

it('shows the Laravel version when enabled', function () {
    config(['envbar.show.laravel_version' => true]);

    $response = $this->get('/');
    $response->assertSee('Laravel '.app()->version(), false);
});

it('hides the Laravel version when disabled', function () {
    config(['envbar.show.laravel_version' => false]);

    $response = $this->get('/');
    $response->assertDontSee('Laravel '.app()->version(), false);
});

it('shows the hostname when enabled', function () {
    config(['envbar.show.hostname' => true]);

    $response = $this->get('/');
    $response->assertSee('host: '.gethostname(), false);
});

it('hides the hostname when disabled', function () {
    config(['envbar.show.hostname' => false]);

    $response = $this->get('/');
    $response->assertDontSee('host:', false);
});

// ---------------------------------------------------------------------------
// Collapsible
// ---------------------------------------------------------------------------

it('includes the collapse button when collapsible is enabled', function () {
    config(['envbar.collapsible' => true]);

    $response = $this->get('/');
    $response->assertSee('id="envbar-toggle"', false);
    $response->assertSee('id="envbar-pill"', false);
});

it('excludes the collapse button when collapsible is disabled', function () {
    config(['envbar.collapsible' => false]);

    $response = $this->get('/');
    $response->assertDontSee('id="envbar-toggle"', false);
    $response->assertDontSee('id="envbar-pill"', false);
});

// ---------------------------------------------------------------------------
// Switcher
// ---------------------------------------------------------------------------

it('does not render the switcher when disabled', function () {
    config(['envbar.switcher.enabled' => false]);

    $response = $this->get('/');
    $response->assertDontSee('Switch to', false);
});

it('renders switcher links when enabled', function () {
    config([
        'envbar.switcher.enabled' => true,
        'envbar.switcher.environments' => [
            'local' => 'http://myapp.test',
            'production' => 'https://myapp.com',
        ],
    ]);

    $response = $this->get('/');
    $response->assertSee('http://myapp.test', false);
    $response->assertSee('https://myapp.com', false);
});

it('does not render a switcher link for the current environment', function () {
    config([
        'envbar.switcher.enabled' => true,
        'envbar.switcher.environments' => [
            'staging' => 'https://staging.myapp.com',
            'local' => 'http://myapp.test',
        ],
    ]);

    $response = $this->get('/');
    // Should NOT link to staging since we ARE on staging
    $response->assertDontSee('title="Switch to staging"', false);
    // Should link to local
    $response->assertSee('title="Switch to local"', false);
});

// ---------------------------------------------------------------------------
// Favicon overlay
// ---------------------------------------------------------------------------

it('includes favicon overlay script when enabled', function () {
    config(['envbar.favicon_overlay' => true]);

    $response = $this->get('/');
    $response->assertSee('data-favicon-overlay="1"', false);
});

it('does not include favicon overlay when disabled', function () {
    config(['envbar.favicon_overlay' => false]);

    $response = $this->get('/');
    $response->assertSee('data-favicon-overlay="0"', false);
});

// ---------------------------------------------------------------------------
// Inline assets (no external dependencies)
// ---------------------------------------------------------------------------

it('contains inline styles and script', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('<style>');
    expect($content)->toContain('<script>');
    expect($content)->not->toContain('<link rel="stylesheet"');
    expect($content)->not->toContain('<script src=');
});

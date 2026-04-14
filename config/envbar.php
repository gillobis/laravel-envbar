<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global enable/disable
    |--------------------------------------------------------------------------
    | You can use an env variable: ENVBAR_ENABLED=true
    */
    'enabled' => env('ENVBAR_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Environments in which to show the bar
    |--------------------------------------------------------------------------
    | 'production' is excluded by default for safety.
    | Set to ['*'] to show in all environments.
    */
    'environments' => ['local', 'development', 'staging', 'testing'],

    /*
    |--------------------------------------------------------------------------
    | Position of the bar
    |--------------------------------------------------------------------------
    | Values: 'top' | 'bottom'
    */
    'position' => env('ENVBAR_POSITION', 'top'),

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    | 'auto' follows the browser's prefers-color-scheme
    */
    'theme' => env('ENVBAR_THEME', 'auto'), // 'light' | 'dark' | 'auto'

    /*
    |--------------------------------------------------------------------------
    | Per-environment configuration
    |--------------------------------------------------------------------------
    */
    'environments_config' => [
        'local' => [
            'label' => 'LOCAL',
            'background_color' => '#6c757d',
            'text_color' => '#ddd',
            'icon' => '💻',
        ],
        'development' => [
            'label' => 'DEVELOPMENT',
            'background_color' => '#0d6efd',
            'text_color' => '#ddd',
            'icon' => '🔧',
        ],
        'staging' => [
            'label' => 'STAGING',
            'background_color' => '#fdc700',
            'text_color' => '#111',
            'icon' => '🎪',
        ],
        'testing' => [
            'label' => 'TESTING',
            'background_color' => '#6f42c1',
            'text_color' => '#ddd',
            'icon' => '🧪',
        ],
        'production' => [
            'label' => 'PRODUCTION',
            'background_color' => '#dc3545',
            'text_color' => '#ddd',
            'icon' => '⚠️',
            // production is not shown by default,
            // but can be enabled explicitly
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Extra information shown in the bar
    |--------------------------------------------------------------------------
    */
    'show' => [
        'app_name' => true,
        'php_version' => true,
        'laravel_version' => true,
        'git_branch' => true,   // mostra il branch git corrente
        'git_commit' => false,  // mostra lo short SHA del commit
        'hostname' => false,  // nome del server
        'database' => false,  // nome del DB corrente
        'user' => false,  // utente autenticato (Auth::user()->name)
        'timestamp' => false,  // orario del server
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Switcher
    |--------------------------------------------------------------------------
    | Shows quick links to the same URLs on other configured environments.
    | Inspired by the Drupal environment_indicator module.
    */
    'switcher' => [
        'enabled' => false,
        'environments' => [
            // 'local'   => 'http://myapp.test',
            // 'staging' => 'https://staging.myapp.com',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Favicon Overlay
    |--------------------------------------------------------------------------
    | Adds a colored badge to the site's favicon.
    | Requires the browser to support canvas.
    */
    'favicon_overlay' => env('ENVBAR_FAVICON', false),

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    | Laravel Gate or null for no control (shows to everyone)
    */
    'gate' => null,
    // Example: 'gate' => 'viewEnvBar'

    /*
    |--------------------------------------------------------------------------
    | Collapsible
    |--------------------------------------------------------------------------
    | The user can minimize the bar (state saved in localStorage)
    */
    'collapsible' => true,

    /*
    |--------------------------------------------------------------------------
    | Custom data providers
    |--------------------------------------------------------------------------
    | Classes that implement EnvbarDataProvider to add custom sections
    */
    'providers' => [
        // App\Envbar\MyCustomProvider::class,
    ],

];

# Laravel Envbar

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gillobis/laravel-envbar.svg?style=flat-square)](https://packagist.org/packages/gillobis/laravel-envbar)
[![Total Downloads](https://img.shields.io/packagist/dt/gillobis/laravel-envbar.svg?style=flat-square)](https://packagist.org/packages/gillobis/laravel-envbar)
[![Tests](https://img.shields.io/github/actions/workflow/status/gillobis/laravel-envbar/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/gillobis/laravel-envbar/actions/workflows/tests.yml)
[![PHP Version](https://img.shields.io/packagist/php-v/gillobis/laravel-envbar.svg?style=flat-square)](https://packagist.org/packages/gillobis/laravel-envbar)
[![Laravel](https://img.shields.io/badge/Laravel-10%20|%2011%20|%2012-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![License](https://img.shields.io/github/license/gillobis/laravel-envbar.svg?style=flat-square)](LICENSE.md)

A visual environment indicator bar for Laravel applications. Instantly see which environment you're working on — local, staging, testing, or production — with a fully configurable, zero-dependency toolbar injected into your HTML pages.

```
┌───────────────────────────────────────────────────────────────────┐
│ ⚠️  STAGING  │ MyApp │ PHP 8.3 │ Laravel 11 │ branch: develop │ ─ │
└───────────────────────────────────────────────────────────────────┘
```

## Features

- **Zero dependencies** — Inline CSS and JS, no external frameworks required
- **Auto-injection** — Automatically injected into HTML responses via middleware
- **Per-environment colors & icons** — Customize background, text color, and icon per environment
- **Configurable segments** — Show/hide app name, PHP version, Laravel version, git branch, commit SHA, hostname, database, authenticated user, server timestamp
- **Collapsible** — Users can minimize the bar to a small pill; state persisted in `localStorage`
- **Environment switcher** — Quick links to the same URL on other environments
- **Favicon overlay** — Adds a colored badge to the browser favicon via canvas
- **Top or bottom positioning** — Fixed bar with automatic body offset
- **Gate authorization** — Restrict visibility using a Laravel Gate
- **Publishable config & views** — Full control over behavior and appearance
- **Inter font** — Uses the Inter variable font for a clean, modern look

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13

## Installation

```bash
composer require gillobis/laravel-envbar
```

The package auto-registers its service provider via Laravel's package discovery.

### Publish the configuration

```bash
php artisan vendor:publish --tag=envbar-config
```

This creates `config/envbar.php` where you can customize all options.

### Publish the views (optional)

```bash
php artisan vendor:publish --tag=envbar-views
```

## Configuration

### Basic options

```php
// config/envbar.php
return [
    'enabled'      => env('ENVBAR_ENABLED', false),
    'environments' => ['local', 'development', 'staging', 'testing'],
    'position'     => env('ENVBAR_POSITION', 'top'), // 'top' | 'bottom'
    'theme'        => env('ENVBAR_THEME', 'auto'),   // 'light' | 'dark' | 'auto'
    'collapsible'  => true,
    'gate'         => null, // e.g. 'viewEnvBar'
];
```

### Per-environment appearance

```php
'environments_config' => [
    'local' => [
        'label'            => 'LOCAL',
        'background_color' => '#6c757d',
        'text_color'       => '#ddd',
        'icon'             => '💻',
    ],
    'staging' => [
        'label'            => 'STAGING',
        'background_color' => '#fdc700',
        'text_color'       => '#111',
        'icon'             => '🎪',
    ],
    // ...
],
```

### Info segments

Toggle which information to display in the bar:

```php
'show' => [
    'app_name'        => true,
    'php_version'     => true,
    'laravel_version' => true,
    'git_branch'      => true,
    'git_commit'      => false,
    'hostname'        => false,
    'database'        => false,
    'user'            => false,
    'timestamp'       => false,
],
```

### Environment switcher

Add quick-switch links to the same page on other environments:

```php
'switcher' => [
    'enabled' => true,
    'environments' => [
        'local'   => 'http://myapp.test',
        'staging' => 'https://staging.myapp.com',
    ],
],
```

### Favicon overlay

Add a colored badge on the browser favicon:

```php
'favicon_overlay' => env('ENVBAR_FAVICON', false),
```

## Environment Variables

| Variable | Default | Description |
|---|---|---|
| `ENVBAR_ENABLED` | `false` | Enable/disable the bar globally |
| `ENVBAR_POSITION` | `top` | Bar position: `top` or `bottom` |
| `ENVBAR_THEME` | `auto` | Theme: `light`, `dark`, or `auto` |
| `ENVBAR_FAVICON` | `false` | Enable favicon overlay |

## How It Works

1. `EnvbarServiceProvider` registers the `InjectEnvbar` middleware in the `web` group
2. The middleware checks if the response is HTML and if the bar is enabled for the current environment
3. The bar HTML (with inline CSS/JS) is injected before the `</body>` tag
4. No external assets are loaded (except the Inter font from Google Fonts)

## Authorization

To restrict visibility, define a Gate and reference it in the config:

```php
// AppServiceProvider
Gate::define('viewEnvBar', function (User $user) {
    return $user->isAdmin();
});

// config/envbar.php
'gate' => 'viewEnvBar',
```

## Testing

```bash
composer test
```

## License

MIT
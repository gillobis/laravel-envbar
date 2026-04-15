<?php

namespace Gillobis\Envbar;

use Gillobis\Envbar\Commands\EnvbarStatus;
use Gillobis\Envbar\Http\Middleware\InjectEnvbar;
use Illuminate\Support\ServiceProvider;

class EnvbarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config: the values of the package as defaults, overridable by the user
        $this->mergeConfigFrom(
            __DIR__.'/../config/envbar.php', 'envbar'
        );

        $this->app->singleton(EnvbarManager::class);
        $this->app->alias(EnvbarManager::class, 'envbar');
    }

    public function boot(): void
    {
        // if not enable in config, do not register the middleware
        if (! config('envbar.enabled', true)) {
            return;
        }

        // Automatically register the middleware on the 'web' group
        $this->app->booted(function () {
            $this->app['router']->pushMiddlewareToGroup('web', InjectEnvbar::class);
        });

        // Load views from the package (namespace 'envbar')
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'envbar');

        // Make config and views publishable
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/envbar.php' => config_path('envbar.php'),
            ], 'envbar-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/envbar'),
            ], 'envbar-views');

            // Register the artisan command
            $this->commands([
                EnvbarStatus::class,
                // NetworkCommand::class,
            ]);
        }
    }
}

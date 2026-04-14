<?php

namespace Gillobis\Envbar\Tests;

use Gillobis\Envbar\EnvbarServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [EnvbarServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('app.env', 'staging');
        $app['env'] = 'staging';
        $app['config']->set('envbar.enabled', true);
    }
}

<?php

namespace Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use LaravelGoogleDrive\Infra\Providers\LaravelGoogleDriveServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use Fixtures;

    protected function getPackageProviders(mixed $app): array
    {
        return [
            LaravelGoogleDriveServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        parent::getEnvironmentSetUp($app);
    }

    protected function instance(mixed $abstract, mixed $instance): object
    {
        $this->app->bind(
            $abstract,
            function () use ($instance) {
                return $instance;
            }
        );

        return $instance;
    }
}

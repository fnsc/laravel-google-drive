<?php

namespace Tests;

use LaravelGoogleDrive\Infra\Providers\LaravelGoogleDriveServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders(mixed $app): array
    {
        return [LaravelGoogleDriveServiceProvider::class];
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

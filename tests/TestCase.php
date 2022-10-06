<?php

namespace Tests;

use LaravelGoogleDrive\Infra\GoogleDriveServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders(mixed $app): array
    {
        return [GoogleDriveServiceProvider::class];
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

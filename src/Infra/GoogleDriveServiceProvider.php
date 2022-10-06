<?php

namespace LaravelGoogleDrive\Infra;

use Illuminate\Support\ServiceProvider;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/google_drive.php' => config_path(
                'google_drive.php'
            ),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/google_drive.php',
            'google_drive'
        );
    }

    public function register(): void
    {
        $this->app->bind('googleDrive', function ($app) {
            return $app->make();
        });
    }
}

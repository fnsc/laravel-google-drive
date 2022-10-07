<?php

namespace LaravelGoogleDrive\Infra\Providers;

use Google\Service\Drive;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use LaravelGoogleDrive\Application\GoogleDriveService;
use LaravelGoogleDrive\Infra\Adapters\GoogleDrive;

class LaravelGoogleDriveServiceProvider extends ServiceProvider
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
        $this->app->bind(
            Google_Service_Drive::class,
            function (Application $app) {
                return $app->make(Google_Client::class);
            }
        );

        $this->app->bind(Google_Client::class, function (Config $config) {
            $client = new Google_Client();
            $client->setClientId(
                $config->get('google_drive.credentials.clientId')
            );
            $client->setClientSecret(
                $config->get('google_drive.credentials.clientSecret')
            );
            $client->refreshToken(
                $config->get('google_drive.credentials.refreshToken')
            );
            $client->addScope(Drive::DRIVE);
            $token = $client->fetchAccessTokenWithRefreshToken();
            $client->setAccessToken($token);

            return $client;
        });

        $this->app->bind(
            GoogleDriveContract::class,
            function (Application $application) {
                $service = $application->make(Google_Service_Drive::class);

                return new GoogleDrive($service);
            }
        );

        $this->app->bind('googleDrive', function (Application $app) {
            return $app->make(GoogleDriveService::class);
        });
    }
}

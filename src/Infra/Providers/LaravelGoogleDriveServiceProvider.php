<?php

namespace LaravelGoogleDrive\Infra\Providers;

use Google\Service\Drive;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use LaravelGoogleDrive\Application\GoogleDriveService;
use LaravelGoogleDrive\Infra\Adapters\GoogleDrive;

class LaravelGoogleDriveServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../../../config/google_drive.php' => config_path(
                    'google_drive.php'
                ),
            ],
            'config'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/google_drive.php',
            'google_drive'
        );
    }

    public function register(): void
    {
        $this->app->bind(Google_Client::class, function () {
            $client = new Google_Client();
            $client->addScope(Drive::DRIVE);

            $credentials = $this->getCredentials();

            $client->setAuthConfig($credentials);

            return $client;
        });

        $this->app->bind(
            Google_Service_Drive::class,
            function (Application $app) {
                $client = $app->make(Google_Client::class);
                $googleServiceDrive = new Google_Service_Drive($client);
                $googleServiceDrive->servicePath = config(
                    'google_drive.folder_id'
                );

                return $googleServiceDrive;
            }
        );

        $this->app->bind(
            GoogleDriveContract::class,
            function (Application $application) {
                $service = $application->make(Google_Service_Drive::class);
                $config = $application->make(Repository::class);

                return new GoogleDrive($service, $config);
            }
        );

        $this->app->bind('googleDrive', function (Application $application) {
            return $application->make(GoogleDriveService::class);
        });
    }

    /**
     * @return array<int,string>
     */
    public function provides(): array
    {
        return [
            Google_Service_Drive::class,
            Google_Client::class,
            GoogleDriveContract::class,
        ];
    }

    /**
     * @return array<string,string>
     */
    private function getCredentials(): array
    {
        $credentialsFilePath = config(
            'google_drive.credentials.service_account'
        );
        $credentialsFileContent = file_get_contents(
            $credentialsFilePath
        ) ?: '';

        return json_decode($credentialsFileContent, true) ?: [];
    }
}

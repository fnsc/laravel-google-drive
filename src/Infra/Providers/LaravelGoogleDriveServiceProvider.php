<?php

namespace LaravelGoogleDrive\Infra\Providers;

use Google\Service\Drive;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use LaravelGoogleDrive\Application\Ports\ConfigContract;
use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Exceptions\CredentialException;
use LaravelGoogleDrive\Infra\Adapters\Config;
use LaravelGoogleDrive\Infra\Adapters\GoogleDrive as GoogleDriveAdapter;
use LaravelGoogleDrive\Infra\Handlers\GoogleDrive as GoogleDriveHandler;

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
        $this->registerGoogleClient();
        $this->registerGoogleServiceDrive();
        $this->registerGoogleDriveAdapter();
        $this->registerConfigAdapter();
        $this->registerGoogleDriveHandler();
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
            ConfigContract::class,
            GoogleDriveHandler::class,
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

        if (empty($credentialsFilePath)) {
            throw new CredentialException(
                'Credential data not found. Please check the GOOGLE_APPLICATION_CREDENTIALS env variable.'
            );
        }

        $credentialsFileContent = file_get_contents($credentialsFilePath);

        return json_decode($credentialsFileContent ?: '', true) ?: [];
    }

    private function registerGoogleClient(): void
    {
        $this->app->bind(Google_Client::class, function () {
            $client = new Google_Client();
            $client->addScope(Drive::DRIVE);
            $credentials = $this->getCredentials();

            if (empty($credentials)) {
                throw new CredentialException(
                    'Credential data not found. Please check the service account file content.'
                );
            }

            $client->setAuthConfig($credentials);

            return $client;
        });
    }

    private function registerGoogleServiceDrive(): void
    {
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
    }

    private function registerGoogleDriveAdapter(): void
    {
        $this->app->bind(
            GoogleDriveContract::class,
            function (Application $application) {
                $service = $application->make(Google_Service_Drive::class);

                return new GoogleDriveAdapter($service);
            }
        );
    }

    private function registerGoogleDriveHandler(): void
    {
        $this->app->bind('googleDrive', function (Application $application) {
            return $application->make(GoogleDriveHandler::class);
        });
    }

    private function registerConfigAdapter(): void
    {
        $this->app->bind(ConfigContract::class, function () {
            $config = $this->app->make(Repository::class);

            return new Config($config);
        });
    }
}

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
use LaravelGoogleDrive\Domain\Exceptions\CredentialException;
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
        $this->registerGoogleClient();
        $this->registerGoogleServiceDrive();
        $this->registerGoogleDriveAdapter();
        $this->registerGoogleDriveService();
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
     * @throws CredentialException
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    private function registerGoogleDriveAdapter(): void
    {
        $this->app->bind(
            GoogleDriveContract::class,
            function (Application $application) {
                $service = $application->make(Google_Service_Drive::class);
                $config = $application->make(Repository::class);

                return new GoogleDrive($service, $config);
            }
        );
    }

    /**
     * @return void
     */
    private function registerGoogleDriveService(): void
    {
        $this->app->bind('googleDrive', function (Application $application) {
            return $application->make(GoogleDriveService::class);
        });
    }
}

<?php

namespace LaravelGoogleDrive\Infra\Providers;

use Google_Client;
use Google_Service_Drive;
use Illuminate\Foundation\Application;
use LaravelGoogleDrive\Application\GoogleDriveService;
use LaravelGoogleDrive\Domain\Exceptions\CredentialException;
use LaravelGoogleDrive\Infra\Adapters\GoogleDrive;
use Mockery as m;
use Tests\TestCase;

class LaravelGoogleDriveServiceProviderTest extends TestCase
{
    public function testShouldRegister(): void
    {
        // Set
        $credentialFileContent = $this->getFixture('service-account.json');
        config(
            ['google_drive.credentials.service_account' => $credentialFileContent]
        );

        // Action
        $googleClient = $this->app->make(Google_Client::class);
        $googleServiceDrive = $this->app->make(Google_Service_Drive::class);
        $googleAdapter = $this->app->make(GoogleDrive::class);
        $service = $this->app->make('googleDrive');

        // Assertions
        $this->assertInstanceOf(Google_Client::class, $googleClient);
        $this->assertInstanceOf(
            Google_Service_Drive::class,
            $googleServiceDrive
        );
        $this->assertInstanceOf(GoogleDrive::class, $googleAdapter);
        $this->assertInstanceOf(GoogleDriveService::class, $service);
    }

    public function testShouldThrowAnExceptionWhenCredentialsNotDefined(): void
    {
        // Set
        config(['google_drive.credentials.service_account' => '']);

        // Expectations
        $this->expectException(CredentialException::class);
        $this->expectExceptionMessage(
            'Credential data not found. Please check the GOOGLE_APPLICATION_CREDENTIALS env variable.'
        );

        // Action
        $this->app->make(Google_Client::class);
    }

    public function testShouldThrowAnExceptionWhenCredentialsFileIsEmpty(): void
    {
        // Set
        $credentialFileContent = $this->getFixture(
            'empty-service-account.json'
        );
        config(
            ['google_drive.credentials.service_account' => $credentialFileContent]
        );

        // Expectations
        $this->expectException(CredentialException::class);
        $this->expectExceptionMessage(
            'Credential data not found. Please check the service account file content.'
        );

        // Action
        $this->app->make(Google_Client::class);
    }

    public function testShouldReturnTheProvidedClasses(): void
    {
        // Set
        $app = m::mock(Application::class);
        /** @phpstan-ignore-next-line  */
        $service = new LaravelGoogleDriveServiceProvider($app);
        $expected = [
            'Google_Service_Drive',
            'Google_Client',
            'LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract',
        ];

        // Action
        $result = $service->provides();

        // Assertions
        $this->assertSame($expected, $result);
    }
}
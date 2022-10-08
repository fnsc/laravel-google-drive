<?php

namespace Tests\Feature\src\Application;

use LaravelGoogleDrive\Application\GoogleDriveService;
use Symfony\Component\HttpFoundation\File\File;
use Tests\TestCase;

class GoogleDriveServiceTest extends TestCase
{
    public function testShouldUploadTheGivenFile(): void
    {
        // Set
        $googleDriveService = $this->app->make(GoogleDriveService::class);
        $file = new File(__DIR__ . '/../../../fixtures/file.txt');

        // Expectations

        // Action
        $result = $googleDriveService->upload($file);

        dd($result);

        // Assertions
    }
}
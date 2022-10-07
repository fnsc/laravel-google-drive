<?php

namespace LaravelGoogleDrive\Application;

use Exception;
use Google\Service\Drive\DriveFile;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;

class GoogleDriveServiceTest extends TestCase
{
    public function testShouldUploadTheGivenFile(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        $logger = m::mock(LoggerInterface::class);
        /** @phpstan-ignore-next-line  */
        $googleDriveService = new GoogleDriveService($googleDrive, $logger);
        $file = m::mock(File::class);

        // Expectations
        /** @phpstan-ignore-next-line  */
        $googleDrive->expects()
            ->upload($file)
            ->andReturn(m::mock(DriveFile::class));

        // Action
        /** @phpstan-ignore-next-line  */
        $result = $googleDriveService->upload($file);

        // Assertions
        $this->assertInstanceOf(DriveFile::class, $result);
    }

    public function testShouldReturnFalseWhenTheFileUploadFails(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        $logger = m::mock(LoggerInterface::class);
        /** @phpstan-ignore-next-line  */
        $googleDriveService = new GoogleDriveService($googleDrive, $logger);
        $file = m::mock(File::class);
        $exception = new Exception('Something went wrong.');

        // Expectations
        /** @phpstan-ignore-next-line  */
        $googleDrive->expects()
            ->upload($file)
            ->andThrow($exception);

        /** @phpstan-ignore-next-line  */
        $logger->expects()
            ->warning(
                '[LaravelGoogleDrive|Upload] Something went wrong while we are uploading your file',
                ['exception' => $exception]
            );

        // Action
        /** @phpstan-ignore-next-line  */
        $result = $googleDriveService->upload($file);

        // Assertions
        $this->assertFalse($result);
    }
}

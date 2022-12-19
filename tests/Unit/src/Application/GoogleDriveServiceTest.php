<?php

namespace LaravelGoogleDrive\Application;

use Exception;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Exceptions\FolderIdException;
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
        $googleDriveFile = new GoogleDriveFile(
            fileId: '639fa51de807c624220da745',
            folderId: '639fa51de807c624220da746'
        );

        // Expectations
        /** @phpstan-ignore-next-line  */
        $googleDrive->expects()
            ->upload($file, '')
            ->andReturn($googleDriveFile);

        // Action
        /** @phpstan-ignore-next-line  */
        $result = $googleDriveService->upload($file);

        // Assertions
        $this->assertInstanceOf(GoogleDriveFile::class, $result);
    }

    /**
     * @dataProvider getExceptionsScenarios
     */
    public function testShouldReturnFalseWhenTheFileUploadFails(Exception $exception, string $logMessage): void
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
            ->upload($file, '')
            ->andThrow($exception);

        /** @phpstan-ignore-next-line  */
        $logger->expects()
            ->warning(
                $logMessage,
                ['exception' => $exception]
            );

        // Action
        /** @phpstan-ignore-next-line  */
        $result = $googleDriveService->upload($file);

        // Assertions
        $this->assertFalse($result);
    }

    /**
     * @return array<mixed>
     */
    public function getExceptionsScenarios(): array
    {
        return [
            'folder id empty' => [
                'exception' => new FolderIdException(),
                'log_message' => '[LaravelGoogleDrive|Upload] Folder id is empty. Please check GOOGLE_DRIVE_FOLDER_ID env variable or send the folderId as a param.',
            ],
            'unexpected exception' => [
                'exception' => new Exception('Something went wrong.'),
                'log_message' => '[LaravelGoogleDrive|Upload] Something went wrong while we are uploading your file',
            ],
        ];
    }
}

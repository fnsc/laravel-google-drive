<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class GoogleDriveServiceTest extends TestCase
{
    public function testShouldUploadTheGivenFile(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        /** @phpstan-ignore-next-line  */
        $googleDriveService = new GoogleDriveService($googleDrive);
        $file = m::mock(File::class);
        $googleDriveFile = new GoogleDriveFileData(
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
        $this->assertInstanceOf(GoogleDriveFileData::class, $result);
    }
}

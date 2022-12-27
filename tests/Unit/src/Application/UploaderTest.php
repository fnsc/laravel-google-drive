<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Ports\ConfigContract;
use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Exceptions\FolderIdException;
use Mockery as m;
use Tests\LeanTestCase;

class UploaderTest extends LeanTestCase
{
    public function testShouldUploadTheGivenFile(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        $config = m::mock(ConfigContract::class);
        /** @phpstan-ignore-next-line  */
        $googleDriveService = new Uploader($googleDrive, $config);
        $file = new GoogleDriveFile(
            'file.txt',
            'hello world!',
            'application/octet-stream'
        );
        $googleDriveFile = new GoogleDriveFileData(
            fileId: '639fa51de807c624220da745',
            folderId: '639fa51de807c624220da746'
        );

        // Expectations
        /** @phpstan-ignore-next-line  */
        $googleDrive->expects()
            ->upload($file, '639fa51de807c624220da746')
            ->andReturn($googleDriveFile);

        // Action
        $result = $googleDriveService->upload(
            $file,
            '639fa51de807c624220da746'
        );

        // Assertions
        $this->assertInstanceOf(GoogleDriveFileData::class, $result);
    }

    public function testShouldThrowAnExceptionWhenFolderIdIsNotSentAsParamOrNotDefinedOnConfigFile(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        $config = m::mock(ConfigContract::class);
        /** @phpstan-ignore-next-line  */
        $googleDriveService = new Uploader($googleDrive, $config);
        $file = new GoogleDriveFile(
            'file.txt',
            'hello world!',
            'application/octet-stream'
        );

        // Expectations
        /** @phpstan-ignore-next-line  */
        $config->expects()
            ->get('google_drive.folder_id', '')
            ->andReturn('');

        $this->expectException(FolderIdException::class);
        $this->expectExceptionMessage(
            'The folder_id is empty. Please check GOOGLE_DRIVE_FOLDER_ID env variable or send the folderId as a param.'
        );

        // Action
        $googleDriveService->upload($file, '');
    }
}

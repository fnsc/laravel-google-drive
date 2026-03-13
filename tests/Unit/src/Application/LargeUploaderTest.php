<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Ports\ConfigContract;
use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Entities\LargeGoogleDriveFile;
use LaravelGoogleDrive\Domain\Exceptions\FolderIdException;
use Mockery as m;
use Tests\LeanTestCase;

class LargeUploaderTest extends LeanTestCase
{
    public function testShouldUploadLargeFile(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        $config = m::mock(ConfigContract::class);
        /** @phpstan-ignore-next-line  */
        $largeUploader = new LargeUploader($googleDrive, $config);

        $file = new LargeGoogleDriveFile(
            name: 'file.txt',
            filePath: '/tmp/file.txt',
            mimeType: 'text/plain',
        );

        $fileData = new GoogleDriveFileData(
            fileId: '639fa51de807c624220da745',
            fileName: 'file.txt',
            folderId: '639fa51de807c624220da746'
        );

        // Expectations
        /** @phpstan-ignore-next-line  */
        $googleDrive->expects()
            ->uploadResumable($file, '639fa51de807c624220da746', 1048576)
            ->andReturn($fileData);

        // Action
        $result = $largeUploader->upload(
            $file,
            '639fa51de807c624220da746',
            1048576
        );

        // Assertions
        $this->assertInstanceOf(GoogleDriveFileData::class, $result);
        $this->assertSame('639fa51de807c624220da745', $result->getFileId());
        $this->assertSame('639fa51de807c624220da746', $result->getFolderId());
    }

    public function testShouldResolveDefaultFolderIdFromConfig(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        $config = m::mock(ConfigContract::class);
        /** @phpstan-ignore-next-line  */
        $largeUploader = new LargeUploader($googleDrive, $config);

        $file = new LargeGoogleDriveFile(
            name: 'file.txt',
            filePath: '/tmp/file.txt',
            mimeType: 'text/plain',
        );

        $fileData = new GoogleDriveFileData(
            fileId: '639fa51de807c624220da745',
            fileName: 'file.txt',
            folderId: '639fa51de807c624220da746'
        );

        // Expectations
        /** @phpstan-ignore-next-line  */
        $config->expects()
            ->get('google_drive.folder_id', '')
            ->andReturn('639fa51de807c624220da746');

        /** @phpstan-ignore-next-line  */
        $googleDrive->expects()
            ->uploadResumable($file, '639fa51de807c624220da746', 1048576)
            ->andReturn($fileData);

        // Action
        $result = $largeUploader->upload($file, '', 1048576);

        // Assertions
        $this->assertInstanceOf(GoogleDriveFileData::class, $result);
        $this->assertSame('639fa51de807c624220da745', $result->getFileId());
    }

    public function testShouldThrowAnExceptionWhenFolderIdIsNotSentAsParamOrNotDefinedOnConfigFile(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        $config = m::mock(ConfigContract::class);
        /** @phpstan-ignore-next-line  */
        $largeUploader = new LargeUploader($googleDrive, $config);

        $file = new LargeGoogleDriveFile(
            name: 'file.txt',
            filePath: '/tmp/file.txt',
            mimeType: 'text/plain',
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
        $largeUploader->upload($file, '', 1048576);
    }
}

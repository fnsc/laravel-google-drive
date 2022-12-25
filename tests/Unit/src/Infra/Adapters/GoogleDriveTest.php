<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Resource\Files;
use Google_Service_Drive;
use Illuminate\Config\Repository;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Exceptions\FolderIdException;
use Mockery as m;
use Symfony\Component\HttpFoundation\File\File;
use Tests\LeanTestCase;

class GoogleDriveTest extends LeanTestCase
{
    public function testShouldUploadTheGivenFileToGoogleDrive(): void
    {
        // Set
        $resourceFiles = $this->createMock(Files::class);
        $googleServiceDrive = m::mock(Google_Service_Drive::class);
        /** @phpstan-ignore-next-line  */
        $googleServiceDrive->files = $resourceFiles;
        $config = m::mock(Repository::class);
        /** @phpstan-ignore-next-line  */
        $adapter = new GoogleDrive($googleServiceDrive, $config);
        $file = new File($this->getFixture('file.txt'));

        $googleDriveFile = new DriveFile([
            'name' => 'file.txt',
            'parents' => ['639fe1f53289654a020e8dd8'],
        ]);

        $uploadedGoogleDriveFile = m::mock(DriveFile::class);

        // Expectations
        /** @phpstan-ignore-next-line  */
        $config->expects()
            ->get('google_drive.folder_id', '')
            ->andReturn('639fe1f53289654a020e8dd8');

        $resourceFiles->expects($this->once())
            ->method('create')
            ->with($googleDriveFile, [
                'data' => $file->getContent(),
                'uploadType' => 'multipart',
                'fields' => 'id',
            ])->willReturn($uploadedGoogleDriveFile);

        /** @phpstan-ignore-next-line  */
        $uploadedGoogleDriveFile->expects()
            ->getId()
            ->andReturn('639fe3a43289654a020e8dd9');

        // Action
        $result = $adapter->upload($file, '');

        // Assertions
        $this->assertInstanceOf(GoogleDriveFileData::class, $result);
        $this->assertSame('639fe3a43289654a020e8dd9', $result->getFileId());
        $this->assertSame('639fe1f53289654a020e8dd8', $result->getFolderId());
    }

    public function testShouldThrowTheFolderIdException(): void
    {
        // Set
        $resourceFiles = $this->createMock(Files::class);
        $googleServiceDrive = m::mock(Google_Service_Drive::class);
        /** @phpstan-ignore-next-line  */
        $googleServiceDrive->files = $resourceFiles;
        $config = m::mock(Repository::class);
        /** @phpstan-ignore-next-line  */
        $adapter = new GoogleDrive($googleServiceDrive, $config);
        $file = new File($this->getFixture('file.txt'));

        // Expectations
        /** @phpstan-ignore-next-line  */
        $config->expects()
            ->get('google_drive.folder_id', '')
            ->andReturn('');

        $this->expectException(FolderIdException::class);
        $this->expectExceptionMessage(
            'The folderId is empty. Please check GOOGLE_DRIVE_FOLDER_ID env variable or send the folderId as a param.'
        );

        // Action
        $adapter->upload($file, '');
    }
}

<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Resource\Files;
use Google_Service_Drive;
use GuzzleHttp\Psr7\Response;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use Mockery as m;
use Psr\Http\Message\StreamInterface;
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
        /** @phpstan-ignore-next-line  */
        $adapter = new GoogleDrive($googleServiceDrive);
        $file = new GoogleDriveFile(
            'file.txt',
            'hello world!',
            'application/octet-stream'
        );

        $googleDriveFile = new DriveFile([
            'name' => 'file.txt',
            'parents' => ['639fe1f53289654a020e8dd8'],
        ]);

        $uploadedGoogleDriveFile = m::mock(DriveFile::class);

        // Expectations
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
        $result = $adapter->upload($file, '639fe1f53289654a020e8dd8');

        // Assertions
        $this->assertInstanceOf(GoogleDriveFileData::class, $result);
        $this->assertSame('639fe3a43289654a020e8dd9', $result->getFileId());
        $this->assertSame('639fe1f53289654a020e8dd8', $result->getFolderId());
    }

    public function testShouldGetTheRequestedFileFromGoogleDrive(): void
    {
        // Set
        $resourceFiles = $this->createMock(Files::class);
        $googleServiceDrive = m::mock(Google_Service_Drive::class);
        /** @phpstan-ignore-next-line  */
        $googleServiceDrive->files = $resourceFiles;
        /** @phpstan-ignore-next-line  */
        $adapter = new GoogleDrive($googleServiceDrive);

        $response = m::mock(Response::class);
        $stream = m::mock(StreamInterface::class);

        // Expectations
        $resourceFiles->expects($this->once())
            ->method('get')
            ->with('1mruyEYrkh2KF2ndK_8xAjHlDD44uQMa1', [
                'fields' => 'name,size,id',
                'alt' => 'media',
            ])->willReturn($response);

        /** @phpstan-ignore-next-line  */
        $response->expects()
            ->getBody()
            ->andReturn($stream);

        /** @phpstan-ignore-next-line  */
        $response->expects()
            ->getHeader('Content-Type')
            ->andReturn(['application/octet-stream']);

        /** @phpstan-ignore-next-line  */
        $stream->expects()
            ->getContents()
            ->andReturn('hello world!!!');

        // Action
        $result = $adapter->get(
            'file.txt',
            '1mruyEYrkh2KF2ndK_8xAjHlDD44uQMa1'
        );

        // Assertions
        $this->assertInstanceOf(GoogleDriveFile::class, $result);
        $this->assertSame('file.txt', $result->getName());
        $this->assertSame('txt', $result->getExtension());
        $this->assertSame('hello world!!!', $result->getContent());
    }
}

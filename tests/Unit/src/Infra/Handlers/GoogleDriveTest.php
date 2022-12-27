<?php

namespace LaravelGoogleDrive\Infra\Handlers;

use Illuminate\Http\UploadedFile;
use LaravelGoogleDrive\Application\Getter;
use LaravelGoogleDrive\Application\Uploader;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use Mockery as m;
use Tests\LeanTestCase;

class GoogleDriveTest extends LeanTestCase
{
    public function testShouldUploadTheGivenFile(): void
    {
        // Set
        $uploader = $this->createMock(Uploader::class);
        $getter = m::mock(Getter::class);
        /** @phpstan-ignore-next-line  */
        $handler = new GoogleDrive($uploader, $getter);

        $uploadedFile = new UploadedFile(
            $this->getFixture('file.txt'),
            'file.txt'
        );
        $file = new GoogleDriveFile(
            name: $uploadedFile->getClientOriginalName(),
            content: $uploadedFile->getContent(),
            mimeType: $uploadedFile->getMimeType() ?? 'application/octet-stream'
        );

        $fileData = new GoogleDriveFileData(
            fileId: '63ab4f34fecd335a6c043104',
            folderId: '63ab4f34fecd335a6c043105'
        );

        // Expectations
        $uploader->expects($this->once())
            ->method('upload')
            ->with($file)
            ->willReturn($fileData);

        // Action
        $result = $handler->upload($uploadedFile);

        // Assertions
        $this->assertInstanceOf(GoogleDriveFileData::class, $result);
        $this->assertSame('63ab4f34fecd335a6c043104', $result->getFileId());
        $this->assertSame('63ab4f34fecd335a6c043105', $result->getFolderId());
    }

    public function testShouldGetTheRequestedFile(): void
    {
        // Set
        $uploader = m::mock(Uploader::class);
        $getter = m::mock(Getter::class);
        /** @phpstan-ignore-next-line  */
        $handler = new GoogleDrive($uploader, $getter);

        $file = new GoogleDriveFile(
            name: 'file.txt',
            content: 'hello world!!!',
            mimeType: 'application/octet-stream'
        );

        // Expectations
        /** @phpstan-ignore-next-line  */
        $getter->expects()
            ->get('file.txt', '63ab4f34fecd335a6c043104')
            ->andReturn($file);

        // Action
        $result = $handler->get('file.txt', '63ab4f34fecd335a6c043104');

        // Assertions
        $this->assertInstanceOf(GoogleDriveFile::class, $result);
    }
}

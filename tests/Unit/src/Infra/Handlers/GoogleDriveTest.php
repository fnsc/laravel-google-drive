<?php

namespace LaravelGoogleDrive\Infra\Handlers;

use Illuminate\Http\UploadedFile;
use LaravelGoogleDrive\Application\Getter;
use LaravelGoogleDrive\Application\Uploader;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Exceptions\InvalidDataProvidedException;
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
            fileName: 'file.txt',
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
        $this->assertSame('file.txt', $result->getFileName());
    }

    public function testShouldUploadMoreThanOneFile(): void
    {
        // Set
        $uploader = $this->createMock(Uploader::class);
        $getter = m::mock(Getter::class);
        /** @phpstan-ignore-next-line  */
        $handler = new GoogleDrive($uploader, $getter);

        $uploadedFile1 = new UploadedFile(
            $this->getFixture('file.txt'),
            'file.txt',
            'text/plain'
        );

        $uploadedFile2 = new UploadedFile(
            $this->getFixture('test.jpeg'),
            'test.jpeg',
            'image/jpeg'
        );

        $uploadedFiles = [
            $uploadedFile1,
            $uploadedFile2,
        ];

        $fileData1 = new GoogleDriveFileData(
            fileId: '63ab4f34fecd335a6c043104',
            fileName: 'file.txt',
            folderId: '63ab4f34fecd335a6c043105'
        );

        $fileData2 = new GoogleDriveFileData(
            fileId: '64a3816f4c60b3fa83089850',
            fileName: 'test.jpeg',
            folderId: '63ab4f34fecd335a6c043105'
        );

        // Expectations
        $uploader->expects($this->exactly(2))
            ->method('upload')
            ->willReturnOnConsecutiveCalls($fileData1, $fileData2);

        // Action
        $result = $handler->uploadMany($uploadedFiles);

        // Assertions
        $this->assertInstanceOf(GoogleDriveFileData::class, $result[0]);
        $this->assertSame('63ab4f34fecd335a6c043104', $result[0]->getFileId());
        $this->assertSame(
            '63ab4f34fecd335a6c043105',
            $result[0]->getFolderId()
        );
        $this->assertSame('64a3816f4c60b3fa83089850', $result[1]->getFileId());
        $this->assertSame(
            '63ab4f34fecd335a6c043105',
            $result[1]->getFolderId()
        );
    }

    public function testShouldThrowAnExceptionWhenTheGivenDataIsInvalid(): void
    {
        // Set
        $uploader = m::mock(Uploader::class);
        $getter = m::mock(Getter::class);
        /** @phpstan-ignore-next-line  */
        $handler = new GoogleDrive($uploader, $getter);

        $uploadedFiles = [
            'invalid data',
        ];

        // Expectations
        $this->expectException(InvalidDataProvidedException::class);
        $this->expectExceptionMessage(
            'Invalid data type. The provided input is not an instance of UploadedFile.'
        );

        // Action
        /** @phpstan-ignore-next-line  */
        $handler->uploadMany($uploadedFiles);
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

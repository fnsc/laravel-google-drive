<?php

namespace LaravelGoogleDrive\Domain\Entities;

use Tests\LeanTestCase;

class GoogleDriveFileTest extends LeanTestCase
{
    public function testShouldGetAnInstance(): void
    {
        // Action
        $result = new GoogleDriveFile(
            'file.txt',
            'hello world!!!',
            'application/octet-stream',
            '63ab4f34fecd335a6c043105'
        );

        // Assertions
        $this->assertSame('txt', $result->getExtension());
        $this->assertSame('file.txt', $result->getName());
        $this->assertSame('hello world!!!', $result->getContent());
        $this->assertSame('application/octet-stream', $result->getMimeType());
        $this->assertSame('63ab4f34fecd335a6c043105', $result->getFileId());
    }
}

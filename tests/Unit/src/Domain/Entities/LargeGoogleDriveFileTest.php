<?php

namespace LaravelGoogleDrive\Domain\Entities;

use Tests\LeanTestCase;

class LargeGoogleDriveFileTest extends LeanTestCase
{
    public function testShouldGetAnInstance(): void
    {
        // Action
        $result = new LargeGoogleDriveFile(
            name: 'file.txt',
            filePath: '/tmp/file.txt',
            mimeType: 'text/plain',
        );

        // Assertions
        $this->assertSame('file.txt', $result->getName());
        $this->assertSame('/tmp/file.txt', $result->getFilePath());
        $this->assertSame('text/plain', $result->getMimeType());
    }
}

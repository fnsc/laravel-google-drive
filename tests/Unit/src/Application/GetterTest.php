<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use Mockery as m;
use Tests\LeanTestCase;

class GetterTest extends LeanTestCase
{
    public function testShouldGetTheRequestedFile(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        /** @phpstan-ignore-next-line */
        $getter = new Getter($googleDrive);

        $file = new GoogleDriveFile(
            name: 'file.txt',
            content: 'hello world!!!',
            mimeType: 'application/octet-stream'
        );

        // Expectations
        /** @phpstan-ignore-next-line  */
        $googleDrive->expects()
            ->get('file.txt', '63ab4f34fecd335a6c043105')
            ->andReturn($file);

        // Action
        $result = $getter->get('file.txt', '63ab4f34fecd335a6c043105');

        // Assertions
        $this->assertInstanceOf(GoogleDriveFile::class, $result);
    }
}

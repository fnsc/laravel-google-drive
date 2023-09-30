<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use Mockery as m;
use Tests\LeanTestCase;

class DeleterTest extends LeanTestCase
{
    public function testShouldGetTheRequestedFile(): void
    {
        // Set
        $googleDrive = m::mock(GoogleDriveContract::class);
        /** @phpstan-ignore-next-line */
        $deleter = new Deleter($googleDrive);

        // Expectations
        /** @phpstan-ignore-next-line  */
        $googleDrive->expects()
            ->delete('63ab4f34fecd335a6c043105')
            ->andReturnTrue();

        // Action
        $result = $deleter->delete('63ab4f34fecd335a6c043105');

        // Assertions
        $this->assertTrue($result);
    }
}

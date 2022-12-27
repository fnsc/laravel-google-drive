<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Illuminate\Config\Repository;
use Mockery as m;
use Tests\LeanTestCase;

class ConfigTest extends LeanTestCase
{
    public function testShouldGetTheRequestedConfig(): void
    {
        // Set
        $repository = m::mock(Repository::class);
        /** @phpstan-ignore-next-line  */
        $config = new Config($repository);

        // Expectations
        /** @phpstan-ignore-next-line  */
        $repository->expects()
            ->get('some.config', null)
            ->andReturn('random_config');

        // Action
        $result = $config->get('some.config');

        // Assertions
        $this->assertSame('random_config', $result);
    }
}

<?php

namespace Tests;

trait Fixtures
{
    public function getFixture(string $fileName): string
    {
        return __DIR__ . '/fixtures/' . $fileName;
    }
}

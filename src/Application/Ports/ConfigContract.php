<?php

namespace LaravelGoogleDrive\Application\Ports;

interface ConfigContract
{
    public function get(string $config, mixed $default = null): mixed;
}

<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Illuminate\Config\Repository;
use LaravelGoogleDrive\Application\Ports\ConfigContract;

class Config implements ConfigContract
{
    public function __construct(private readonly Repository $config)
    {
    }

    public function get(string $config, mixed $default = null): mixed
    {
        return $this->config->get($config, $default);
    }
}

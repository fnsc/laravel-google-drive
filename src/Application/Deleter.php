<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;

class Deleter
{
    public function __construct(private readonly GoogleDriveContract $googleDrive)
    {
    }

    public function delete(string $fileId): bool
    {
        return $this->googleDrive->delete($fileId);
    }
}

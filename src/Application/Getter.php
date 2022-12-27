<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;

class Getter
{
    public function __construct(
        private readonly GoogleDriveContract $googleDrive,
    ) {
    }

    public function get(string $fileName, string $fileId): GoogleDriveFile
    {
        return $this->googleDrive->get($fileName, $fileId);
    }
}

<?php

namespace LaravelGoogleDrive\Application\Ports;

use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;

interface GoogleDriveContract
{
    public function upload(GoogleDriveFile $file, string $folderId): GoogleDriveFileData;

    public function get(string $fileName, string $fileId): GoogleDriveFile;
}

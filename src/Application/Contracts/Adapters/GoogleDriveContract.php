<?php

namespace LaravelGoogleDrive\Application\Contracts\Adapters;

use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use Symfony\Component\HttpFoundation\File\File;

interface GoogleDriveContract
{
    public function upload(File $uploadedFile, string $folderId): GoogleDriveFileData;

    public function get(string $fileName, string $fileId): GoogleDriveFile;
}

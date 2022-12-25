<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use Symfony\Component\HttpFoundation\File\File;

class GoogleDriveService
{
    public function __construct(
        private readonly GoogleDriveContract $googleDrive,
    ) {
    }

    public function upload(File $file, string $folderId = ''): GoogleDriveFileData
    {
        return $this->googleDrive->upload($file, $folderId);
    }

    public function get(string $fileName, string $fileId): GoogleDriveFile
    {
        return $this->googleDrive->get($fileName, $fileId);
    }
}

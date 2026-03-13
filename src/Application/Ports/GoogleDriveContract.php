<?php

namespace LaravelGoogleDrive\Application\Ports;

use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Entities\LargeGoogleDriveFile;

interface GoogleDriveContract
{
    public function upload(GoogleDriveFile $file, string $folderId): GoogleDriveFileData;

    public function uploadResumable(LargeGoogleDriveFile $file, string $folderId, int $chunkSize): GoogleDriveFileData;

    public function get(string $fileName, string $fileId): GoogleDriveFile;

    public function delete(string $fileId): bool;
}

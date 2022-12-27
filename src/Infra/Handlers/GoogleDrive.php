<?php

namespace LaravelGoogleDrive\Infra\Handlers;

use LaravelGoogleDrive\Application\Getter;
use LaravelGoogleDrive\Application\Uploader;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GoogleDrive
{
    public function __construct(
        private readonly Uploader $uploader,
        private readonly Getter $getter
    ) {
    }

    public function upload(UploadedFile $uploadedFile, string $folderId = ''): GoogleDriveFileData
    {
        $file = new GoogleDriveFile(
            name: $uploadedFile->getClientOriginalName(),
            content: $uploadedFile->getContent(),
            mimeType: $uploadedFile->getMimeType() ?? 'application/octet-stream'
        );

        return $this->uploader->upload($file, $folderId);
    }

    public function get(string $fileName, string $fileId): GoogleDriveFile
    {
        return $this->getter->get($fileName, $fileId);
    }
}

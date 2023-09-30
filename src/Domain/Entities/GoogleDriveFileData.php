<?php

namespace LaravelGoogleDrive\Domain\Entities;

final class GoogleDriveFileData
{
    public function __construct(
        private readonly string $fileId,
        private readonly string $fileName,
        private readonly string $folderId
    ) {
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFolderId(): string
    {
        return $this->folderId;
    }
}

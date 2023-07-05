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

    /**
     * @return string
     */
    public function getFileId(): string
    {
        return $this->fileId;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getFolderId(): string
    {
        return $this->folderId;
    }
}

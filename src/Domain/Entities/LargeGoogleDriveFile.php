<?php

namespace LaravelGoogleDrive\Domain\Entities;

final class LargeGoogleDriveFile
{
    public function __construct(
        private readonly string $name,
        private readonly string $filePath,
        private readonly string $mimeType,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}

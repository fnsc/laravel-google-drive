<?php

namespace LaravelGoogleDrive\Domain\Entities;

final class GoogleDriveFile
{
    public function __construct(
        private readonly string $name,
        private readonly string $content,
        private readonly string $mimeType,
        private readonly string $fileId = '',
    ) {
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getExtension(): string
    {
        $fileName = preg_split('/\./', $this->name);
        $size = count($fileName ?: []) - 1;

        return $fileName[$size] ?? '';
    }
}

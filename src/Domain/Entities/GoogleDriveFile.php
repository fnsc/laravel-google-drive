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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
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

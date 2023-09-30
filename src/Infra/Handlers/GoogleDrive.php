<?php

namespace LaravelGoogleDrive\Infra\Handlers;

use LaravelGoogleDrive\Application\Deleter;
use LaravelGoogleDrive\Application\Getter;
use LaravelGoogleDrive\Application\Uploader;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Exceptions\InvalidDataProvidedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GoogleDrive
{
    public function __construct(
        private readonly Uploader $uploader,
        private readonly Getter $getter,
        private readonly Deleter $deleter
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

    /**
     * @param UploadedFile[] $uploadedFiles
     * @param string         $folderId
     * @return GoogleDriveFileData[]
     * @throws InvalidDataProvidedException
     */
    public function uploadMany(array $uploadedFiles, string $folderId = ''): array
    {
        $result = [];

        foreach ($uploadedFiles as $uploadedFile) {
            if (!($uploadedFile instanceof UploadedFile)) {
                throw new InvalidDataProvidedException(
                    'Invalid data type. The provided input is not an instance of UploadedFile.'
                );
            }

            $result[] = $this->upload($uploadedFile, $folderId);
        }

        return $result;
    }

    public function get(string $fileName, string $fileId): GoogleDriveFile
    {
        return $this->getter->get($fileName, $fileId);
    }

    public function delete(string $fileId): bool
    {
        return $this->deleter->delete($fileId);
    }
}

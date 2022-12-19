<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Google\Service\Drive\DriveFile;
use Google_Service_Drive;
use Illuminate\Config\Repository;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use Symfony\Component\HttpFoundation\File\File;

class GoogleDrive implements GoogleDriveContract
{
    public function __construct(
        private readonly Google_Service_Drive $googleServiceDrive,
        private readonly Repository $config
    ) {
    }

    public function upload(File $uploadedFile, string $folderId): GoogleDriveFile
    {
        $folderId = $folderId ?: $this->config->get('google_drive.folder_id');
        $googleDriveFile = new DriveFile([
            'name' => $uploadedFile->getFilename(),
            'parents' => [$folderId],
        ]);

        $driveFile = $this->googleServiceDrive->files->create(
            $googleDriveFile,
            [
                'data' => $uploadedFile->getContent(),
                'uploadType' => 'multipart',
                'fields' => 'id',
            ]
        );

        return new GoogleDriveFile(
            fileId: $driveFile->getId(),
            folderId: $folderId
        );
    }
}

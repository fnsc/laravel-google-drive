<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Google\Service\Drive\DriveFile;
use Google_Service_Drive;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use Symfony\Component\HttpFoundation\File\File;

class GoogleDrive implements GoogleDriveContract
{
    public function __construct(private readonly Google_Service_Drive $googleServiceDrive)
    {
    }

    public function upload(File $uploadedFile): DriveFile
    {
        $googleDriveFile = new DriveFile();
        $googleDriveFile->setName($uploadedFile->getFilename());
        $googleDriveFile->setOriginalFilename($uploadedFile->getFilename());

        return $this->googleServiceDrive
            ->files->create($googleDriveFile, [
                'data' => $uploadedFile->getContent(),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'media',
            ]);
    }
}

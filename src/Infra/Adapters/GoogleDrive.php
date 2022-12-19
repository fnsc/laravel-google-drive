<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Google\Service\Drive\DriveFile;
use Google_Service_Drive;
use Illuminate\Config\Repository;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Exceptions\FolderIdException;
use Symfony\Component\HttpFoundation\File\File;

class GoogleDrive implements GoogleDriveContract
{
    public function __construct(
        private readonly Google_Service_Drive $googleServiceDrive,
        private readonly Repository $config
    ) {
    }

    /**
     * @throws FolderIdException
     */
    public function upload(File $uploadedFile, string $folderId): GoogleDriveFile
    {
        $folderId = $this->getFolderId($folderId);
        $googleDriveFile = $this->getGoogleDriveFile($uploadedFile, $folderId);
        $driveFile = $this->uploadToGoogleDrive(
            $googleDriveFile,
            $uploadedFile
        );

        return new GoogleDriveFile(
            fileId: $driveFile->getId(),
            folderId: $folderId
        );
    }

    /**
     * @param string $folderId
     * @return string
     * @throws FolderIdException
     */
    private function getFolderId(string $folderId): string
    {
        $folderId = $folderId ?: $this->config->get(
            'google_drive.folder_id',
            ''
        );

        if (empty($folderId)) {
            throw new FolderIdException(
                'The folderId is empty. Please check GOOGLE_DRIVE_FOLDER_ID env variable or send the folderId as a param.'
            );
        }

        return $folderId;
    }

    /**
     * @param File   $uploadedFile
     * @param string $folderId
     * @return DriveFile
     */
    private function getGoogleDriveFile(File $uploadedFile, string $folderId): DriveFile
    {
        return new DriveFile([
            'name' => $uploadedFile->getFilename(),
            'parents' => [$folderId],
        ]);
    }

    /**
     * @param DriveFile $googleDriveFile
     * @param File      $uploadedFile
     * @return DriveFile
     */
    private function uploadToGoogleDrive(DriveFile $googleDriveFile, File $uploadedFile): DriveFile
    {
        return $this->googleServiceDrive->files->create(
            $googleDriveFile,
            [
                'data' => $uploadedFile->getContent(),
                'uploadType' => 'multipart',
                'fields' => 'id',
            ]
        );
    }
}

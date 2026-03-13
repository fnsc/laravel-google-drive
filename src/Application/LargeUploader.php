<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Ports\ConfigContract;
use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Entities\LargeGoogleDriveFile;
use LaravelGoogleDrive\Domain\Exceptions\FolderIdException;

class LargeUploader
{
    public function __construct(
        private readonly GoogleDriveContract $googleDrive,
        private readonly ConfigContract $config
    ) {
    }

    public function upload(LargeGoogleDriveFile $file, string $folderId, int $chunkSize): GoogleDriveFileData
    {
        $folderId = $this->getFolderId($folderId);

        return $this->googleDrive->uploadResumable(
            $file,
            $folderId,
            $chunkSize
        );
    }

    private function getFolderId(string $folderId): string
    {
        $folderId = $folderId ?: $this->config->get(
            'google_drive.folder_id',
            ''
        );

        if (empty($folderId)) {
            throw new FolderIdException(
                'The folder_id is empty. Please check GOOGLE_DRIVE_FOLDER_ID env variable or send the folderId as a param.'
            );
        }

        return $folderId;
    }
}

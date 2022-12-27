<?php

namespace LaravelGoogleDrive\Application;

use LaravelGoogleDrive\Application\Ports\ConfigContract;
use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Exceptions\FolderIdException;

class Uploader
{
    public function __construct(
        private readonly GoogleDriveContract $googleDrive,
        private readonly ConfigContract $config
    ) {
    }

    /**
     * @throws FolderIdException
     */
    public function upload(GoogleDriveFile $file, string $folderId): GoogleDriveFileData
    {
        $folderId = $this->getFolderId($folderId);

        return $this->googleDrive->upload($file, $folderId);
    }

    /**
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
                'The folder_id is empty. Please check GOOGLE_DRIVE_FOLDER_ID env variable or send the folderId as a param.'
            );
        }

        return $folderId;
    }
}

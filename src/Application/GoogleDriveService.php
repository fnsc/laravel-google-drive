<?php

namespace LaravelGoogleDrive\Application;

use Exception;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Exceptions\FolderIdException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;

class GoogleDriveService
{
    public function __construct(
        private readonly GoogleDriveContract $googleDrive,
        private readonly LoggerInterface $logger
    ) {
    }

    public function upload(File $file, string $folderId = ''): GoogleDriveFile|bool
    {
        try {
            return $this->googleDrive->upload($file, $folderId);
        } catch (FolderIdException $exception) {
            $this->logger->warning(
                '[LaravelGoogleDrive|Upload] Folder id is empty. Please check GOOGLE_DRIVE_FOLDER_ID env variable or send the folderId as a param.',
                compact('exception')
            );

            return false;
        } catch (Exception $exception) {
            $this->logger->warning(
                '[LaravelGoogleDrive|Upload] Something went wrong while we are uploading your file',
                compact('exception')
            );

            return false;
        }
    }
}

<?php

namespace LaravelGoogleDrive\Application;

use Exception;
use Google\Service\Drive\DriveFile;
use LaravelGoogleDrive\Application\Contracts\Adapters\GoogleDriveContract;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;

class GoogleDriveService
{
    public function __construct(
        private readonly GoogleDriveContract $googleDrive,
        private readonly LoggerInterface $logger
    ) {
    }

    public function upload(File $file): DriveFile|bool
    {
        try {
            return $this->googleDrive->upload($file);
        } catch (Exception $exception) {
            $this->logger->warning(
                '[LaravelGoogleDrive|Upload] Something went wrong while we are uploading your file',
                compact('exception')
            );

            return false;
        }
    }
}

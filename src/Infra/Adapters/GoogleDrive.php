<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Google\Service\Drive\DriveFile;
use Google_Service_Drive;
use GuzzleHttp\Psr7\Response;
use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;

class GoogleDrive implements GoogleDriveContract
{
    public function __construct(
        private readonly Google_Service_Drive $googleServiceDrive,
    ) {
    }

    public function upload(GoogleDriveFile $file, string $folderId): GoogleDriveFileData
    {
        $googleDriveFile = $this->buildDriveFile($file, $folderId);
        $driveFile = $this->uploadToGoogleDrive(
            $googleDriveFile,
            $file
        );

        return new GoogleDriveFileData(
            fileId: $driveFile->getId(),
            fileName: $file->getName(),
            folderId: $folderId
        );
    }

    public function get(string $fileName, string $fileId): GoogleDriveFile
    {
        $response = $this->getGoogleDriveFile($fileId);

        return new GoogleDriveFile(
            name: $fileName,
            content: $response->getBody()->getContents(),
            mimeType: current(
                $response->getHeader('Content-Type')
            ) ?: 'application/octet-stream',
            fileId: $fileId
        );
    }


    /**
     * @param GoogleDriveFile $uploadedFile
     * @param string          $folderId
     * @return DriveFile
     */
    private function buildDriveFile(GoogleDriveFile $uploadedFile, string $folderId): DriveFile
    {
        return new DriveFile([
            'name' => $uploadedFile->getName(),
            'parents' => [$folderId],
        ]);
    }

    /**
     * @param DriveFile       $googleDriveFile
     * @param GoogleDriveFile $file
     * @return DriveFile
     */
    private function uploadToGoogleDrive(DriveFile $googleDriveFile, GoogleDriveFile $file): DriveFile
    {
        return $this->googleServiceDrive->files->create(
            $googleDriveFile,
            [
                'data' => $file->getContent(),
                'uploadType' => 'multipart',
                'fields' => 'id',
            ]
        );
    }

    /**
     * @param string $fileId
     * @return Response
     */
    private function getGoogleDriveFile(string $fileId): Response
    {
        return $this->googleServiceDrive->files->get($fileId, [
            'fields' => 'name,size,id',
            'alt' => 'media',
        ]);
    }
}

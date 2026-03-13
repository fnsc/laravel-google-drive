<?php

namespace LaravelGoogleDrive\Infra\Adapters;

use Google\Http\MediaFileUpload;
use Google\Service\Drive\DriveFile;
use Google_Client;
use Google_Service_Drive;
use GuzzleHttp\Psr7\Response;
use LaravelGoogleDrive\Application\Ports\GoogleDriveContract;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use LaravelGoogleDrive\Domain\Entities\GoogleDriveFileData;
use LaravelGoogleDrive\Domain\Entities\LargeGoogleDriveFile;
use Psr\Http\Message\RequestInterface;

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

    public function uploadResumable(LargeGoogleDriveFile $file, string $folderId, int $chunkSize): GoogleDriveFileData
    {
        $googleDriveFile = new DriveFile([
            'name' => $file->getName(),
            'parents' => [$folderId],
        ]);

        $client = $this->googleServiceDrive->getClient();
        $client->setDefer(true);

        try {
            $request = $this->googleServiceDrive->files->create(
                $googleDriveFile
            );

            $media = $this->createMediaFileUpload(
                $client,
                $request,
                $file->getMimeType(),
                $chunkSize
            );

            $filePath = $file->getFilePath();
            $fileSize = filesize($filePath);
            $media->setFileSize(false !== $fileSize ? $fileSize : 0);

            $status = false;
            $handle = fopen($filePath, 'rb');

            if (false !== $handle) {
                while (!$status && !feof($handle)) {
                    $chunk = fread($handle, max(1, $chunkSize));

                    if (false !== $chunk) {
                        $status = $media->nextChunk($chunk);
                    }
                }

                fclose($handle);
            }
        } finally {
            $client->setDefer(false);
        }

        $driveFile = $status instanceof DriveFile ? $status : new DriveFile();

        return new GoogleDriveFileData(
            fileId: (string) $driveFile->getId(),
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

    public function delete(string $fileId): bool
    {
        $response = $this->googleServiceDrive->files->delete($fileId);

        return empty($response->getBody()->getContents());
    }

    /**
     * @codeCoverageIgnore
     */
    protected function createMediaFileUpload(
        Google_Client $client,
        RequestInterface $request,
        string $mimeType,
        int $chunkSize
    ): MediaFileUpload {
        return new MediaFileUpload(
            $client,
            $request,
            $mimeType,
            '',
            true,
            $chunkSize
        );
    }

    private function buildDriveFile(GoogleDriveFile $uploadedFile, string $folderId): DriveFile
    {
        return new DriveFile([
            'name' => $uploadedFile->getName(),
            'parents' => [$folderId],
        ]);
    }

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

    private function getGoogleDriveFile(string $fileId): Response
    {
        return $this->googleServiceDrive->files->get($fileId, [
            'fields' => 'name,size,id',
            'alt' => 'media',
        ]);
    }
}

<?php

namespace LaravelGoogleDrive\Application\Contracts\Adapters;

use Google\Service\Drive\DriveFile;
use Symfony\Component\HttpFoundation\File\File;

interface GoogleDriveContract
{
    public function upload(File $uploadedFile): DriveFile;
}

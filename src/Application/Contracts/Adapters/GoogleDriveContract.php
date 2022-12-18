<?php

namespace LaravelGoogleDrive\Application\Contracts\Adapters;

use LaravelGoogleDrive\Domain\Entities\GoogleDriveFile;
use Symfony\Component\HttpFoundation\File\File;

interface GoogleDriveContract
{
    public function upload(File $uploadedFile): GoogleDriveFile;
}

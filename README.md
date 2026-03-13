<h1 align="center">Laravel Google Drive</h1>

<p align="center">
    <a href="https://github.com/fnsc/laravel-google-drive/graphs/contributors"><img src="https://img.shields.io/github/contributors/fnsc/laravel-google-drive" /></a>
    <a href="https://github.com/fnsc/laravel-google-drive/actions?query=workflow%3ATests"><img src="https://github.com/fnsc/laravel-google-drive/workflows/Tests/badge.svg" alt="Tests Status"></a>
    <a href="https://www.codacy.com/gh/fnsc/laravel-google-drive/dashboard"><img src="https://app.codacy.com/project/badge/Grade/a0d0146de7fe421295e99a0c09b9db8c"/></a>
    <a href="https://www.codacy.com/gh/fnsc/laravel-google-drive/dashboard"><img src="https://app.codacy.com/project/badge/Coverage/a0d0146de7fe421295e99a0c09b9db8c"/></a>
</p>

A Laravel package to upload, download, and delete files on Google Drive using a service account.

- [Requirements](#requirements)
- [Installation](#installation)
- [Setup](#setup)
- [Usage](#usage)
- [License](#license)

## Requirements

- PHP ^8.2
- Laravel ^10.0

## Installation

```bash
composer require fnsc/laravel-google-drive
```

Publish the config file:

```bash
php artisan vendor:publish --provider="LaravelGoogleDrive\ServiceProvider"
```

## Setup

### 1. Create a Google Service Account

Go to [Google Cloud Console → Credentials](https://console.cloud.google.com/apis/credentials),
create a Service Account, and download the generated JSON key file.

Add the file to your project (e.g. `storage/app/service-account.json`) and **never commit it to git**.

### 2. Share the Google Drive folder

Open the target folder in Google Drive, click **Share**, and add the `client_email`
from the JSON file with **Editor** access.

### 3. Configure environment variables

Add the following to your `.env` file:

```env
GOOGLE_APPLICATION_CREDENTIALS=storage/app/service-account.json
GOOGLE_DRIVE_FOLDER_ID=your_folder_id_here
```

`GOOGLE_APPLICATION_CREDENTIALS` is the path to the JSON key file relative to the project root.
`GOOGLE_DRIVE_FOLDER_ID` is the ID found in the Google Drive folder URL:
`https://drive.google.com/drive/folders/<FOLDER_ID>`.

## Usage

Inject `LaravelGoogleDrive\GoogleDrive` into your controller or route closure.

### Upload a file

```php
use LaravelGoogleDrive\GoogleDrive;
use Illuminate\Http\Request;

Route::post('/upload', function (Request $request, GoogleDrive $drive) {
    $result = $drive->upload($request->file('file'));

    return [
        'file_id'   => $result->getFileId(),
        'file_name' => $result->getFileName(),
        'folder_id' => $result->getFolderId(),
    ];
});
```

To upload to a specific folder instead of the default one, pass the folder ID as the second argument:

```php
$drive->upload($request->file('file'), 'your_folder_id');
```

### Upload multiple files

```php
$results = $drive->uploadMany($request->file('files'));
```

### Download a file

```php
use Illuminate\Http\Response;

Route::get('/download', function (GoogleDrive $drive) {
    $file = $drive->get('filename.pdf', 'google_drive_file_id');

    return new Response($file->getContent(), 200, [
        'Content-Type'        => $file->getMimeType(),
        'Content-Disposition' => 'attachment; filename=' . $file->getName(),
    ]);
});
```

### Delete a file

```php
$deleted = $drive->delete('google_drive_file_id'); // returns bool
```

## License

This package is free software distributed under the terms of the [MIT license](http://opensource.org/licenses/MIT).

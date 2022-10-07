<?php

return [
    'credentials' => [
        'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
        'folderId' => env('GOOGLE_DRIVE_FOLDER_ID'),
    ],
];

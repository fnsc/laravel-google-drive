<?php

return [
    'credentials' => [
        'service_account' => __DIR__ . '/../' . ltrim(
            env('GOOGLE_APPLICATION_CREDENTIALS'),
            '/'
        ),
    ],

    'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),

    'tmp_directory' => env('TMP_DIRECTORY', '/tmp/'),
];

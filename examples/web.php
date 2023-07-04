<?php

/**
 * Examples
 */

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use LaravelGoogleDrive\GoogleDrive;

/**
 * Upload single file example
 */
Route::post(
    '/',
    function (Request $request, GoogleDrive $service): JsonResponse {
        $uploadedFile = $request->file('file');
        $result = $service->upload($uploadedFile);

        return new JsonResponse([
            'folder_id' => $result->getFolderId(),
            'file_id' => $result->getFileId(),
        ]);
    }
);

/**
 * Upload many example
 */
Route::post(
    '/upload-many',
    function (Request $request, GoogleDrive $service): JsonResponse {
        $uploadedFiles = $request->file('files');
        $result = $service->uploadMany($uploadedFiles);

        $payload = [];

        foreach ($result as $fileData) {
            $payload[] = [
                'folder_id' => $fileData->getFolderId(),
                'file_id' => $fileData->getFileId(),
            ];
        }

        return new JsonResponse($payload);
    }
);

/**
 * Download single file example
 */
Route::get('/download', function (GoogleDrive $service): Response {
    $file = $service->get('file.txt', '1ch20VwBUDffhnD3qzr0_DZk4Dk5pOKO321');

    $headers = [
        'ContentType' => $file->getMimeType(),
        'Content-Disposition' => 'attachment; filename=' . $file->getName(),
    ];

    return new Response($file->getContent(), Response::HTTP_OK, $headers);
});

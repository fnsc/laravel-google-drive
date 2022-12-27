<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use LaravelGoogleDrive\Infra\Handlers\GoogleDrive;

Route::post('/', function (Request $request) {
    $service = app(GoogleDrive::class);
    $uploadedFiles = $request->file('file');

    $result = $service->upload($uploadedFiles);

    return new JsonResponse([
        'folder_id' => $result->getFolderId(),
        'file_id' => $result->getFileId(),
    ]);
});

Route::get('/download', function () {
    $service = app(GoogleDrive::class);

    $file = $service->get('file.txt', '1ch20VwBUDffhnD3qzr0_DZk4Dk5pOKO321');

    return response($file->getContent(), Response::HTTP_OK)
        ->header('ContentType', $file->getMimeType())
        ->header(
            'Content-Disposition',
            'attachment; filename=' . $file->getName()
        );
});

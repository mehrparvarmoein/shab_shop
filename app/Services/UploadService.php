<?php

namespace App\Services;

use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{

    public function upload($uploadable, UploadedFile $uploadedFile, $disk, $type = 1)
    {
        $uploadedPath =  Str::uuid()->toString() . '.' . $this->getFileExtension($uploadedFile);

        $upload = Storage::disk($disk)->put($uploadedPath, $uploadedFile->getContent());

        if ($upload) {
            return $uploadable->images()->create([
                'path'      => $uploadedPath,
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'type'      => $type,
                'disk'      => $disk,
            ]);
        }

        throw new \Exception("Error uploading file");
    }

    public static function getUrl($id)
    {
        $file = Upload::find($id);
        return Storage::disk($file->disk)->url($file->path);
    }

    public static function getUrls($idArray)
    {
        $response = [];
        foreach ($idArray ?? [] as $key => $id) {
            $file = Upload::find($id);
            if (!empty($file)) {
                $response[$key]['url']  = Storage::disk($file->disk)->url($file->path);
                $response[$key]['id']   = $id;
                $response[$key]['type'] = $file->type;
            }
        }

        return $response;
    }

    public function getFileExtension(UploadedFile $file)
    {
        return $file->clientExtension();
    }
}

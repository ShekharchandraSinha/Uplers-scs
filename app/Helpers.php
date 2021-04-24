<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as FacadesImage;

class Helpers
{
    public static function randomDigit($count = 9)
    {
        return rand(pow(10, $count - 1), pow(10, $count) - 1);
    }

    public static function resizeImage($fileObject, $fileName, $folder = '/')
    {
        $folderPath = Storage::disk('project_public')->path($folder);
        $img = FacadesImage::make($fileObject)->resize(200, 96, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });;
        $img->save($folderPath . '/' . $fileName);

        return $fileName;
    }
}

<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class ImageService
{
    protected UploadedFile $file;

    public function storeMedia(Model&HasMedia $model, array $files, string $disk = 'public')
    {
        $modelName = class_basename($model);
        $folderName = strtolower($modelName) . '_images';

        $media = [];
        
        foreach ($files as $file)
        {
            $media[] = $model->addMedia($file)->toMediaCollection($folderName, $disk);
        }

        return $media;
    }

    public function storeLocal(Model $model, $image)
    {
        $modelName = class_basename($model);

        $userPath = Str::slug(auth()->user()->name, '-');
        $imagePath = strtolower($modelName) . '_images/' . $userPath;
        $imageName = Str::slug($model->title, '-') . '.' . $image->extension();
        $imageUrl = Storage::disk('public')->putFileAs($imagePath, $image, $imageName);

        return $model->update(['cover_image' => $imageUrl]);
    }
}

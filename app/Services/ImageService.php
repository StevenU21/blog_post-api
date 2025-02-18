<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class ImageService
{
    protected UploadedFile $file;

    public function storeMedia(Model&HasMedia $model, array $files, string $diskName = 'public')
    {
        $modelName = class_basename($model);
        $collectionName = strtolower($modelName) . '_images';

        $media = [];

        foreach ($files as $file)
        {
            $media[] = $model
            ->addMedia($file)
            ->toMediaCollection($collectionName, $diskName);
        }

        return $media;
    }

    public function storeLocal(Model $model, $attribute, $image)
    {
        $modelName = class_basename($model);

        $userPath = Str::slug(auth()->user()->name, '-');
        $imagePath = strtolower($modelName) . '_images/' . $userPath;
        $imageName = Str::slug($attribute, '-') . '.' . $image->extension();
        $imageUrl = Storage::disk('public')->putFileAs($imagePath, $image, $imageName);

        return $model->update(['cover_image' => $imageUrl]);
    }
}

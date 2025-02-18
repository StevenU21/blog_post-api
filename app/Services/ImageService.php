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

    /**
     * Store media files for the given model.
     *
     * @param Model&HasMedia $model
     * @param array $files
     * @param string $diskName
     * @return array
     */
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

    /**
     * Update media files for the given model.
     *
     * @param Model&HasMedia $model
     * @param array $mediaIdsToRetain
     * @param array $files
     * @param string $collectionName
     * @return array
     */
    public function updateMedia(Model&HasMedia $model, array $mediaIdsToRetain ,array $files, string $collectionName)
    {
        $mediaItems = $model->getMedia($collectionName);

        foreach ($mediaItems as $media) {
            if (!in_array($media->id, $mediaIdsToRetain)) {
                $media->delete();
            }
        }

        $media = $this->storeMedia($model, $files);

        return $media;
    }

    /**
     * Store a file locally and update the model's file attribute.
     *
     * @param Model $model
     * @param string $file_attribute
     * @param string $file_name
     * @param UploadedFile $file
     * @return bool
     */
    public function storeLocal(Model $model,string $file_attribute, string $file_name, UploadedFile $file)
    {
        $modelName = class_basename($model);

        $userPath = Str::slug(auth()->user()->name, '-');
        $imagePath = strtolower($modelName) . '_images/' . $userPath;
        $fileName = Str::slug($file_name, '-') . '.' . $file->extension();
        $imageUrl = Storage::disk('public')->putFileAs($imagePath, $file, $fileName);

        return $model->update([$file_attribute => $imageUrl]);
    }

    /**
     * Update a file locally and update the model's file attribute.
     *
     * @param Model $model
     * @param string $file_attribute
     * @param string $file_name
     * @param UploadedFile $file
     * @return bool
     */
    public function updateLocal(Model $model, string $file_attribute, string $file_name, UploadedFile $file)
    {
        Storage::disk('public')->delete($model->$file_attribute);
        return $this->storeLocal($model, $file_attribute, $file_name, $file);
    }
}

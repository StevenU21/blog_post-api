<?php

namespace App\Classes;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $user = auth()->user();
        $userSlug = Str::slug($user->name, '-');
        $modelName = strtolower(class_basename($media->model_type));
        $modelId = $media->model_id;

        return "{$userSlug}/{$modelName}/{$modelId}/{$media->id}/";
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive/';
    }
}

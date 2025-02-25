<?php

namespace App\Classes;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * Get the base path for the media file.
     *
     * @param Media $media The media instance.
     */
    public function getPath(Media $media): string
    {
        $userSlug = auth()->user()->slug;
        $modelName = strtolower(class_basename($media->model_type));
        $modelId = $media->model_id;

        return "{$userSlug}/{$modelName}/{$modelId}/{$media->id}/";
    }

    /**
     * Get the path for conversions of the media file.
     *
     * @param Media $media The media instance.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    /**
     * Get the path for responsive images of the media file.
     *
     * @param Media $media The media instance.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive/';
    }
}

<?php

namespace App\Traits;

use Spatie\ResponseCache\Facades\ResponseCache;

trait CacheClearable
{
    public static function bootClearsResponseCache()
    {
        self::created(function ($model) {
            ResponseCache::forget($model->getCacheKey());
        });

        self::updated(function ($model) {
            ResponseCache::forget($model->getCacheKey());
        });

        self::deleted(function ($model) {
            ResponseCache::forget($model->getCacheKey());
        });
    }

    public function getCacheKey()
    {
        return strtolower(class_basename($this)) . '.' . $this->id;
    }
}

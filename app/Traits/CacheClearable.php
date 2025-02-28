<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheClearable
{
    public static function bootCacheClearable()
    {
        static::created(function ($model) {
            $model->clearCache();
        });

        static::updated(function ($model) {
            $model->clearCache();
        });

        static::deleted(function ($model) {
            $model->clearCache();
        });
    }

    protected function clearCache()
    {
        if (method_exists($this, 'getCacheKeysToClear')) {
            $cacheKeys = $this->getCacheKeysToClear();

            foreach ($cacheKeys as $cacheKey) {
                Cache::forget($cacheKey);
            }
        }
    }


    /**
     * Get the cache keys that need to be cleared.
     *
     * This method should be overridden in the model to provide the specific cache keys.
     *
     * @return array
     */
    protected function getCacheKeysToClear(): array
    {
        return [];
    }
}

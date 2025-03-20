<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Closure;

class CacheService
{
    /**
     * Get data from cache or store it if not present
     *
     * @param string $key Cache key
     * @param int $minutes Minutes to keep in cache
     * @param \Closure $callback Function to execute if cache miss
     * @param bool $force Force refresh cache
     * @return mixed
     */
    public function remember(string $key, int $minutes, Closure $callback, bool $force = false)
    {
        if ($force) {
            Cache::forget($key);
        }
        
        return Cache::remember($key, Carbon::now()->addMinutes($minutes), $callback);
    }
    
    /**
     * Get data from cache with tags or store it if not present
     *
     * @param array $tags Cache tags
     * @param string $key Cache key
     * @param int $minutes Minutes to keep in cache
     * @param \Closure $callback Function to execute if cache miss
     * @param bool $force Force refresh cache
     * @return mixed
     */
    public function rememberWithTags(array $tags, string $key, int $minutes, Closure $callback, bool $force = false)
    {
        if ($force) {
            Cache::tags($tags)->forget($key);
        }
        
        return Cache::tags($tags)->remember($key, Carbon::now()->addMinutes($minutes), $callback);
    }
    
    /**
     * Clear cache by tags
     *
     * @param array $tags Tags to clear
     * @return bool
     */
    public function clearByTags(array $tags): bool
    {
        try {
            Cache::tags($tags)->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get appropriate cache duration based on data type and environment
     *
     * @param string $type Type of data (options: short, medium, long, permanent)
     * @return int Minutes to cache
     */
    public function getDuration(string $type = 'medium'): int
    {
        // Use shorter cache times in development
        $isProd = app()->environment('production');
        
        switch ($type) {
            case 'short':
                return $isProd ? 5 : 1; // 5 min in prod, 1 min in dev
            case 'medium':
                return $isProd ? 60 : 5; // 1 hour in prod, 5 min in dev
            case 'long':
                return $isProd ? 1440 : 30; // 1 day in prod, 30 min in dev
            case 'permanent':
                return $isProd ? 10080 : 60; // 1 week in prod, 1 hour in dev
            default:
                return $isProd ? 60 : 5;
        }
    }
}

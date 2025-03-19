<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Format bytes to kb, mb, gb, tb
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Format seconds to a readable time format
     *
     * @param int $seconds
     * @return string
     */
    public static function formatSeconds($seconds)
    {
        $days = floor($seconds / (60 * 60 * 24));
        $hours = floor(($seconds % (60 * 60 * 24)) / (60 * 60));
        $minutes = floor(($seconds % (60 * 60)) / 60);
        $seconds = $seconds % 60;
        
        $result = '';
        if ($days > 0) $result .= $days . ' days, ';
        if ($hours > 0 || $days > 0) $result .= $hours . ' hours, ';
        $result .= $minutes . ' minutes, ' . $seconds . ' seconds';
        
        return $result;
    }
}

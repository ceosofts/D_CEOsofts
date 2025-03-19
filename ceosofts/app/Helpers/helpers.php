<?php

use App\Helpers\FormatHelper;

if (!function_exists('format_bytes')) {
    /**
     * Format bytes to kb, mb, gb, tb
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function format_bytes($bytes, $precision = 2) {
        return FormatHelper::formatBytes($bytes, $precision);
    }
}

if (!function_exists('format_seconds')) {
    /**
     * Format seconds to a readable time format
     *
     * @param int $seconds
     * @return string
     */
    function format_seconds($seconds) {
        return FormatHelper::formatSeconds($seconds);
    }
}

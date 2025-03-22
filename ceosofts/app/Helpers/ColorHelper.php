<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Determine if a color is dark or not
     * Returns true if the color is dark, false otherwise
     * 
     * @param string $hexColor Hex color code (with or without #)
     * @return bool
     */
    public static function isDarkColor($hexColor)
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Calculate brightness (standard formula)
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        // Consider dark if brightness is less than 128
        return $brightness < 128;
    }
}

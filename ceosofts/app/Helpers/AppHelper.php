<?php

namespace App\Helpers;

class AppHelper
{
    /**
     * Format a number to Thai Baht currency format
     *
     * @param float $amount
     * @param bool $includeSymbol
     * @return string
     */
    public static function formatCurrency($amount, bool $includeSymbol = true): string
    {
        $formatted = number_format($amount, 2);
        return $includeSymbol ? "฿{$formatted}" : $formatted;
    }
    
    /**
     * Format a date to Thai format
     *
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function formatThaiDate($date, string $format = 'd/m/Y'): string
    {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        return date($format, $timestamp);
    }
    
    /**
     * Generate a unique reference code
     * 
     * @param string $prefix
     * @param int $length
     * @return string
     */
    public static function generateReferenceCode(string $prefix = '', int $length = 8): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $prefix . $randomString;
    }
}

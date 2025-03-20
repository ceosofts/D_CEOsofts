<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppHelper
{
    /**
     * Convert Thai date to database format
     * 
     * @param string $thaiDate Thai date format (dd/mm/yyyy)
     * @return string|null Database date format (Y-m-d)
     */
    public static function thaiDateToDbDate(?string $thaiDate): ?string
    {
        if (empty($thaiDate)) {
            return null;
        }
        
        try {
            // Convert dd/mm/yyyy to Y-m-d
            $parts = explode('/', $thaiDate);
            if (count($parts) === 3) {
                $day = $parts[0];
                $month = $parts[1];
                $year = (int)$parts[2] - 543; // Convert Buddhist year to Gregorian
                
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Convert database date to Thai date format
     * 
     * @param string $dbDate Database date (Y-m-d)
     * @param bool $includeTime Whether to include time
     * @return string|null Thai date format (dd/mm/yyyy)
     */
    public static function dbDateToThaiDate(?string $dbDate, bool $includeTime = false): ?string
    {
        if (empty($dbDate)) {
            return null;
        }
        
        try {
            $date = Carbon::parse($dbDate);
            $buddhistYear = $date->year + 543;
            
            if ($includeTime) {
                return $date->format('d/m/') . $buddhistYear . ' ' . $date->format('H:i:s');
            }
            
            return $date->format('d/m/') . $buddhistYear;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Generate a unique code with prefix
     * 
     * @param string $prefix Prefix for the code
     * @param int $length Length of the random part
     * @return string Unique code
     */
    public static function generateUniqueCode(string $prefix, int $length = 8): string
    {
        $random = Str::random($length);
        return strtoupper($prefix . $random);
    }
    
    /**
     * Format number to Thai baht format
     * 
     * @param float $number Number to format
     * @return string Formatted number
     */
    public static function formatCurrency(?float $number): string
    {
        if ($number === null) {
            return "0.00";
        }
        
        return number_format($number, 2, '.', ',');
    }
    
    /**
     * Convert number to Thai text
     * 
     * @param float $number Number to convert
     * @return string Thai text representation
     */
    public static function numberToThaiText(float $number): string
    {
        $thaiNumbers = ['ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
        $thaiUnits = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
        
        $number = round($number, 2);
        list($integer, $fraction) = explode('.', number_format($number, 2, '.', ''));
        
        $result = '';
        
        if ((int)$integer === 0) {
            $result = 'ศูนย์บาท';
        } else {
            $integer = (string)$integer;
            $length = strlen($integer);
            
            for ($i = 0; $i < $length; $i++) {
                $digit = (int)$integer[$i];
                
                if ($digit !== 0) {
                    if ($digit == 1 && $length - $i === 2) {
                        $result .= 'สิบ';
                    } elseif ($digit == 2 && $length - $i === 2) {
                        $result .= 'ยี่สิบ';
                    } else {
                        $result .= $thaiNumbers[$digit] . $thaiUnits[($length - $i - 1) % 7];
                    }
                }
                
                if ($digit !== 0 && ($length - $i - 1) !== 0 && ($length - $i - 1) % 6 === 0) {
                    $result .= 'ล้าน';
                }
            }
            
            $result .= 'บาท';
        }
        
        if ((int)$fraction === 0) {
            $result .= 'ถ้วน';
        } else {
            $result .= $thaiNumbers[(int)$fraction[0]] . 'สิบ';
            
            if ((int)$fraction[1] !== 0) {
                $result .= $thaiNumbers[(int)$fraction[1]];
            }
            
            $result .= 'สตางค์';
        }
        
        return $result;
    }
    
    /**
     * Get current user department
     * 
     * @return int|null Department ID
     */
    public static function getCurrentDepartmentId(): ?int
    {
        if (Auth::check() && Auth::user()->department) {
            return Auth::user()->department->id;
        }
        
        return null;
    }
    
    /**
     * Check if the current date is in a Thai holiday
     * 
     * @param string|null $date Date to check (Y-m-d format), defaults to today
     * @return bool True if holiday
     */
    public static function isHoliday(?string $date = null): bool
    {
        if ($date === null) {
            $date = Carbon::today()->format('Y-m-d');
        }
        
        // Use cache to improve performance
        return Cache::remember("holiday_{$date}", 86400, function() use ($date) {
            // Check if the date is in company_holidays table
            $count = \DB::table('company_holidays')
                ->whereDate('holiday_date', $date)
                ->count();
                
            return $count > 0;
        });
    }
    
    /**
     * Get Thai month name
     * 
     * @param int $month Month number (1-12)
     * @return string Thai month name
     */
    public static function getThaiMonth(int $month): string
    {
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];
        
        return $thaiMonths[$month] ?? '';
    }
    
    /**
     * Format full Thai date
     * 
     * @param string $date Date in Y-m-d format
     * @param bool $shortFormat Whether to use short format for month
     * @return string Formatted Thai date
     */
    public static function formatThaiDate(string $date, bool $shortFormat = false): string
    {
        try {
            $carbon = Carbon::parse($date);
            $day = $carbon->day;
            $month = $carbon->month;
            $year = $carbon->year + 543;
            
            $thaiMonth = self::getThaiMonth($month);
            
            if ($shortFormat) {
                $thaiMonth = mb_substr($thaiMonth, 0, 3, 'UTF-8');
            }
            
            return "{$day} {$thaiMonth} {$year}";
        } catch (\Exception $e) {
            return $date;
        }
    }
    
    /**
     * Sanitize input data
     * 
     * @param string $input Input to sanitize
     * @return string Sanitized input
     */
    public static function sanitize(string $input): string
    {
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * Validate data with customizable rules
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array|bool True if valid, or array of errors
     */
    public static function validate(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }
        
        return true;
    }
    
    /**
     * Check if file exists and is newer than specified time
     * 
     * @param string $path File path
     * @param int $seconds Max age in seconds
     * @return bool True if file exists and is fresh
     */
    public static function isFileFresh(string $path, int $seconds = 3600): bool
    {
        if (!File::exists($path)) {
            return false;
        }
        
        $fileTime = File::lastModified($path);
        $currentTime = time();
        
        return ($currentTime - $fileTime) < $seconds;
    }
    
    /**
     * Calculate age from birthdate
     * 
     * @param string $birthdate Birthdate in Y-m-d format
     * @return int Age in years
     */
    public static function calculateAge(string $birthdate): int
    {
        return Carbon::parse($birthdate)->age;
    }
    
    /**
     * Format Thai ID card number
     * 
     * @param string $id ID card number
     * @return string Formatted ID (e.g. x-xxxx-xxxxx-xx-x)
     */
    public static function formatThaiID(string $id): string
    {
        $id = preg_replace('/[^0-9]/', '', $id);
        
        if (strlen($id) !== 13) {
            return $id;
        }
        
        return substr($id, 0, 1) . '-' . 
               substr($id, 1, 4) . '-' . 
               substr($id, 5, 5) . '-' . 
               substr($id, 10, 2) . '-' . 
               substr($id, 12, 1);
    }
    
    /**
     * Get fiscal year period based on given date
     * 
     * @param string|null $date Date in Y-m-d format
     * @return array Start and end dates of fiscal year
     */
    public static function getFiscalYear(?string $date = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        
        // Thai fiscal year starts on October 1
        $year = $date->month >= 10 ? $date->year + 1 : $date->year;
        
        $start = Carbon::create($year - 1, 10, 1)->format('Y-m-d');
        $end = Carbon::create($year, 9, 30)->format('Y-m-d');
        
        return [
            'start' => $start,
            'end' => $end,
            'year' => $year
        ];
    }
    
    /**
     * Clean a filename to be safe for storing
     * 
     * @param string $filename Original filename
     * @return string Sanitized filename
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove any character that is not alphanumeric, underscore, dash, or dot
        $filename = preg_replace('/[^\w\-\.]/', '_', $filename);
        
        // Ensure filename isn't too long
        if (strlen($filename) > 255) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 250) . '.' . $ext;
        }
        
        return $filename;
    }
    
    /**
     * Format bytes to human-readable size
     * 
     * @param int $bytes Size in bytes
     * @param int $precision Decimal precision
     * @return string Formatted size
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * ตรวจสอบว่าสีที่ให้มาเป็นสีเข้มหรือไม่
     *
     * @param string $hexColor รหัสสี HEX (เช่น #FF0000)
     * @return bool true ถ้าเป็นสีเข้ม, false ถ้าเป็นสีอ่อน
     */
    public static function isDarkColor($hexColor)
    {
        // ถ้าไม่มีค่าหรือไม่ถูกต้อง ให้ถือว่าเป็นสีเข้ม
        if (!$hexColor || !preg_match('/^#[0-9A-F]{6}$/i', $hexColor)) {
            return true;
        }
        
        // แปลงสี HEX เป็นค่า RGB
        $r = hexdec(substr($hexColor, 1, 2));
        $g = hexdec(substr($hexColor, 3, 2));
        $b = hexdec(substr($hexColor, 5, 2));
        
        // คำนวณความสว่าง (ใช้สูตร YIQ)
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        // ถ้า YIQ < 128 ถือว่าเป็นสีเข้ม
        return $yiq < 128;
    }
}

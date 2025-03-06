<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * ฟิลด์ที่อนุญาตให้กรอก (Mass Assignment)
     */
    protected $fillable = [
        'company_name',
        'branch',               // เพิ่มฟิลด์ branch
        'branch_description',   // เพิ่มฟิลด์ branch_description
        'address',
        'phone',
        'mobile',
        'fax',
        'email',
        'website',
        'logo',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'tiktok',
        'facebook',
        'line',
        'tax_id',
        'contact_person'
    ];

    /**
     * ตัวอย่างการแปลง (cast) ค่าบางฟิลด์
     * หากต้องการบังคับให้ branch เป็น integer เสมอ
     */
    protected $casts = [
        'branch' => 'integer',
    ];
}

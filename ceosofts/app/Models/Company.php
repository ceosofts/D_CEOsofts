<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory;

    /**
     * ฟิลด์ที่อนุญาตให้กรอก (Mass Assignment)
     *
     * @var array<string>
     */
    protected $fillable = [
        'company_name',
        'branch',
        'branch_description',
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
     * การแปลง (cast) ค่าบางฟิลด์
     *
     * @var array<string, string>
     */
    protected $casts = [
        'branch' => 'integer',
    ];

    /**
     * Get the full logo URL
     *
     * @return string
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::url($this->logo) : asset('images/default-company-logo.png');
    }
    
    /**
     * Get formatted address
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return $this->address ?: 'ไม่ระบุที่อยู่';
    }
    
    /**
     * Check if company has social media
     *
     * @return bool
     */
    public function hasSocialMedia()
    {
        return !empty($this->facebook) || 
               !empty($this->twitter) || 
               !empty($this->instagram) || 
               !empty($this->linkedin) || 
               !empty($this->youtube) || 
               !empty($this->tiktok) || 
               !empty($this->line);
    }
    
    /**
     * ความสัมพันธ์กับโมเดลอื่นๆ ที่เกี่ยวข้องกับบริษัท
     * เช่น invoices, employees, etc.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefix extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prefixes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', // รองรับคอลัมน์แบบเก่า
        'prefix_th', 
        'prefix_en', 
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // ก่อนบันทึกข้อมูล ตรวจสอบว่า prefix_th มีค่าและ name ไม่มีค่า ให้กำหนด name = prefix_th
        static::saving(function ($prefix) {
            if (!empty($prefix->prefix_th) && empty($prefix->name)) {
                $prefix->name = $prefix->prefix_th;
            }
            // กรณีตรงกันข้าม ถ้ามี name แต่ไม่มี prefix_th
            elseif (!empty($prefix->name) && empty($prefix->prefix_th)) {
                $prefix->prefix_th = $prefix->name;
            }
        });
    }
    
    /**
     * Get the display name (compatible with old and new column names)
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->prefix_th ?? $this->name ?? '-';
    }

    /**
     * Get the employees for this prefix.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}

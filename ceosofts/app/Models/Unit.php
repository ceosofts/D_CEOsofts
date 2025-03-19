<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Unit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'units';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_name_th',
        'unit_name_en',
        'description',
        'is_active',
        'unit_code',
        // ชื่อสำรองหากมีการตั้งชื่อคอลัมน์ต่างไป
        'unit_name',
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * ชื่อหน่วยนับในภาษาไทย หรือชื่อหลัก
     * อ้างอิงตาม field ที่มีในฐานข้อมูล
     */
    public function getNameAttribute()
    {
        if (Schema::hasColumn('units', 'unit_name_th')) {
            return $this->unit_name_th;
        } elseif (Schema::hasColumn('units', 'unit_name')) {
            return $this->unit_name;
        } elseif (Schema::hasColumn('units', 'name')) {
            return $this->name;
        }
        
        return null;
    }

    /**
     * ความสัมพันธ์กับตาราง Products
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id', 'id');
    }
}

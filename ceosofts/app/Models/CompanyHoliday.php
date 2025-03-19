<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CompanyHoliday extends Model
{
    use HasFactory;

    /**
     * ชื่อตารางในฐานข้อมูล
     */
    protected $table = 'company_holidays';

    /**
     * ฟิลด์ที่อนุญาตให้ทำ Mass Assignment
     */
    protected $fillable = [
        'date',
        'name',
        'description',
    ];

    /**
     * กำหนดการ Cast คอลัมน์ 'date' ให้เป็น Date (Carbon instance)
     * ทำให้เวลาดึงค่า $model->date จะได้เป็น Carbon Object
     */
    protected $casts = [
        'date' => 'date', // หรือ 'date:Y-m-d' หากต้องการ format default
    ];

    /**
     * Scope ตัวอย่าง: ค้นหา Holiday เฉพาะปีที่ระบุ
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $year
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInYear($query, int $year)
    {
        return $query->whereYear('date', $year);
    }

    /**
     * Scope ตัวอย่าง: ค้นหา Holiday ที่ยังไม่ถึง (ในอนาคต) นับจากวันนี้
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->startOfDay());
    }

    /**
     * Accessor ตัวอย่าง: ดึงฟิลด์ date ออกมาเป็นข้อความรูปแบบ d/m/Y
     *
     * เรียกใช้ด้วย $model->date_formatted
     *
     * @return string|null
     */
    public function getDateFormattedAttribute()
    {
        return $this->date
            ? $this->date->format('d/m/Y')
            : null;
    }
}

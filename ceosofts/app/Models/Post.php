<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * ฟิลด์ที่อนุญาตให้ทำ Mass Assignment
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'user_id', // ตัวอย่าง: ผู้เขียนโพสต์
    ];

    /**
     * ความสัมพันธ์: โพสต์แต่ละโพสต์จะ belong to ผู้ใช้ (ถ้าต้องการ)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

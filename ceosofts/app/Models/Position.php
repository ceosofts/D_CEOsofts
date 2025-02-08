<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // เชื่อมกับ User (1 ตำแหน่ง มีหลาย User)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

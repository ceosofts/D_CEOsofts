<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public $timestamps = true; // ✅ ให้ Laravel อัปเดต created_at และ updated_at อัตโนมัติ
}

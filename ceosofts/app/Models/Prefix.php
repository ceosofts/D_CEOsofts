<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefix extends Model
{
    use HasFactory;

    /**
     * ฟิลด์ที่อนุญาตให้ทำ Mass Assignment
     *
     * @var array
     */
    protected $fillable = ['name'];
}

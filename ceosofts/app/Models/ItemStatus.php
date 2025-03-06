<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStatus extends Model
{
    use HasFactory;

    /**
     * กำหนดฟิลด์ที่สามารถทำ Mass Assignment ได้
     *
     * @var array
     */
    protected $fillable = ['name'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TaxSetting
 *
 * This model represents a tax setting configuration within the application.
 *
 * @package App\Models
 *
 * @property string $name
 * @property float $rate
 */
class TaxSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'rate'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rate' => 'float',
    ];
}

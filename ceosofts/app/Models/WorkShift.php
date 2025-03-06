<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class WorkShift
 *
 * Represents a work shift with a name and defined start and end times.
 *
 * @property string $name        The name of the work shift.
 * @property string $start_time  The start time of the shift (format: "H:i:s").
 * @property string $end_time    The end time of the shift (format: "H:i:s").
 */
class WorkShift extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'start_time', 'end_time'];

    /**
     * The attributes that should be cast.
     *
     * Casting the start_time and end_time as datetime objects (using Carbon)
     * can help when performing time calculations.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time'   => 'datetime:H:i:s',
    ];

    /**
     * Optionally, you can add accessors if you need a custom format for the time.
     *
     * Example:
     * public function getStartTimeFormattedAttribute()
     * {
     *     return Carbon::parse($this->start_time)->format('g:i A');
     * }
     *
     * public function getEndTimeFormattedAttribute()
     * {
     *     return Carbon::parse($this->end_time)->format('g:i A');
     * }
     */
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Traits\GeneratesEmployeeCode;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Position;

/**
 * Class Employee
 *
 * Represents an employee record in the system.
 *
 * @package App\Models
 */
class Employee extends Model
{
    use HasFactory, GeneratesEmployeeCode;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'national_id',
        'driver_license',
        'date_of_birth',
        'phone',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'spouse_name',
        'tax_deductions',
        'department_id',
        'position_id',
        'salary',
        'employment_status',
        'hire_date',
        'resignation_date'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth'    => 'datetime:Y-m-d',
        'hire_date'        => 'datetime:Y-m-d',
        'resignation_date' => 'datetime:Y-m-d',
    ];

    /**
     * Get all attendances for the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    /**
     * Get the department that the employee belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the position that the employee holds.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Accessor: Calculate the employee's age based on the date of birth.
     *
     * @return int|null
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Generate a new employee code.
     *
     * This function retrieves the latest employee code and increments the numeric portion.
     *
     * @return string
     */
    public static function generateEmployeeCode(): string
    {
        $latestEmployee = self::whereNotNull('employee_code')->latest('id')->first();
        $nextNumber = $latestEmployee ? intval(substr($latestEmployee->employee_code, 3)) + 1 : 1;
        return 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}

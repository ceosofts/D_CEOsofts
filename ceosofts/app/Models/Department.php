<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 *
 * This model represents a department within the organization.
 *
 * @package App\Models
 */
class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    /**
     * Optional: Define a relationship with Employee model.
     *
     * Uncomment the following method if you have an Employee model
     * and want to retrieve all employees in the department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function employees()
    // {
    //     return $this->hasMany(Employee::class);
    // }
}

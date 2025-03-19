<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Role
 *
 * This model represents a user role within the application.
 *
 * @package App\Models
 *
 * @property string $name
 * @property string $guard_name
 */
class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'guard_name'];

    /**
     * Indicates if the model should be timestamped.
     *
     * If your roles table doesn't have created_at and updated_at columns,
     * set this to false.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

/**
 * Class User
 *
 * Represents a user of the application.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int|null $department_id
 * @property string|null $remember_token
 * @property string $role  // Appended attribute from getRoleAttribute()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'department_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['role'];

    /**
     * Relationship: User belongs to a Department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relationship: User belongs to a Position.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        // If you're using Spatie's HasRoles trait, you can rely on its implementation
        if (method_exists(get_parent_class($this), 'hasRole')) {
            return parent::hasRole($roleName);
        }
        
        // If you have a roles relationship
        if (method_exists($this, 'roles')) {
            return $this->roles()->where('name', $roleName)->exists();
        }
        
        // If you have a role column in users table
        if (isset($this->attributes['role'])) {
            return $this->attributes['role'] === $roleName;
        }
        
        // Default implementation for testing
        return ($roleName === 'admin' && $this->id === 1);
    }

    /**
     * Check if the user has any of the given roles
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if the user has all of the given roles
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasAllRoles(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->count() === count($roleNames);
    }

    /**
     * Accessor for the role attribute.
     *
     * @return string
     */
    public function getRoleAttribute()
    {
        return $this->getRoleNames()->first() ?? 'ไม่มี Role';
    }

    /**
     * Set the user's password.
     *
     * Automatically hashes the password when setting.
     *
     * @param string $password
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            // If the password is not already hashed, hash it
            $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
        }
    }

    /**
     * Boot method to assign a default role to new users.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // If no role has been assigned, default to "user"
            if ($user->getRoleNames()->isEmpty()) {
                $user->assignRole('user');
            }
        });
    }
}

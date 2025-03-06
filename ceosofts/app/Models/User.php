<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
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

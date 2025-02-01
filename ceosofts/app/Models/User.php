<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'department_id', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['role'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getRoleAttribute()
    {
        return $this->getRoleNames()->first() ?? 'ไม่มี Role'; // ✅ ป้องกัน Null
    }

    /**
     * กำหนด Role ให้ User ใหม่อัตโนมัติ
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->hasRole('admin') && !$user->hasRole('user')) {
                $user->assignRole('user'); // ✅ กำหนด Role Default เป็น "user"
            }
        });
    }
}

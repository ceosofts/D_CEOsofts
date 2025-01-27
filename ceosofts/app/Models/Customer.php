<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'phone', 'address', 'taxid'];

    /**
     * ความสัมพันธ์กับ Order
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Mutator และ Accessor สำหรับ taxid
     */
    public function getTaxidAttribute($value)
    {
        return 'TAX' . $value;
    }

    public function setTaxidAttribute($value)
    {
        $this->attributes['taxid'] = str_replace('TAX', '', $value);
    }

    /**
     * Accessor สำหรับ Full Name
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->email . ')';
    }

    /**
     * Mutator และ Accessor สำหรับ Phone
     */
    public function getPhoneAttribute($value)
    {
        return str_starts_with($value, '0') ? $value : '0' . $value;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = ltrim($value, '0');
    }

    /**
     * Accessor สำหรับ Created At, Updated At, Deleted At
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i:s');
    }

    public function getDeletedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i:s');
    }

    /**
     * Accessor สำหรับตรวจสอบสถานะการลบ
     */
    public function getIsDeletedAttribute()
    {
        return $this->deleted_at !== null;
    }

    public function getIsNotDeletedAttribute()
    {
        return $this->deleted_at === null;
    }
}

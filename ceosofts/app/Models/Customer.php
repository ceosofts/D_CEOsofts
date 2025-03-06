<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Customer
 *
 * @property int $id
 * @property string $name       ชื่อของลูกค้า
 * @property string $email      อีเมล
 * @property string $phone      เบอร์โทร (มีการปรับแต่ง 0 นำหน้า)
 * @property string $address    ที่อยู่
 * @property string $taxid      เลขประจำตัวผู้เสียภาษี (บันทึกใน DB โดยตัด 'TAX' ออก)
 * @property string $code       รหัสลูกค้า (ถ้ามี)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read bool $is_deleted
 * @property-read bool $is_not_deleted
 * @property-read string $full_name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer onlyTrashed()    // ถ้าใช้ SoftDeletes
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withTrashed()   // ถ้าใช้ SoftDeletes
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withoutTrashed()// ถ้าใช้ SoftDeletes
 */
class Customer extends Model
{
    use HasFactory;
    // ถ้าต้องการให้รองรับ Soft Delete → ใส่ SoftDeletes ด้วย
    // use SoftDeletes;

    /**
     * กำหนดฟิลด์ที่สามารถ Mass Assign ได้
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'taxid',
        'code',
    ];

    /**
     * (ตัวอย่าง) หากใช้ SoftDeletes → เพิ่มฟิลด์ 'deleted_at' ใน $dates หรือ $casts
     */
    // protected $dates = ['deleted_at'];

    /**
     * ความสัมพันธ์กับ Model Order (กรณีมีตาราง orders)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | ส่วนนี้คือการปรับแต่ง (get/set) ฟิลด์ต่าง ๆ
    | ช่วยให้เวลาอ่าน/เขียนค่าในฟิลด์เป็นไปตาม Logic ที่กำหนด
    |
    */

    /**
     * Mutator: setTaxidAttribute
     * บันทึกลงฐานข้อมูลโดยตัด 'TAX' นำหน้าออก
     */
    public function setTaxidAttribute($value)
    {
        $this->attributes['taxid'] = str_replace('TAX', '', $value);
    }

    /**
     * Accessor: getTaxidAttribute
     * เวลาอ่านค่าจาก DB จะเติม 'TAX' นำหน้า
     */
    public function getTaxidAttribute($value)
    {
        return 'TAX' . $value;
    }

    /**
     * Accessor: getFullNameAttribute
     * รวม name และ email ไว้ใช้งานสะดวก เช่น $customer->full_name
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->email . ')';
    }

    /**
     * Mutator: setPhoneAttribute
     * เก็บเบอร์โทรลง DB โดยตัด 0 ข้างหน้าทิ้ง (ถ้ามี)
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = ltrim($value, '0');
    }

    /**
     * Accessor: getPhoneAttribute
     * เวลาอ่านเบอร์โทรจาก DB → หากไม่ขึ้นต้นด้วย 0 จะเติม 0 ให้อัตโนมัติ
     */
    public function getPhoneAttribute($value)
    {
        return str_starts_with($value, '0') ? $value : '0' . $value;
    }

    /**
     * Accessor: getCreatedAtAttribute
     * ปรับรูปแบบแสดงผลของ created_at เป็น d/m/Y H:i:s
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i:s');
    }

    /**
     * Accessor: getUpdatedAtAttribute
     * ปรับรูปแบบแสดงผลของ updated_at เป็น d/m/Y H:i:s
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i:s');
    }

    /**
     * Accessor: getDeletedAtAttribute
     * ปรับรูปแบบแสดงผลของ deleted_at เป็น d/m/Y H:i:s (ถ้าใช้ SoftDeletes)
     */
    public function getDeletedAtAttribute($value)
    {
        return $value
            ? Carbon::parse($value)->format('d/m/Y H:i:s')
            : null;
    }

    /**
     * Accessor: getIsDeletedAttribute
     * ตรวจสอบว่าถูกลบแบบ Soft Delete หรือไม่
     */
    public function getIsDeletedAttribute()
    {
        return $this->deleted_at !== null;
    }

    /**
     * Accessor: getIsNotDeletedAttribute
     * ตรวจสอบว่ายังไม่ถูกลบ
     */
    public function getIsNotDeletedAttribute()
    {
        return $this->deleted_at === null;
    }
}

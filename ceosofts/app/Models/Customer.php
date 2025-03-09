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
 * @property string $companyname       ชื่อบริษัทลูกค้า
 * @property string $contact_name      ชื่อผู้ติดต่อ
 * @property string $email             อีเมล
 * @property string $phone             เบอร์โทร (มีการปรับแต่ง 0 นำหน้า)
 * @property string $address           ที่อยู่
 * @property string $taxid             เลขประจำตัวผู้เสียภาษี (บันทึกใน DB โดยตัด 'TAX' ออก)
 * @property string $code              รหัสลูกค้า (รูปแบบ "CUSxxxx1")
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read bool $is_deleted
 * @property-read bool $is_not_deleted
 * @property-read string $full_name      // full_name จะรวม companyname และ email
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withoutTrashed()
 */
class Customer extends Model
{
    use HasFactory;
    // หากต้องการใช้ SoftDeletes ให้ uncomment บรรทัดด้านล่าง
    // use SoftDeletes;

    /**
     * กำหนดฟิลด์ที่สามารถ Mass Assign ได้
     */
    protected $fillable = [
        'companyname',     // ชื่อบริษัทลูกค้า (ใช้แทนฟิลด์ name)
        'contact_name',    // ชื่อผู้ติดต่อ
        'email',
        'phone',
        'address',
        'taxid',
        'code',
    ];

    /**
     * Boot method สำหรับ auto generate รหัสลูกค้า (Customer Code)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            // ถ้า code ยังไม่มี ให้สร้าง code ใหม่ในรูปแบบ "CUSxxxx1"
            if (empty($customer->code)) {
                // ดึงรหัสลูกค้าสูงสุดจากฐานข้อมูล
                $lastCode = Customer::max('code');
                // หากมีค่า ให้นำส่วนตัวเลขออกมา (คาดว่า code มีรูปแบบ "CUSxxxx1")
                // เราจะตัดเอาตัวเลขจากตำแหน่งที่ 3 ถึง 6 (4 หลัก)
                $lastNumber = $lastCode ? intval(substr($lastCode, 3, 4)) : 0;
                // สร้าง code ใหม่
                $customer->code = 'CUS' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT) . '1';
            }
        });
    }

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
        return $value;
    }

    /**
     * Accessor: getFullNameAttribute
     * รวม companyname และ email ไว้ใช้งานสะดวก เช่น $customer->full_name
     */
    public function getFullNameAttribute()
    {
        return $this->companyname . ' (' . $this->email . ')';
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
        return $value ? Carbon::parse($value)->format('d/m/Y H:i:s') : null;
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

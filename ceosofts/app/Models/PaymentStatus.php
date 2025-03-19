<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_statuses';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * รายการที่เกี่ยวข้องกับสถานะนี้ (ถ้ามี)
     */
    public function transactions()
    {
        // อาจจะต้องอัพเดทให้สัมพันธ์กับตารางในระบบที่เกี่ยวข้องกับการจ่ายเงิน
        return $this->hasMany(Transaction::class, 'payment_status_id');
    }
}

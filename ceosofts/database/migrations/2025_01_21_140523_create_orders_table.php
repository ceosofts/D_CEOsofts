<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();


            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // ลิงก์กับตาราง customers >>>เพิ่มจากของเดิม
            $table->string('order_number')->unique(); // หมายเลขคำสั่งซื้อ ต้องไม่ซ้ำ >>>เพิ่มจากของเดิม
            $table->date('order_date'); // วันที่คำสั่งซื้อ >>>เพิ่มจากของเดิม
            $table->decimal('total_amount', 10, 2); // ยอดรวมของคำสั่งซื้อ >>>เพิ่มจากของเดิม
            $table->string('status')->default('pending'); // สถานะคำสั่งซื้อ เช่น pending, completed, canceled >>>เพิ่มจากของเดิม
            $table->text('notes')->nullable(); // หมายเหตุคำสั่งซื้อ (ถ้ามี) >>>เพิ่มจากของเดิม


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

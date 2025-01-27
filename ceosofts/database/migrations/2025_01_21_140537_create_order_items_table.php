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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();


            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // ลิงก์กับตาราง orders >>>เพิ่มจากของเดิม
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // ลิงก์กับตาราง products >>>เพิ่มจากของเดิม
            $table->integer('quantity'); // จำนวนสินค้า >>>เพิ่มจากของเดิม
            $table->decimal('unit_price', 10, 2); // ราคาต่อหน่วย >>>เพิ่มจากของเดิม
            $table->decimal('subtotal', 10, 2); // ราคารวมต่อรายการสินค้า >>>เพิ่มจากของเดิม


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

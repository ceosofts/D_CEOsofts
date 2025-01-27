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
        Schema::create('products', function (Blueprint $table) {
            $table->id();


            $table->string('name'); // ชื่อสินค้า >>>เพิ่มจากของเดิม
            $table->text('description')->nullable(); // รายละเอียดสินค้า (ไม่จำเป็นต้องมี) >>>เพิ่มจากของเดิม
            $table->decimal('price', 10, 2); // ราคาสินค้า (ทศนิยม 2 ตำแหน่ง) >>>เพิ่มจากของเดิม
            $table->integer('stock_quantity')->default(0); // จำนวนในคลังสินค้า เริ่มต้นเป็น 0 >>>เพิ่มจากของเดิม
            $table->string('sku')->unique(); // รหัสสินค้า ต้องไม่ซ้ำ >>>เพิ่มจากของเดิม
            $table->boolean('is_active')->default(true); // สถานะสินค้า (ใช้งาน/ไม่ใช้งาน) >>>เพิ่มจากของเดิม


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

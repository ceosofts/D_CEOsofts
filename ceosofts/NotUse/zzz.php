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
        // ตรวจสอบว่ามีตาราง prefixes อยู่แล้วหรือไม่ก่อนที่จะสร้าง
        if (!Schema::hasTable('prefixes')) {
            Schema::create('prefixes', function (Blueprint $table) {
                $table->id();
                $table->string('prefix_th');
                $table->string('prefix_en')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('description')->nullable();
                $table->timestamps();
            });
        } else {
            // ตรวจสอบคอลัมน์ที่อาจขาดและต้องการเพิ่ม
            if (!Schema::hasColumn('prefixes', 'description')) {
                Schema::table('prefixes', function (Blueprint $table) {
                    $table->string('description')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ลบตารางในการ rollback เนื่องจากอาจถูกสร้างโดย migration อื่น
        // และจะถูกจัดการในไฟล์ 2023_12_01_000000_create_complete_schema.php
    }
};

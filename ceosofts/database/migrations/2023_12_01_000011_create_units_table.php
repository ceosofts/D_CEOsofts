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
        // ตรวจสอบว่ามีตาราง units อยู่แล้วหรือไม่
        if (!Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->string('unit_name_th', 50)->unique();
                $table->string('unit_name_en', 50)->nullable();
                $table->string('description', 255)->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('unit_code', 10)->nullable();
                $table->timestamps();
            });
        } 
        // หากมีตารางแล้ว ตรวจสอบคอลัมน์ที่อาจจำเป็นต้องเพิ่ม
        else {
            // เพิ่มคอลัมน์ unit_code หากยังไม่มี
            if (!Schema::hasColumn('units', 'unit_code')) {
                Schema::table('units', function (Blueprint $table) {
                    $table->string('unit_code', 10)->nullable();
                });
            }
            
            // เพิ่มคอลัมน์ description หากยังไม่มี
            if (!Schema::hasColumn('units', 'description')) {
                Schema::table('units', function (Blueprint $table) {
                    $table->string('description', 255)->nullable();
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
    }
};

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
        // ตรวจสอบว่ามีตาราง item_statuses อยู่แล้วหรือไม่
        if (!Schema::hasTable('item_statuses')) {
            Schema::create('item_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->string('code', 20)->nullable();
                $table->string('description', 255)->nullable();
                $table->string('color', 20)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
        // ถ้ามีตารางแล้ว อาจเพิ่มคอลัมน์ที่จำเป็น
        else {
            // ตรวจสอบและเพิ่มคอลัมน์ color ถ้ายังไม่มี
            if (!Schema::hasColumn('item_statuses', 'color')) {
                Schema::table('item_statuses', function (Blueprint $table) {
                    $table->string('color', 20)->nullable();
                });
            }
            
            // ตรวจสอบและเพิ่มคอลัมน์ code ถ้ายังไม่มี
            if (!Schema::hasColumn('item_statuses', 'code')) {
                Schema::table('item_statuses', function (Blueprint $table) {
                    $table->string('code', 20)->nullable();
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
        // ล้อตามรูปแบบที่ทำกับ departments และ positions
        
        // หากต้องการเพิ่มการลบคอลัมน์ที่เพิ่มเข้าไปใหม่:
        if (Schema::hasTable('item_statuses')) {
            if (Schema::hasColumn('item_statuses', 'color')) {
                Schema::table('item_statuses', function (Blueprint $table) {
                    $table->dropColumn('color');
                });
            }
            if (Schema::hasColumn('item_statuses', 'code')) {
                Schema::table('item_statuses', function (Blueprint $table) {
                    $table->dropColumn('code');
                });
            }
        }
    }
};

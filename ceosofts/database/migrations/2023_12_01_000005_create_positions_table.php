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
        // ตรวจสอบว่ามีตาราง positions อยู่แล้วหรือไม่ก่อนที่จะพยายามสร้าง
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('code', 255)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
        
        // เพิ่ม position_id เป็น foreign key ให้ employees หรือ users ตามต้องการ
        // ตัวอย่าง: เพิ่ม position_id ให้ users (ถ้าต้องการ)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'position_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('position_id')->nullable();
                $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ลบ foreign key ก่อน (ถ้ามีการเพิ่ม)
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'position_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['position_id']);
                $table->dropColumn('position_id');
            });
        }
        
        // หมายเหตุ: เราไม่ลบตาราง positions เพราะอาจถูกสร้างโดย migration อื่น
        // เราจะให้ 2023_12_01_000000_create_complete_schema.php จัดการการลบในขั้นตอน rollback
    }
};

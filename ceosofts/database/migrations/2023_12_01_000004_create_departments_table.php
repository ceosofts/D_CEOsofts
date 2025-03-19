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
        // ตรวจสอบว่ามีตาราง departments อยู่แล้วหรือไม่ก่อนที่จะพยายามสร้าง
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->timestamps();
            });
        }
        
        // Add department_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
        // อย่าลบตาราง departments ในการ rollback เพราะอาจจะถูกสร้างจาก migration อื่น
        // แทนที่จะใช้ Schema::dropIfExists('departments');
        // เราจะปล่อยให้ migration ไฟล์ 2023_12_01_000000_create_complete_schema.php จัดการการลบ
    }
};

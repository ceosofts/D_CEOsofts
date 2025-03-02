<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name'); // ชื่อ
            $table->string('last_name');  // นามสกุล
            $table->string('email')->unique(); // อีเมล (ไม่ซ้ำ)
            $table->string('national_id')->unique(); // หมายเลขบัตรประชาชน (ไม่ซ้ำ)
            $table->string('driver_license')->nullable(); // ใบขับขี่ (ถ้ามี)
            $table->date('date_of_birth')->nullable(); // วันเกิด
            $table->integer('age')->nullable(); // อายุ
            $table->string('phone')->nullable(); // เบอร์โทรศัพท์
            $table->text('address')->nullable(); // ที่อยู่
            $table->string('emergency_contact_name')->nullable(); // ผู้ติดต่อฉุกเฉิน
            $table->string('emergency_contact_phone')->nullable(); // เบอร์โทรฉุกเฉิน
            $table->string('spouse_name')->nullable(); // ชื่อคู่สมรส
            $table->json('children')->nullable(); // บันทึกข้อมูลลูกในรูปแบบ JSON
            $table->decimal('tax_deductions', 10, 2)->default(0.00); // รายการหักภาษี

            // คอลัมน์ department_id ต้องเป็น unsignedBigInteger
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');

            // คอลัมน์ position_id เป็น unsignedBigInteger (nullable)
            $table->unsignedBigInteger('position_id')->nullable();
            $table->foreign('position_id')
                ->references('id')
                ->on('positions')
                ->onDelete('set null');

            $table->decimal('salary', 10, 2)->nullable(); // เงินเดือน
            $table->enum('employment_status', ['active', 'resigned', 'terminated', 'on_leave'])->default('active');
            $table->date('hire_date')->nullable(); // วันที่เริ่มงาน
            $table->date('resignation_date')->nullable(); // วันที่ลาออก (ถ้ามี)

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};

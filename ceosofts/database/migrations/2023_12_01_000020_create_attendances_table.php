<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->string('status')->default('normal'); // normal, absent, late, leave, etc.
            $table->decimal('work_hours', 5, 2)->default(0); // จำนวนชั่วโมงที่ทำงาน
            $table->decimal('overtime_hours', 5, 2)->default(0); // จำนวนชั่วโมง OT
            $table->boolean('work_hours_completed')->default(false); // ทำงานครบชั่วโมงหรือไม่
            $table->text('notes')->nullable(); // บันทึกเพิ่มเติม
            $table->timestamps();
            
            // ป้องกันการบันทึกข้อมูลซ้ำในวันเดียวกัน
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
}

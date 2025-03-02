<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePayrollsMonthYearFormat extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // เปลี่ยนคอลัมน์ month_year ให้เป็น string(7)
            $table->string('month_year', 7)->change();
        });

        // คุณอาจจะรันการอัปเดต record เก่าใน Tinker หรือเขียน logic ใน migration (ถ้าจำนวนน้อย)
        // ตัวอย่าง (รันใน Tinker):
        // $payrolls = \App\Models\Payroll::all();
        // foreach($payrolls as $payroll) {
        //     $parts = explode('-', $payroll->month_year); // คาดว่าได้ ["February", "2025"]
        //     if(count($parts) == 2) {
        //         $monthName = $parts[0]; // "February"
        //         $year = $parts[1];      // "2025"
        //         $monthNum = \Carbon\Carbon::parse($monthName)->format('m'); // "02"
        //         $newValue = $year . '-' . $monthNum; // "2025-02"
        //         $payroll->month_year = $newValue;
        //         $payroll->save();
        //     }
        // }
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // หากต้องการกลับไปรูปแบบเดิม (คุณอาจเปลี่ยนเป็น text หรือ string ที่ไม่มีความยาวจำกัด)
            $table->string('month_year')->change();
        });
    }
}

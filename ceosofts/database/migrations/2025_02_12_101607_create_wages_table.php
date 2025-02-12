<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('wages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('work_days');
            $table->decimal('daily_wage', 10, 2);
            $table->decimal('total_wage', 10, 2);
            $table->decimal('ot_hours', 10, 2)->default(0);
            $table->decimal('ot_pay', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->string('status')->default('pending'); // สถานะการจ่ายเงิน
            $table->date('month_year'); // ใช้ "YYYY-MM" เก็บข้อมูลแต่ละเดือน
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wages');
    }
};

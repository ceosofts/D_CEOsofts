<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('month_year')->nullable();
            $table->decimal('salary', 10, 2)->default(0);
            $table->decimal('allowance', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('overtime', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('transport', 10, 2)->default(0);
            $table->decimal('special_severance_pay', 10, 2)->default(0);
            $table->decimal('other_income', 10, 2)->default(0);
            $table->decimal('total_income', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('social_fund', 10, 2)->default(0);
            $table->decimal('provident_fund', 10, 2)->default(0);
            $table->decimal('telephone_bill', 10, 2)->default(0);
            $table->decimal('house_rental', 10, 2)->default(0);
            $table->decimal('no_pay_leave', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('net_income', 10, 2)->default(0);
            $table->decimal('ytd_income', 10, 2)->default(0);
            $table->decimal('ytd_tax', 10, 2)->default(0);
            $table->decimal('ytd_social_fund', 10, 2)->default(0);
            $table->decimal('ytd_provident_fund', 10, 2)->default(0);
            $table->text('remarks')->nullable();

            // สร้างความสัมพันธ์กับ employees
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
};

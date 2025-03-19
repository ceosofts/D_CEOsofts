<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('holiday_name');
            $table->date('holiday_date');
            $table->text('description')->nullable();
            $table->boolean('is_annual')->default(false); // วันหยุดประจำปี (ซ้ำทุกปี) หรือไม่
            $table->boolean('is_active')->default(true);
            $table->year('holiday_year')->nullable(); // ปีของวันหยุด (กรณีไม่ใช่วันหยุดประจำปี)
            $table->timestamps();
            
            // ป้องกันซ้ำในวันเดียวกัน
            $table->unique(['holiday_date', 'holiday_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_holidays');
    }
}

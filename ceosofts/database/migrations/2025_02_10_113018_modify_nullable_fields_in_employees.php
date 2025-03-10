<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable()->change(); // ทำให้ email เป็น nullable
            $table->string('national_id')->nullable()->change(); // ทำให้ national_id เป็น nullable

            // เปลี่ยน department_id เป็น unsignedBigInteger และทำให้ nullable
            $table->unsignedBigInteger('department_id')->nullable()->change();

            $table->string('employment_status')->nullable()->change(); // ทำให้ employment_status เป็น nullable
            $table->decimal('tax_deductions', 10, 2)->nullable()->default(0)->change(); // ทำให้ tax_deductions เป็น nullable พร้อม default 0
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('national_id')->nullable(false)->change();

            // เปลี่ยนกลับ department_id ให้เป็น unsignedBigInteger แบบ not nullable
            $table->unsignedBigInteger('department_id')->nullable(false)->change();

            $table->string('employment_status')->nullable(false)->change();
            $table->decimal('tax_deductions', 10, 2)->nullable(false)->default(0)->change();
        });
    }
};

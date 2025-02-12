<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('work_hours_completed')->default(false)->after('work_hours'); // ทำงานครบ 8 ชม. หรือไม่
            $table->decimal('overtime_hours', 5, 2)->default(0)->after('work_hours_completed'); // ชั่วโมง OT
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['work_hours_completed', 'overtime_hours']);
        });
    }
};

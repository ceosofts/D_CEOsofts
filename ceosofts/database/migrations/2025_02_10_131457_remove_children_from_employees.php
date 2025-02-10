<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('children'); // ✅ ลบคอลัมน์ children
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->json('children')->nullable(); // ✅ กรณี rollback จะคืนคอลัมน์กลับมา
        });
    }
};

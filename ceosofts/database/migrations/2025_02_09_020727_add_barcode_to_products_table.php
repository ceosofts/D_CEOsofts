<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->unique()->nullable()->after('sku'); // ✅ เพิ่มคอลัมน์ barcode
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('barcode'); // ✅ ลบคอลัมน์ barcode หาก rollback
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prefixes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // ✅ เพิ่มข้อมูลพื้นฐาน
        DB::table('prefixes')->insert([
            ['name' => 'นาย', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'นางสาว', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'นาง', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'เด็กชาย', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'เด็กหญิง', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('prefixes');
    }
};

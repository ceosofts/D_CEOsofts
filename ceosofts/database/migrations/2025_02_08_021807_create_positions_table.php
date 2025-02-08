<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ชื่อตำแหน่ง เช่น CEO, Manager
            $table->timestamps();
        });

        // เพิ่มข้อมูลเริ่มต้น (Seed Data)
        \Illuminate\Support\Facades\DB::table('positions')->insert([
            ['name' => 'พนักงาน'],
            ['name' => 'หัวหน้างาน'],
            ['name' => 'หัวหน้าแผนก'],
            ['name' => 'หัวหน้าฝ่าย'],
            ['name' => 'ผู้จัดการ'],
            ['name' => 'CEO']
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};

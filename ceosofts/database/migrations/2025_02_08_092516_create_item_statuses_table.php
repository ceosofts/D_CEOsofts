<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ชื่อสถานะ เช่น "กำลังส่งของ"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_statuses');
    }
};

<?php

use Illuminate\Database\Migrations\Migration; // Import the Migration class
use Illuminate\Database\Schema\Blueprint; // Import the Blueprint class
use Illuminate\Support\Facades\Schema; // Import the Schema facade

return new class extends Migration // Return a new anonymous class that extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // Define the up method to create the table
    {
        Schema::create('customers', function (Blueprint $table) { // Create the 'customers' table
            $table->id(); // Add an auto-incrementing ID column


            $table->string('name'); // ชื่อลูกค้า >>>เพิ่มจากของเดิม
            $table->string('email')->unique(); // อีเมล ต้องไม่ซ้ำ >>>เพิ่มจากของเดิม
            $table->string('phone')->nullable(); // เบอร์โทรศัพท์ (ไม่จำเป็นต้องมี) >>>เพิ่มจากของเดิม
            $table->string('address')->nullable(); // ที่อยู่ (ไม่จำเป็นต้องมี) >>>เพิ่มจากของเดิม
            $table->string('taxid')->nullable(); //  taxid (ไม่จำเป็นต้องมี) >>>เพิ่มจากของเดิม


            $table->timestamps(); // Add created_at and updated_at timestamp columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void // Define the down method to drop the table
    {
        Schema::dropIfExists('customers'); // Drop the 'customers' table if it exists
    }
};

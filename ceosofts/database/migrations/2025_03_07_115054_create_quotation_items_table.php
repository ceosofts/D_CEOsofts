<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->string('item_no')->nullable();     // ลำดับสินค้า (No.)
            $table->text('description')->nullable();   // รายละเอียดสินค้า
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('net_price', 12, 2)->default(0); // quantity * unit_price

            $table->timestamps();

            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotation_items');
    }
};

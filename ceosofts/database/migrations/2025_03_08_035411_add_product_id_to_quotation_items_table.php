<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('item_no');

            // ถ้าอยากทำ Foreign Key:
            // $table->foreign('product_id')
            //       ->references('id')->on('products')
            //       ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            // ถ้าเคยทำ FK ต้อง dropForeign ก่อน
            // $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};

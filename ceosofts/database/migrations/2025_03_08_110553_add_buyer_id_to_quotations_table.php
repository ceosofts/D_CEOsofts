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
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('buyer_id')->nullable()->after('quotation_date');

            // ถ้าต้องการทำ Foreign Key จริงๆ ก็เพิ่ม:
            // $table->foreign('buyer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // ถ้าทำ foreign key ด้วย ต้อง drop foreign key ก่อน
            // $table->dropForeign(['buyer_id']);

            $table->dropColumn('buyer_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuyerFieldsToQuotationsTable extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // เพิ่มคอลัมน์สำหรับเก็บชื่อบริษัทลูกค้าและชื่อผู้ติดต่อ
            $table->string('buyer_company')->nullable()->after('buyer_email');
            $table->string('buyer_contact_name')->nullable()->after('buyer_company');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['buyer_company', 'buyer_contact_name']);
        });
    }
}

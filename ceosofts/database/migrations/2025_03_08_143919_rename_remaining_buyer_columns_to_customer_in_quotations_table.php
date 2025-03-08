<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->renameColumn('buyer_id', 'customer_id');
            $table->renameColumn('buyer_company', 'customer_company');
            $table->renameColumn('buyer_contact_name', 'customer_contact_name');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'buyer_id');
            $table->renameColumn('customer_company', 'buyer_company');
            $table->renameColumn('customer_contact_name', 'buyer_contact_name');
        });
    }
};

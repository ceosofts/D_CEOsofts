<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->renameColumn('buyer_name', 'customer_name');
            $table->renameColumn('buyer_address', 'customer_address');
            $table->renameColumn('buyer_phone', 'customer_phone');
            $table->renameColumn('buyer_fax', 'customer_fax');
            $table->renameColumn('buyer_email', 'customer_email');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->renameColumn('customer_name', 'buyer_name');
            $table->renameColumn('customer_address', 'buyer_address');
            $table->renameColumn('customer_phone', 'buyer_phone');
            $table->renameColumn('customer_fax', 'buyer_fax');
            $table->renameColumn('customer_email', 'buyer_email');
        });
    }
};

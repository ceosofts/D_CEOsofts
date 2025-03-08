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
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('name', 'companyname');
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('companyname', 'name');
        });
    }
};

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
        Schema::table('wages', function (Blueprint $table) {
            $table->decimal('daily_wage', 10, 2)->default(0.00)->change();
            $table->decimal('total_wage', 10, 2)->default(0.00)->change();
            $table->decimal('grand_total', 10, 2)->default(0.00)->change();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wages', function (Blueprint $table) {
            //
        });
    }
};

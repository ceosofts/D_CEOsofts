<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('accumulate_provident_fund', 10, 2)->default(0)->after('ytd_provident_fund');
            $table->decimal('accumulate_social_fund', 10, 2)->default(0)->after('accumulate_provident_fund');
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('accumulate_provident_fund');
            $table->dropColumn('accumulate_social_fund');
        });
    }
};

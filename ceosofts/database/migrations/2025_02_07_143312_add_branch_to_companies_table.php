<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'branch')) {
                $table->integer('branch')->nullable();
            }
            if (!Schema::hasColumn('companies', 'branch_description')) {
                $table->string('branch_description')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'branch')) {
                $table->dropColumn('branch');
            }
            if (Schema::hasColumn('companies', 'branch_description')) {
                $table->dropColumn('branch_description');
            }
        });
    }
};

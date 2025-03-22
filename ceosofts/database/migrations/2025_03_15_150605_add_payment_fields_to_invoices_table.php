<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_date')) {
                $table->date('payment_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'payment_amount')) {
                $table->decimal('payment_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'remaining_balance')) {
                $table->decimal('remaining_balance', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'payment_terms')) {
                $table->string('payment_terms')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Only drop foreign keys if they exist
            if (Schema::hasColumn('invoices', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('invoices', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }

            // Drop columns that exist
            $columns = [
                'payment_date', 'payment_method', 'payment_amount',
                'remaining_balance', 'payment_terms', 'due_date',
                'created_by', 'updated_by'
            ];
            
            $existingColumns = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $existingColumns[] = $column;
                }
            }
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};

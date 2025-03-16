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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('quotation_id')->constrained();
            $table->date('invoice_date');
            $table->string('your_ref')->nullable();
            $table->string('our_ref')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('payment_amount', 10, 2); // Amount to be paid
            $table->decimal('remaining_balance', 10, 2); // Remaining balance
            $table->decimal('payment_percentage', 5, 2); // Payment percentage (e.g., 50.00)
            $table->string('amount_in_words');
            $table->string('payment_terms')->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('job_statuses');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            // Seller (Company) Info
            $table->string('seller_company')->nullable();  // ชื่อบริษัทผู้ขาย
            $table->text('seller_address')->nullable();
            $table->string('seller_phone')->nullable();
            $table->string('seller_fax')->nullable();
            $table->string('seller_line')->nullable();
            $table->string('seller_email')->nullable();

            // Quotation Info
            $table->string('quotation_number')->unique();  // เช่น Q-202503001
            $table->date('quotation_date');                // วันที่ออกใบเสนอราคา

            // Buyer Info
            $table->string('buyer_name')->nullable();
            $table->text('buyer_address')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->string('buyer_fax')->nullable();
            $table->string('buyer_email')->nullable();

            // Reference
            $table->string('your_ref')->nullable();        // Your Ref
            $table->string('our_ref')->nullable();         // Our Ref

            // Summary
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('amount_in_words')->nullable(); // เช่น "THIRTEEN THOUSAND ONLY"

            // Conditions
            $table->string('delivery')->nullable();
            $table->string('warranty')->nullable();
            $table->string('validity')->nullable();
            $table->string('payment')->nullable();

            // Signature
            $table->string('prepared_by')->nullable();     // เช่น "Customer Service Management"
            $table->string('sales_engineer')->nullable();  // เช่น "Tryporn T."

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotations');
    }
};

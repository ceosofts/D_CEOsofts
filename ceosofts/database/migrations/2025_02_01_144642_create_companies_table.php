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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->unique();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('fax', 20)->nullable();


            $table->string('email')->nullable();
            $table->string('website')->nullable();

            $table->string('logo')->nullable();

            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            
            $table->string('tiktok')->nullable();
           
            $table->string('facebook')->nullable();
            $table->string('line')->nullable();

            $table->string('tax_id')->nullable();
            $table->string('contact_person')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

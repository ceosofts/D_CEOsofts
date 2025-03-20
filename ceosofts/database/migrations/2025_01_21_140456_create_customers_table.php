<?php

use Illuminate\Database\Migrations\Migration; // Import the Migration class
use Illuminate\Database\Schema\Blueprint; // Import the Blueprint class
use Illuminate\Support\Facades\Schema; // Import the Schema facade

return new class extends Migration // Return a new anonymous class that extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // Define the up method to create the table
    {
        Schema::create('customers', function (Blueprint $table) { // Create the 'customers' table
            $table->id(); // Add an auto-incrementing ID column
            $table->string('code')->unique();
            $table->string('companyname');
            $table->string('contact_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('branch')->nullable();
            $table->string('taxid')->nullable();  // Add this line
            $table->timestamps(); // Add created_at and updated_at timestamp columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void // Define the down method to drop the table
    {
        Schema::dropIfExists('customers'); // Drop the 'customers' table if it exists
    }
};

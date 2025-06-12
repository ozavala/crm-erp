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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->morphs('addressable'); // This will create addressable_id and addressable_type
            $table->string('address_type')->nullable()->comment('e.g., Billing, Shipping, Primary, Mailing'); // Type of address
            $table->string('street_address_line_1');
            $table->string('street_address_line_2')->nullable();
            $table->string('city');
            $table->string('state_province')->nullable(); // State or Province
            $table->string('postal_code');
            $table->string('country_code', 2)->default('US'); // ISO 3166-1 alpha-2 country code
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
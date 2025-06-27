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
        Schema::table('contacts', function (Blueprint $table) {
            // It's safer to drop the foreign key before the column
            // Assuming the foreign key constraint is named 'contacts_customer_id_foreign'
            // You might need to check your actual constraint name
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');

            $table->morphs('contactable'); // Adds contactable_id and contactable_type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropMorphs('contactable');
            
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('customer_id')->on('customers');
        });
    }
};

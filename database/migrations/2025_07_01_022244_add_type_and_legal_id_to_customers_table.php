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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('type', 50)->default('Person')->after('customer_id');
            $table->string('legal_id', 100)->unique()->after('company_name');

            // Make name fields nullable to support both types
            $table->string('first_name', 100)->nullable()->change();
            $table->string('last_name', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('legal_id'); // The unique index is typically dropped with the column

            // Reverting the nullable change is omitted for safety,
            // as there might be company records with null names.
            $table->string('first_name', 100)->nullable(false)->change();
            $table->string('last_name', 100)->nullable(false)->change();
        });
    }
};

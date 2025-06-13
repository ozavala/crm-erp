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
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('subtotal'); // 'percentage' or 'fixed'
            $table->decimal('discount_value', 15, 2)->nullable()->after('discount_type');
            $table->decimal('tax_percentage', 5, 2)->nullable()->after('discount_amount'); // Store tax ra
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'tax_percentage']);
        });
    }
};

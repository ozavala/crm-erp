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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_taxable')->default(true)->after('tax_rate_id');
            $table->decimal('tax_rate_percentage', 5, 2)->nullable()->after('is_taxable');
            $table->string('tax_category')->nullable()->after('tax_rate_percentage'); // 'goods', 'services', 'transport', 'insurance', 'storage'
            $table->string('tax_country_code', 3)->default('EC')->after('tax_category'); // Código de país para IVA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_taxable', 'tax_rate_percentage', 'tax_category', 'tax_country_code']);
        });
    }
};

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
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('quotation_id')->references('quotation_id')->on('quotations')->onDelete('set null');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('tax_rate_id')->references('tax_rate_id')->on('tax_rates')->onDelete('set null');
            $table->foreign('created_by_user_id')->references('user_id')->on('crm_users')->onDelete('set null');
            $table->foreign('owner_company_id')->references('id')->on('owner_companies');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->foreign('opportunity_id')->references('opportunity_id')->on('opportunities')->onDelete('cascade');
            $table->foreign('tax_rate_id')->references('tax_rate_id')->on('tax_rates')->onDelete('set null');
            $table->foreign('created_by_user_id')->references('user_id')->on('crm_users')->onDelete('set null');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
            $table->foreign('shipping_address_id')->references('address_id')->on('addresses')->onDelete('set null');
            $table->foreign('tax_rate_id')->references('tax_rate_id')->on('tax_rates')->onDelete('set null');
            $table->foreign('created_by_user_id')->references('user_id')->on('crm_users')->onDelete('set null');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('created_by_user_id')->references('user_id')->on('crm_users')->onDelete('set null');
            $table->foreign('product_category_id')->references('category_id')->on('product_categories')->onDelete('set null');
            $table->foreign('tax_rate_id')->references('tax_rate_id')->on('tax_rates')->onDelete('set null');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreign('created_by_user_id')->references('user_id')->on('crm_users');
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->foreign('journal_entry_id')->references('journal_entry_id')->on('journal_entries')->onDelete('cascade');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->foreign('purchase_order_id')->references('purchase_order_id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
            $table->foreign('created_by_user_id')->references('user_id')->on('crm_users');
            $table->foreign('owner_company_id')->references('id')->on('owner_companies');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('created_by_user_id')->references('user_id')->on('crm_users')->onDelete('set null');
            $table->foreign('owner_company_id')->references('id')->on('owner_companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['quotation_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['tax_rate_id']);
            $table->dropForeign(['created_by_user_id']);
            $table->dropForeign(['owner_company_id']);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['opportunity_id']);
            $table->dropForeign(['tax_rate_id']);
            $table->dropForeign(['created_by_user_id']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['shipping_address_id']);
            $table->dropForeign(['tax_rate_id']);
            $table->dropForeign(['created_by_user_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropForeign(['product_category_id']);
            $table->dropForeign(['tax_rate_id']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropForeign(['journal_entry_id']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['created_by_user_id']);
            $table->dropForeign(['owner_company_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropForeign(['owner_company_id']);
        });
    }
};
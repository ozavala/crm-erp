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
            $table->id('invoice_id');
            $table->unsignedBigInteger('owner_company_id')->nullable();
            $table->foreignId('order_id')->nullable();
            $table->foreignId('quotation_id')->nullable();
            $table->foreignId('customer_id');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('status')->default('Draft');
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->foreignId('tax_rate_id')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('amount_paid', 15, 2)->default(0.00);
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
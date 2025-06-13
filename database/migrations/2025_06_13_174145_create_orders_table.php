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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->foreignId('quotation_id')->nullable()->constrained('quotations', 'quotation_id')->onDelete('set null');
            $table->foreignId('opportunity_id')->nullable()->constrained('opportunities', 'opportunity_id')->onDelete('set null');
            // Consider using polymorphic relationship for addresses if they are stored in the 'addresses' table
            $table->unsignedBigInteger('shipping_address_id')->nullable();
            $table->unsignedBigInteger('billing_address_id')->nullable();
            $table->string('order_number')->unique()->nullable(); // Auto-generate or manual
            $table->date('order_date');
            $table->string('status')->default('Pending'); // e.g., Pending, Processing, Shipped, Delivered, Cancelled
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('crm_users', 'user_id')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
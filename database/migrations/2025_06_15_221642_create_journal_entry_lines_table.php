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
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id('journal_entry_line_id');
            $table->foreignId('journal_entry_id')->constrained('journal_entries', 'journal_entry_id')->onDelete('cascade');
            // Later, this would be foreignId('account_id')->constrained('chart_of_accounts')
            $table->string('account_name'); // Simplified for now: e.g., "Cash", "Accounts Receivable"
            $table->decimal('debit_amount', 15, 2)->default(0.00);
            $table->decimal('credit_amount', 15, 2)->default(0.00);
            $table->nullablemorphs('entity'); // Optional: For sub-ledger tracking (e.g., Customer, Supplier)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};

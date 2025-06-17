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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id('journal_entry_id');
            $table->date('entry_date');
            $table->string('transaction_type')->nullable()->comment('e.g., Payment, Invoice, Bill, Manual Journal');
            $table->text('description')->nullable();
            $table->nullableMorphs('referenceable'); // Creates nullable referenceable_id and referenceable_type
            $table->foreignId('created_by_user_id')->nullable()->constrained('crm_users', 'user_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
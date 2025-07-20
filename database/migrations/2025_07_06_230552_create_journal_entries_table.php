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
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by_user_id')->constrained('crm_users', 'user_id');
            $table->string('transaction_type')->nullable();
            $table->unsignedBigInteger('referenceable_id')->nullable();
            $table->string('referenceable_type')->nullable();
            $table->unsignedBigInteger('owner_company_id')->nullable();
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

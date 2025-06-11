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
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('activity_id');
            $table->foreignId('lead_id')->constrained('leads', 'lead_id')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('crm_users', 'user_id')->onDelete('set null'); // User who performed/logged activity
            $table->string('type', 50); // e.g., Call, Email, Meeting, Note
            $table->text('description');
            $table->timestamp('activity_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
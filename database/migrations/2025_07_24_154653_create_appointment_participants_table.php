<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Handle foreign key checks based on database driver
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        Schema::create('appointment_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->string('participant_type', 50); // 'crm_user', 'customer', 'contact'
            $table->unsignedBigInteger('participant_id');
            $table->boolean('is_organizer')->default(false);
            $table->string('response_status', 50)->default('pending'); // 'pending', 'accepted', 'declined'
            $table->timestamps();

            // Foreign keys
            $table->foreign('appointment_id')->references('appointment_id')->on('appointments')->onDelete('cascade');
            
            // Create an index on the polymorphic relationship
            $table->index(['participant_type', 'participant_id']);
        });

        // Re-enable foreign key checks for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Handle foreign key checks based on database driver
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        Schema::dropIfExists('appointment_participants');

        // Re-enable foreign key checks for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};

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

        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id('calendar_event_id');
            $table->unsignedBigInteger('owner_company_id');
            $table->string('google_calendar_id');
            $table->string('google_event_id');
            $table->string('related_type', 50)->nullable(); // 'appointment', 'task'
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('sync_status', 50)->default('synced'); // 'synced', 'pending', 'failed'
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('owner_company_id')->references('id')->on('owner_companies');
            
            // Create an index on the polymorphic relationship
            $table->index(['related_type', 'related_id']);
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

        Schema::dropIfExists('calendar_events');

        // Re-enable foreign key checks for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};

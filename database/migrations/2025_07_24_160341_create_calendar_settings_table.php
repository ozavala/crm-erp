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

        Schema::create('calendar_settings', function (Blueprint $table) {
            $table->id('calendar_setting_id');
            $table->unsignedBigInteger('owner_company_id');
            $table->unsignedBigInteger('user_id')->nullable(); // null for company-wide settings
            $table->string('google_calendar_id')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('auto_sync')->default(true);
            $table->integer('sync_frequency_minutes')->default(60);
            $table->timestamps();

            // Foreign keys
            $table->foreign('owner_company_id')->references('id')->on('owner_companies');
            $table->foreign('user_id')->references('user_id')->on('crm_users')->onDelete('cascade');
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

        Schema::dropIfExists('calendar_settings');

        // Re-enable foreign key checks for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};

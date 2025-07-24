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
        
        Schema::table('tasks', function (Blueprint $table) {
            // First add the column if it doesn't exist
            if (!Schema::hasColumn('tasks', 'owner_company_id')) {
                $table->unsignedBigInteger('owner_company_id')->nullable()->after('task_id');
            }
            
            // Then add the foreign key constraint
            $table->foreign('owner_company_id')->references('id')->on('owner_companies');
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
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['owner_company_id']);
            // Don't drop the column since it might be used elsewhere
        });
        
        // Re-enable foreign key checks for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};

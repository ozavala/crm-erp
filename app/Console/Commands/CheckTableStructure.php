<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckTableStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:table-structure {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the structure of a table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument('table');
        
        if (!Schema::hasTable($table)) {
            $this->error("Table {$table} does not exist!");
            return 1;
        }

        $columns = Schema::getColumnListing($table);
        $this->info("Columns in table {$table}:");
        foreach ($columns as $column) {
            $this->line("- {$column}");
        }

        return 0;
    }
}

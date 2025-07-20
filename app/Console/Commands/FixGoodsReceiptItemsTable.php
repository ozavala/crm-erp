<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixGoodsReceiptItemsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:goods-receipt-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add missing columns to goods_receipt_items table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking goods_receipt_items table structure...');
        
        $columns = Schema::getColumnListing('goods_receipt_items');
        $this->info('Current columns: ' . implode(', ', $columns));
        
        // Check if columns exist
        $missingColumns = [];
        if (!in_array('unit_cost_with_landed', $columns)) {
            $missingColumns[] = 'unit_cost_with_landed';
        }
        if (!in_array('total_cost', $columns)) {
            $missingColumns[] = 'total_cost';
        }
        if (!in_array('notes', $columns)) {
            $missingColumns[] = 'notes';
        }
        
        if (empty($missingColumns)) {
            $this->info('All required columns already exist!');
            return 0;
        }
        
        $this->info('Missing columns: ' . implode(', ', $missingColumns));
        
        // Add missing columns
        try {
            Schema::table('goods_receipt_items', function ($table) use ($missingColumns) {
                if (in_array('unit_cost_with_landed', $missingColumns)) {
                    $table->decimal('unit_cost_with_landed', 10, 2)->nullable();
                }
                if (in_array('total_cost', $missingColumns)) {
                    $table->decimal('total_cost', 10, 2)->nullable();
                }
                if (in_array('notes', $missingColumns)) {
                    $table->text('notes')->nullable();
                }
            });
            
            $this->info('Columns added successfully!');
            
            // Show updated structure
            $newColumns = Schema::getColumnListing('goods_receipt_items');
            $this->info('Updated columns: ' . implode(', ', $newColumns));
            
        } catch (\Exception $e) {
            $this->error('Error adding columns: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 
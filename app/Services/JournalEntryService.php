<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Supplier;

class JournalEntryService
{
    public static function createForTransaction(Transaction $transaction, int $ownerCompanyId)
    {
        $description = self::getTransactionDescription($transaction);

        $journalEntry = JournalEntry::create([
            'owner_company_id' => $ownerCompanyId,
            'entry_date' => $transaction->date,
            'description' => $description,
            'transaction_type' => ucfirst($transaction->type),
            'created_by_user_id' => $transaction->created_by_user_id,
            'referenceable_id' => $transaction->id,
            'referenceable_type' => Transaction::class,
        ]);

        self::createJournalEntryLines($journalEntry, $transaction);

        $transaction->journal_entry_id = $journalEntry->journal_entry_id;
        $transaction->save();

        return $journalEntry;
    }

    private static function getTransactionDescription(Transaction $transaction): string
    {
        switch ($transaction->type) {
            case 'venta':
                return 'Sale for Invoice #' . ($transaction->invoice_id ?? $transaction->id);
            case 'compra':
                return 'Purchase for Bill #' . ($transaction->bill_id ?? $transaction->id);
            case 'pago':
                return 'Payment for Payment #' . ($transaction->payment_id ?? $transaction->id);
            default:
                return 'Transaction #' . $transaction->id;
        }
    }

    private static function createJournalEntryLines($journalEntry, $transaction)
    {
        // TODO: These account codes should be fetched from company-specific settings
        // rather than being hardcoded.
        $accounts = [
            'sales' => '4000', // Example: Sales Revenue
            'accounts_receivable' => '1200', // Example: Accounts Receivable
            'purchases' => '5000', // Example: Cost of Goods Sold / Expense
            'accounts_payable' => '2000', // Example: Accounts Payable
            'cash' => '1010', // Example: Cash / Bank
        ];

        if ($transaction->type === 'venta') {
            // A sale increases Accounts Receivable (debit) and Sales Revenue (credit).
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->journal_entry_id,
                'account_code' => $accounts['accounts_receivable'],
                'account_name' => 'Accounts Receivable',
                'debit_amount' => $transaction->amount,
                'credit_amount' => 0.00,
                'entity_type' => 'customer',
                'entity_id' => $transaction->customer_id,
            ]);
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->journal_entry_id,
                'account_code' => $accounts['sales'],
                'account_name' => 'Sales Revenue',
                'debit_amount' => 0.00,
                'credit_amount' => $transaction->amount,
            ]);
        } elseif ($transaction->type === 'compra') {
            // A purchase increases an Expense/Asset (debit) and Accounts Payable (credit).
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->journal_entry_id,
                'account_code' => $accounts['purchases'],
                'account_name' => 'Purchases',
                'debit_amount' => $transaction->amount,
                'credit_amount' => 0.00,
            ]);
            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->journal_entry_id,
                'account_code' => $accounts['accounts_payable'],
                'account_name' => 'Accounts Payable',
                'debit_amount' => 0.00,
                'credit_amount' => $transaction->amount,
                'entity_type' => 'supplier',
                'entity_id' => $transaction->supplier_id,
            ]);
        } elseif ($transaction->type === 'pago') {
            if ($transaction->customer_id) { // Payment received from a customer
                // Increases Cash (debit), decreases Accounts Receivable (credit).
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->journal_entry_id,
                    'account_code' => $accounts['cash'],
                    'account_name' => 'Cash',
                    'debit_amount' => $transaction->amount,
                    'credit_amount' => 0.00,
                ]);
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->journal_entry_id,
                    'account_code' => $accounts['accounts_receivable'],
                    'account_name' => 'Accounts Receivable',
                    'debit_amount' => 0.00,
                    'credit_amount' => $transaction->amount,
                    'entity_type' => 'customer',
                    'entity_id' => $transaction->customer_id,
                ]);
            } elseif ($transaction->supplier_id) { // Payment made to a supplier
                // Decreases Accounts Payable (debit) and decreases Cash (credit).
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->journal_entry_id,
                    'account_code' => $accounts['accounts_payable'],
                    'account_name' => 'Accounts Payable',
                    'debit_amount' => $transaction->amount,
                    'credit_amount' => 0.00,
                    'entity_type' => 'supplier',
                    'entity_id' => $transaction->supplier_id,
                ]);
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->journal_entry_id,
                    'account_code' => $accounts['cash'],
                    'account_name' => 'Cash',
                    'debit_amount' => 0.00,
                    'credit_amount' => $transaction->amount,
                ]);
            }
        }
    }

}
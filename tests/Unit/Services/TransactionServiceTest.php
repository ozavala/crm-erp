<?php

namespace Tests\Unit\Services;

use App\Models\Invoice;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\OwnerCompany;
use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_transaction_from_invoice_creates_journal_entry()
    {
        $company = OwnerCompany::factory()->create();
        $invoice = Invoice::factory()->create([
            'owner_company_id' => $company->id,
            'total_amount' => 1000,
        ]);

        $transaction = TransactionService::createFromInvoice($invoice, $company->id);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'owner_company_id' => $company->id,
            'type' => 'venta',
            'amount' => 1000,
            'invoice_id' => $invoice->invoice_id,
            'created_by_user_id' => $invoice->created_by_user_id,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'owner_company_id' => $company->id,
            'referenceable_id' => $transaction->id,
            'referenceable_type' => Transaction::class,
        ]);

        $journalEntry = JournalEntry::where('referenceable_id', $transaction->id)
            ->where('referenceable_type', Transaction::class)
            ->first();
        $this->assertCount(2, $journalEntry->lines);
    }

    public function test_create_transaction_from_bill_creates_journal_entry()
    {
        $company = OwnerCompany::factory()->create();
        $bill = Bill::factory()->create([
            'owner_company_id' => $company->id,
            'total_amount' => 500,
        ]);

        $transaction = TransactionService::createFromBill($bill, $company->id);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'owner_company_id' => $company->id,
            'type' => 'compra',
            'amount' => 500,
            'bill_id' => $bill->bill_id,
            'created_by_user_id' => $bill->created_by_user_id,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'owner_company_id' => $company->id,
            'referenceable_id' => $transaction->id,
            'referenceable_type' => Transaction::class,
        ]);

        $journalEntry = JournalEntry::where('referenceable_id', $transaction->id)
            ->where('referenceable_type', Transaction::class)
            ->first();
        $this->assertCount(2, $journalEntry->lines);
    }

    public function test_create_transaction_from_payment_creates_journal_entry()
    {
        $company = OwnerCompany::factory()->create();
        $payment = Payment::factory()->create([
            'owner_company_id' => $company->id,
            'amount' => 250,
        ]);

        $transaction = TransactionService::createFromPayment($payment, $company->id);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'owner_company_id' => $company->id,
            'type' => 'pago',
            'amount' => 250,
            'payment_id' => $payment->payment_id,
            'created_by_user_id' => $payment->created_by_user_id,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'owner_company_id' => $company->id,
            'referenceable_id' => $transaction->id,
            'referenceable_type' => Transaction::class,
           
        ]);

        $journalEntry = JournalEntry::where('referenceable_id', $transaction->id)
            ->where('referenceable_type', Transaction::class)
            ->first();
        $this->assertCount(2, $journalEntry->lines);
    }
    
}

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
            'owner_company_id' => $company->owner_company_id,
            'total' => 1000,
        ]);

        $transaction = TransactionService::createFromInvoice($invoice, $company->owner_company_id);

        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $transaction->transaction_id,
            'owner_company_id' => $company->owner_company_id,
            'type' => 'venta',
            'amount' => 1000,
            'invoice_id' => $invoice->invoice_id,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'transaction_id' => $transaction->transaction_id,
            'owner_company_id' => $company->owner_company_id,
        ]);
    }

    public function test_create_transaction_from_bill_creates_journal_entry()
    {
        $company = OwnerCompany::factory()->create();
        $bill = Bill::factory()->create([
            'owner_company_id' => $company->owner_company_id,
            'total' => 500,
        ]);

        $transaction = TransactionService::createFromBill($bill, $company->owner_company_id);

        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $transaction->transaction_id,
            'owner_company_id' => $company->owner_company_id,
            'type' => 'compra',
            'amount' => 500,
            'bill_id' => $bill->bill_id,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'transaction_id' => $transaction->transaction_id,
            'owner_company_id' => $company->owner_company_id,
        ]);
    }

    public function test_create_transaction_from_payment_creates_journal_entry()
    {
        $company = OwnerCompany::factory()->create();
        $payment = Payment::factory()->create([
            'owner_company_id' => $company->owner_company_id,
            'amount' => 250,
        ]);

        $transaction = TransactionService::createFromPayment($payment, $company->owner_company_id);

        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $transaction->transaction_id,
            'owner_company_id' => $company->owner_company_id,
            'type' => 'pago',
            'amount' => 250,
            'payment_id' => $payment->payment_id,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'transaction_id' => $transaction->transaction_id,
            'owner_company_id' => $company->owner_company_id,
        ]);
    }
}

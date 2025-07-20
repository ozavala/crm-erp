<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\OwnerCompany;
use App\Services\JournalEntryService;

class TransactionService
{
    public static function createFromInvoice(Invoice $invoice, $ownerCompanyId)
    {
        $transaction = Transaction::create([
            'owner_company_id' => $ownerCompanyId,
            'type' => 'venta',
            'amount' => $invoice->total,
            'customer_id' => $invoice->customer_id,
            'invoice_id' => $invoice->invoice_id,
            'date' => $invoice->invoice_date,
        ]);
        JournalEntryService::createForTransaction($transaction);
        return $transaction;
    }

    public static function createFromBill(Bill $bill, $ownerCompanyId)
    {
        $transaction = Transaction::create([
            'owner_company_id' => $ownerCompanyId,
            'type' => 'compra',
            'amount' => $bill->total,
            'supplier_id' => $bill->supplier_id,
            'bill_id' => $bill->bill_id,
            'date' => $bill->bill_date,
        ]);
        JournalEntryService::createForTransaction($transaction);
        return $transaction;
    }

    public static function createFromPayment(Payment $payment, $ownerCompanyId)
    {
        $transaction = Transaction::create([
            'owner_company_id' => $ownerCompanyId,
            'type' => 'pago',
            'amount' => $payment->amount,
            'supplier_id' => $payment->supplier_id,
            'customer_id' => $payment->customer_id,
            'payment_id' => $payment->payment_id,
            'date' => $payment->payment_date,
        ]);
        JournalEntryService::createForTransaction($transaction);
        return $transaction;
    }
} 
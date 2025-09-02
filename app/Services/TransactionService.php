<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\OwnerCompany;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public static function createFromInvoice(Invoice $invoice, $ownerCompanyId)
    {
        return DB::transaction(function () use ($invoice, $ownerCompanyId) {
            $transaction = Transaction::create([
                'owner_company_id' => $ownerCompanyId,
                'type' => 'venta',
                'amount' => $invoice->total_amount,
                'customer_id' => $invoice->customer_id,
                'invoice_id' => $invoice->invoice_id,
                'date' => $invoice->invoice_date,
                'created_by_user_id' => $invoice->created_by_user_id,
            ]);

            JournalEntryService::createForTransaction($transaction, $ownerCompanyId);

            return $transaction;
        });
    }

    public static function createFromBill(Bill $bill, $ownerCompanyId)
    {
        return DB::transaction(function () use ($bill, $ownerCompanyId) {
            $transaction = Transaction::create([
                'owner_company_id' => $ownerCompanyId,
                'type' => 'compra',
                'amount' => $bill->total_amount,
                'supplier_id' => $bill->supplier_id,
                'bill_id' => $bill->bill_id,
                'date' => $bill->bill_date,
                'created_by_user_id' => $bill->created_by_user_id,
            ]);

            JournalEntryService::createForTransaction($transaction, $ownerCompanyId);

            return $transaction;
        });
    }

    public static function createFromPayment(Payment $payment, $ownerCompanyId)
    {
        return DB::transaction(function () use ($payment, $ownerCompanyId) {
            $supplier_id = null;
            $customer_id = null;

            if ($payment->payable instanceof Bill) {
                $supplier_id = $payment->payable->supplier_id;
            } elseif ($payment->payable instanceof Invoice) {
                $customer_id = $payment->payable->customer_id;
            }

            $transaction = Transaction::create([
                'owner_company_id' => $ownerCompanyId,
                'type' => 'pago',
                'amount' => $payment->amount,
                'supplier_id' => $supplier_id,
                'customer_id' => $customer_id,
                'payment_id' => $payment->payment_id,
                'date' => $payment->payment_date,
                'created_by_user_id' => $payment->created_by_user_id,
            ]);

            JournalEntryService::createForTransaction($transaction, $ownerCompanyId);

            return $transaction;
        });
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice; // To update invoice amount_paid
use App\Models\PurchaseOrder; // To update PO amount_paid (if implemented directly)
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Http\Requests\StorePaymentRequest;
// We might not need UpdatePaymentRequest if payments are immutable or handled differently
// use App\Http\Requests\UpdatePaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer; // If you need to link payments to customers

class PaymentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * This method will be generic but primarily used via specific routes
     * e.g., POST /invoices/{invoice}/payments
     */
    public function store(StorePaymentRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by_user_id'] = Auth::id();

        $payableType = $validatedData['payable_type'];
        $payableId = $validatedData['payable_id'];

        // Find the payable model (Invoice or PurchaseOrder)
        $payableModel = null;
        if ($payableType === Invoice::class) {
            $payableModel = Invoice::find($payableId);
        } elseif ($payableType === PurchaseOrder::class) {
            // Enable linking payments directly to PurchaseOrder
            $payableModel = PurchaseOrder::find($payableId);
        }

        if (!$payableModel) {
            return back()->with('error', 'Invalid payable entity specified.');
        }

        // Check if payment amount exceeds amount due for Invoices
        if ($payableModel instanceof Invoice) {
            $amountDue = $payableModel->total_amount - $payableModel->amount_paid;
            if ($validatedData['amount'] > $amountDue) {
                return back()->withInput()->with('error', 'Payment amount cannot exceed the amount due.');
            }
        } elseif ($payableModel instanceof PurchaseOrder) {
            $amountDue = $payableModel->amount_due; // Using the accessor from the model
            if ($validatedData['amount'] > $amountDue) {
                return back()->withInput()->with('error', 'Payment amount cannot exceed the amount due for the Purchase Order.');
            }
        }

        DB::transaction(function () use ($validatedData, $payableModel) {
            $payment = Payment::create($validatedData);
            $this->createJournalEntriesForPayment($payment, $payableModel);

            // Update the amount_paid on the payable model (e.g., Invoice)
            if ($payableModel instanceof Invoice) {
                $payableModel->increment('amount_paid', $payment->amount);

                // Optionally, update invoice status based on payment
                if ($payableModel->amount_paid >= $payableModel->total_amount) {
                    $payableModel->status = 'Paid';
                } elseif ($payableModel->amount_paid > 0) {
                    $payableModel->status = 'Partially Paid';
                }
                $payableModel->save();
            }
            // Logic for PurchaseOrder payments
            if ($payableModel instanceof PurchaseOrder) {
                $payableModel->increment('amount_paid', $payment->amount);

                // Optionally, update PO status based on payment
                if ($payableModel->amount_paid >= $payableModel->total_amount) {
                    $payableModel->status = 'Paid';
                } else { // No need for 'Partially Paid' if any payment makes it so, unless starting from 0
                    $payableModel->status = 'Partially Paid';
                }
                $payableModel->save();
            }
        });

        // Redirect back to the payable entity's show page
        if ($payableModel instanceof Invoice) {
            return redirect()->route('invoices.show', $payableModel->invoice_id)
                             ->with('success', 'Payment recorded successfully.');
        } elseif ($payableModel instanceof PurchaseOrder) {
            return redirect()->route('purchase-orders.show', $payableModel->purchase_order_id)
                             ->with('success', 'Payment recorded successfully.');
        }

        return redirect()->back()->with('success', 'Payment recorded successfully.'); // Fallback
    }

    /**
     * Remove the specified resource from storage.
     * Typically, deleting a payment should also adjust the parent's paid amount.
     */
    public function destroy(Payment $payment)
    {
        $payable = $payment->payable; // Get the related Invoice or PurchaseOrder

        DB::transaction(function () use ($payment, $payable) {
            if ($payable instanceof Invoice) {
                $payable->decrement('amount_paid', $payment->amount);
                // TODO: Consider how to reverse or mark journal entries related to this deleted payment.
                // Optionally, update invoice status
                if ($payable->amount_paid <= 0) {
                    $payable->status = 'Sent'; // Or 'Draft' or original status
                    $payable->amount_paid = 0; // Ensure it doesn't go negative
                } elseif ($payable->amount_paid < $payable->total_amount) {
                    $payable->status = 'Partially Paid';
                }
                $payable->save();
            }
            // Logic for deleting a PurchaseOrder payment
            if ($payable instanceof PurchaseOrder) {
                $payable->decrement('amount_paid', $payment->amount);

                // Optionally, update PO status
                if ($payable->amount_paid <= 0) {
                    $payable->status = 'Confirmed'; // Revert to a logical pre-payment status
                    $payable->amount_paid = 0; // Ensure it doesn't go negative
                } else {
                    $payable->status = 'Partially Paid';
                }
                $payable->save();
            }

            $payment->delete();
        });

        if ($payable instanceof Invoice) {
            return redirect()->route('invoices.show', $payable->invoice_id)
                             ->with('success', 'Payment deleted successfully and invoice updated.');
        } elseif ($payable instanceof PurchaseOrder) {
            return redirect()->route('purchase-orders.show', $payable->purchase_order_id)
                             ->with('success', 'Payment deleted successfully and purchase order updated.');
        }

        return redirect()->back()->with('success', 'Payment deleted successfully.'); // Fallback
    }

    /**
     * Create journal entries for a given payment.
     */
    protected function createJournalEntriesForPayment(Payment $payment, Model $payableModel)
    {
        $journalEntry = JournalEntry::create([
            'entry_date' => $payment->payment_date,
            'transaction_type' => 'Payment',
            'description' => "Payment for " . class_basename($payableModel) . " #" . ($payableModel->invoice_number ?? $payableModel->purchase_order_number ?? $payableModel->getKey()),
            'referenceable_id' => $payment->payment_id,
            'referenceable_type' => Payment::class,
            'created_by_user_id' => $payment->created_by_user_id,
        ]);

        if ($payableModel instanceof Invoice) {
            // Payment Received for an Invoice
            // Debit Cash (or Bank), Credit Accounts Receivable
            $journalEntry->lines()->createMany([
                [
                    'account_name' => 'Cash', // Or specific bank account
                    'debit_amount' => $payment->amount,
                    'credit_amount' => 0,
                ],
                [
                    'account_name' => 'Accounts Receivable',
                    'debit_amount' => 0,
                    'credit_amount' => $payment->amount,
                    'entity_id' => $payableModel->customer_id,
                    'entity_type' => Customer::class,
                ]
            ]);
        } elseif ($payableModel instanceof PurchaseOrder) {
            // Payment Made for a Purchase Order (or Bill)
            // Debit Accounts Payable, Credit Cash (or Bank)
            $journalEntry->lines()->createMany([
                [
                    'account_name' => 'Accounts Payable',
                    'debit_amount' => $payment->amount,
                    'credit_amount' => 0,
                    'entity_id' => $payableModel->supplier_id,
                    'entity_type' => \App\Models\Supplier::class,
                ],
                [
                    'account_name' => 'Cash', // Or specific bank account
                    'debit_amount' => 0,
                    'credit_amount' => $payment->amount,
                ]
            ]);
        }
    }

    // Other methods like index, create, show, edit, update might not be directly used
    // for payments in a standalone way, or would need context (e.g., payments for a specific invoice).
}
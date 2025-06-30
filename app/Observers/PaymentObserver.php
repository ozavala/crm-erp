<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     * This is triggered after a payment is saved.
     */
    public function created(Payment $payment): void
    {
        if (method_exists($payment->payable, 'updateStatusAfterPayment')) {
            $payment->payable->updateStatusAfterPayment();
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        if (method_exists($payment->payable, 'updateStatusAfterPayment')) {
            $payment->payable->updateStatusAfterPayment();
        }
    }
}
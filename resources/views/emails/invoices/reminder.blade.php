<x-mail::message>
# Payment Reminder

Hello {{ $invoice->customer->full_name }},

This is a friendly reminder that your invoice **#{{ $invoice->invoice_number }}** for the amount of **${{ number_format($invoice->total_amount, 2) }}** is overdue. The due date was {{ $invoice->due_date->format('Y-m-d') }}.

Your outstanding balance is **${{ number_format($invoice->amount_due, 2) }}**.

<x-mail::button :url="route('invoices.show', $invoice->invoice_id)">
View Invoice
</x-mail::button>

Please make the payment at your earliest convenience. If you have already made the payment, please disregard this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
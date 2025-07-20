<x-mail::message>
# {{ __('emails.invoices.Payment Reminder') }}

{{ __('emails.invoices.Hello') }} {{ $invoice->customer->full_name }},

{{ __('emails.invoices.Reminder body', ['number' => $invoice->invoice_number, 'amount' => number_format($invoice->total_amount, 2), 'date' => $invoice->due_date->format('Y-m-d')]) }}

{{ __('emails.invoices.Outstanding balance', ['amount' => number_format($invoice->amount_due, 2)]) }}

<x-mail::button :url="route('invoices.show', $invoice->invoice_id)">
{{ __('emails.invoices.View Invoice') }}
</x-mail::button>

{{ __('emails.invoices.Please pay or disregard') }}

{{ __('emails.invoices.Thanks') }},<br>
{{ config('app.name') }}
</x-mail::message>
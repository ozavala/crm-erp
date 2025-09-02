<x-mail::message>
# {{ __('emails.quotations.Your Quotation is Ready') }}

{{ __('emails.quotations.Hello') }} {{ $quotation->customer?->name ?? __('emails.quotations.Valued Customer') }},

{{ __('emails.quotations.Thank you for your interest') }}

**{{ __('emails.quotations.Quotation ID') }}** {{ $quotation->id }}
**{{ __('emails.quotations.Total Amount') }}** ${{ number_format($quotation->total_amount, 2) }}
**{{ __('emails.quotations.Status') }}** {{ ucfirst($quotation->status) }}

<x-mail::button :url="route('quotations.show', $quotation)">
{{ __('emails.quotations.View Quotation') }}
</x-mail::button>

{{ __('emails.quotations.Questions contact us') }}

{{ __('emails.quotations.Thanks') }},<br>
{{ config('app.name') }}
</x-mail::message>

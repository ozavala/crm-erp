<x-mail::message>
# Your Quotation is Ready

Hello {{ $quotation->customer?->name ?? 'Valued Customer' }},

Thank you for your interest in our products. Please find your quotation details below:

**Quotation ID:** {{ $quotation->id }}
**Total Amount:** ${{ number_format($quotation->total_amount, 2) }}
**Status:** {{ ucfirst($quotation->status) }}

<x-mail::button :url="route('quotations.show', $quotation)">
View Quotation
</x-mail::button>

If you have any questions, please feel free to contact us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill {{ $bill->bill_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header-table { width: 100%; border: 0; margin-bottom: 30px; }
        .header-table td { border: 0; padding: 0; vertical-align: top; }
        .company-logo { max-height: 80px; margin-bottom: 10px; }
        .company-details p, .bill-details p { margin: 0; line-height: 1.4; }
        .supplier-details { margin-top: 30px; margin-bottom: 20px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .totals-table { float: right; width: 45%; margin-top: 20px; }
        .totals-table td, .totals-table th { padding: 8px; }
        .totals-table .total-label { font-weight: bold; text-align: right; }
        .totals-table .grand-total { font-size: 1.1em; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td class="company-details">
                    @if(isset($logoData))
                        <img src="{{ $logoData }}" alt="Company Logo" class="company-logo">
                    @endif
                    <h3>{{ config('settings.company_name', 'Your Company') }}</h3>
                    <p>
                        Legal ID: {{ config('settings.company_legal_id', 'N/A') }}<br>
                        {{ config('settings.company_address_line_1', '123 Street') }}<br>
                        {{ config('settings.company_address_line_2', 'City, State, Zip') }}<br>
                        Email: {{ config('settings.company_email', 'contact@yourcompany.com') }}<br>
                        Phone: {{ config('settings.company_phone', '123-456-7890') }}<br>
                        @if(config('settings.company_website'))
                            Web: {{ config('settings.company_website') }}<br>
                        @endif
                    </p>
                </td>
                <td class="bill-details" style="text-align: right;">
                    <h1>BILL</h1>
                    <p>
                        <strong>Bill #:</strong> {{ $bill->bill_number }}<br>
                        <strong>Date:</strong> {{ $bill->bill_date->format('Y-m-d') }}<br>
                        <strong>Due Date:</strong> {{ $bill->due_date->format('Y-m-d') }}<br>
                    </p>
                </td>
            </tr>
        </table>

        <div class="supplier-details">
            <h4>Supplier:</h4>
            <p>
                <strong>{{ $bill->supplier->name }}</strong><br>
                Legal ID: {{ $bill->supplier->legal_id ?? 'N/A' }}<br>
                @if($bill->supplier->addresses->first())
                    {{ $bill->supplier->addresses->first()->street_address_line_1 }}<br>
                    @if($bill->supplier->addresses->first()->street_address_line_2)
                        {{ $bill->supplier->addresses->first()->street_address_line_2 }}<br>
                    @endif
                    {{ $bill->supplier->addresses->first()->city }}, {{ $bill->supplier->addresses->first()->state_province }} {{ $bill->supplier->addresses->first()->postal_code }}
                @endif
            </p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->item_name }}</strong><br>
                            <small>{{ $item->item_description }}</small>
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">${{ number_format($item->item_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td class="total-label">Subtotal:</td>
                <td class="text-right">${{ number_format($bill->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">Tax:</td>
                <td class="text-right">${{ number_format($bill->tax_amount, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="total-label">Total:</td>
                <td class="text-right">${{ number_format($bill->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html> 
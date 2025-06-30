<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order {{ $purchaseOrder->purchase_order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        @page {
            margin: 20px 25px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 5px;
            vertical-align: top;
        }
        .address-table {
            margin-top: 20px;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .address-table td {
            padding: 10px;
            vertical-align: top;
        }
        .items-table {
            margin-top: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .totals-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .totals-table {
            width: 40%;
            float: right;
        }
        .totals-table td {
            padding: 5px 8px;
        }
        .totals-table .total-row td {
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .notes-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        .company-details {
            text-align: right;
        }
        .company-details h2 {
            margin-bottom: 0;
        }
        .company-details p {
            margin-top: 5px;
        }
        .vendor-details, .shipping-details {
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    @if(isset($logoData))
                        <img src="{{ $logoData }}" alt="Logo" style="max-height: 80px; margin-bottom: 15px;">
                    @endif
                    <p><strong>{{ config('settings.company_name', 'Your Company Name') }}</strong><br>
                    {{ config('settings.company_address_line_1', '123 Your Street') }}<br>
                    {{ config('settings.company_address_line_2', 'Your City, ST 12345') }}<br>
                    {{ config('settings.company_email', 'your.email@example.com') }}<br>
                    {{ config('settings.company_phone', '') }}</p>
                </td>
                <td class="company-details" style="width: 50%;">
                    <h1 style="font-size: 28px;">PURCHASE ORDER</h1>
                    <h2>PO #: {{ $purchaseOrder->purchase_order_number }}</h2>
                    <p><strong>Status:</strong> {{ $purchaseOrder->status }}</p>
                    <p><strong>Date:</strong> {{ $purchaseOrder->order_date->format('Y-m-d') }}<br>
                    <strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('Y-m-d') : 'N/A' }}</p>
                </td>
            </tr>
        </table>

        <table class="address-table">
            <tr>
                <td class="vendor-details">
                    <strong>Vendor:</strong><br>
                    @if($supplierAddress = $purchaseOrder->supplier->addresses->first())
                        <strong>{{ $purchaseOrder->supplier->name }}</strong><br>
                        {{ $supplierAddress->street_address_line_1 }}<br>
                        @if($supplierAddress->street_address_line_2){{ $supplierAddress->street_address_line_2 }}<br>@endif
                        {{ $supplierAddress->city }}, {{ $supplierAddress->state_province }} {{ $supplierAddress->postal_code }}
                    @else
                        {{ $purchaseOrder->supplier->name }}<br>
                        No address on file.
                    @endif
                </td>
                <td class="shipping-details">
                    <strong>Ship To:</strong><br>
                    @if($purchaseOrder->shippingAddress)
                        <strong>{{ $purchaseOrder->shippingAddress->address_name ?? 'Main Warehouse' }}</strong><br>
                        {{ $purchaseOrder->shippingAddress->street_address_line_1 }}<br>
                        @if($purchaseOrder->shippingAddress->street_address_line_2){{ $purchaseOrder->shippingAddress->street_address_line_2 }}<br>@endif
                        {{ $purchaseOrder->shippingAddress->city }}, {{ $purchaseOrder->shippingAddress->state_province }} {{ $purchaseOrder->shippingAddress->postal_code }}
                    @else
                        No shipping address specified.
                    @endif
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-end" style="width: 5%;">Qty</th>
                    <th class="text-end" style="width: 15%;">Unit Price</th>
                    <th class="text-end" style="width: 15%;">Landed Cost/Unit</th>
                    <th class="text-end" style="width: 15%;">Final Cost/Unit</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->item_name }}</strong><br>
                        <small>{{ $item->item_description }}</small>
                    </td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">${{ number_format($item->landed_cost_per_unit, 4) }}</td>
                    <td class="text-end">${{ number_format($item->unit_price + $item->landed_cost_per_unit, 4) }}</td>
                    <td class="text-end">${{ number_format($item->item_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-end">${{ number_format($purchaseOrder->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax ({{ number_format($purchaseOrder->tax_percentage, 2) }}%):</td>
                    <td class="text-end">${{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Shipping:</td>
                    <td class="text-end">${{ number_format($purchaseOrder->shipping_cost, 2) }}</td>
                </tr>
                @if($purchaseOrder->other_charges > 0)
                <tr>
                    <td>Other Charges:</td>
                    <td class="text-end">${{ number_format($purchaseOrder->other_charges, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td><strong>Total:</strong></td>
                    <td class="text-end">${{ number_format($purchaseOrder->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        @if($purchaseOrder->notes)
            <div class="notes-section">
                <strong>Notes:</strong>
                <p>{{ $purchaseOrder->notes }}</p>
            </div>
        @endif

        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>

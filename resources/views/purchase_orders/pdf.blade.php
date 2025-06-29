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
        .address-table td {
            padding: 10px;
            vertical-align: top;
            width: 50%;
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
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td>
                    <h1>PURCHASE ORDER</h1>
                    <p><strong>Your Company Name</strong><br>
                    123 Your Street<br>
                    Your City, ST 12345<br>
                    your.email@example.com</p>
                </td>
                <td class="text-end">
                    <h2>PO #: {{ $purchaseOrder->purchase_order_number }}</h2>
                    <p><strong>Date:</strong> {{ $purchaseOrder->order_date->format('Y-m-d') }}<br>
                    <strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('Y-m-d') : 'N/A' }}</p>
                </td>
            </tr>
        </table>

        <table class="address-table">
            <tr>
                <td>
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
                <td>
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
                    <th class="text-end">Qty</th>
                    <th class="text-end">Unit Price</th>
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
                <tr>
                    <td><strong>Total:</strong></td>
                    <td class="text-end"><strong>${{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
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


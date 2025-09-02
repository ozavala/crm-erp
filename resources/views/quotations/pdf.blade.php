<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cotización {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header-table { width: 100%; border: 0; margin-bottom: 30px; }
        .header-table td { border: 0; padding: 0; vertical-align: top; }
        .company-logo { max-height: 80px; margin-bottom: 10px; }
        .company-details p, .quotation-details p { margin: 0; line-height: 1.4; }
        .bill-to { margin-top: 30px; margin-bottom: 20px; }
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
                    <h3>{{ config('settings.company_name', 'Tu Empresa') }}</h3>
                    <p>
                        {{ config('settings.company_address_line_1', 'Calle 123') }}<br>
                        {{ config('settings.company_address_line_2', 'Ciudad, Estado, CP') }}<br>
                        Email: {{ config('settings.company_email', 'contacto@tuempresa.com') }}<br>
                        Tel: {{ config('settings.company_phone', '123-456-7890') }}<br>
                        @if(config('settings.company_website'))
                            Web: {{ config('settings.company_website') }}<br>
                        @endif
                    </p>
                </td>
                <td class="quotation-details" style="text-align: right;">
                    <h1>COTIZACIÓN</h1>
                    <p>
                        <strong>Cotización #:</strong> {{ $quotation->quotation_number }}<br>
                        <strong>Fecha:</strong> {{ $quotation->quotation_date ? $quotation->quotation_date->format('Y-m-d') : ($quotation->created_at ? $quotation->created_at->format('Y-m-d') : '') }}<br>
                        <strong>Válida hasta:</strong> {{ $quotation->due_date ? $quotation->due_date->format('Y-m-d') : '' }}<br>
                        <strong>Condiciones de Pago:</strong> {{ $quotation->payment_terms ?? config('settings.default_payment_terms', 'Contado') }}<br>
                        <strong>Días de Vencimiento:</strong> {{ $quotation->due_date && $quotation->quotation_date ? $quotation->quotation_date->diffInDays($quotation->due_date) : config('settings.default_due_days', 30) }}
                    </p>
                </td>
            </tr>
        </table>

        <div class="bill-to">
            <h4>Cliente:</h4>
            <p>
                <strong>{{ $quotation->opportunity->customer->full_name ?? '' }}</strong><br>
                {{ $quotation->opportunity->customer->company_name ? $quotation->opportunity->customer->company_name . '<br>' : '' }}
                @if($quotation->opportunity->customer && $quotation->opportunity->customer->primaryAddress)
                    {{ $quotation->opportunity->customer->primaryAddress->street_address_line_1 }}<br>
                    {{ $quotation->opportunity->customer->primaryAddress->street_address_line_2 ? $quotation->opportunity->customer->primaryAddress->street_address_line_2 . '<br>' : '' }}
                    {{ $quotation->opportunity->customer->primaryAddress->city }}, {{ $quotation->opportunity->customer->primaryAddress->state }} {{ $quotation->opportunity->customer->primaryAddress->postal_code }}
                @endif
            </p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Producto/Servicio</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
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
                <td class="text-right">${{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            @if($quotation->discount_amount > 0)
            <tr>
                <td class="total-label">Descuento:</td>
                <td class="text-right text-danger">-${{ number_format($quotation->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="total-label">Impuesto ({{ $quotation->tax_percentage }}%):</td>
                <td class="text-right">${{ number_format($quotation->tax_amount, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="total-label">Total:</td>
                <td class="text-right">${{ number_format($quotation->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html> 
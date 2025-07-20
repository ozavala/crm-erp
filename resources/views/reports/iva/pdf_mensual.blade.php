<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Mensual de IVA</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        h1, h2, h3 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        .resumen { margin-bottom: 20px; }
        .section-title { margin-top: 30px; margin-bottom: 10px; font-size: 16px; }
    </style>
</head>
<body>
    <h1>Monthly VAT Report</h1>
    <p><strong>Period:</strong> {{ $report['period']['start'] }} to {{ $report['period']['end'] }}</p>
    <div class="resumen">
        <table>
            <tr>
                <th>VAT Paid</th>
                <th>VAT Collected</th>
                <th>Net VAT</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>${{ number_format($report['tax_paid']['total'], 2) }}</td>
                <td>${{ number_format($report['tax_collected']['total'], 2) }}</td>
                <td>${{ number_format($report['net_tax']['amount'], 2) }}</td>
                <td>{{ $report['net_tax']['status'] == 'payable' ? 'Payable' : 'Receivable' }}</td>
            </tr>
        </table>
    </div>

    <h2 class="section-title">Breakdown VAT Paid</h2>
    <table>
        <thead>
            <tr>
                <th>Rate</th>
                <th>Amount</th>
                <th>Operations</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['tax_paid']['breakdown'] as $item)
            <tr>
                <td>{{ $item['tax_rate_name'] }} ({{ $item['tax_rate_percentage'] }}%)</td>
                <td>${{ number_format($item['total_amount'], 2) }}</td>
                <td>{{ $item['count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="section-title">Breakdown VAT Collected</h2>
    <table>
        <thead>
            <tr>
                <th>Rate</th>
                <th>Amount</th>
                <th>Operations</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['tax_collected']['breakdown'] as $item)
            <tr>
                <td>{{ $item['tax_rate_name'] }} ({{ $item['tax_rate_percentage'] }}%)</td>
                <td>${{ number_format($item['total_amount'], 2) }}</td>
                <td>{{ $item['count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size:11px; color:#888;">Generated on {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html> 
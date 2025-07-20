<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Anual de IVA</title>
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
    <h1>Annual VAT Report</h1>
    <p><strong>Year:</strong> {{ $year }}</p>
    <div class="resumen">
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>VAT Paid</th>
                    <th>VAT Collected</th>
                    <th>Net VAT</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary as $month => $report)
                <tr>
                    <td>{{ \Carbon\Carbon::create()->month($month+1)->format(__('iva_reports.months.' . strtolower(\Carbon\Carbon::create()->month($month+1)->format('F')))) }}</td>
                    <td>${{ number_format($report['tax_paid']['total'], 2) }}</td>
                    <td>${{ number_format($report['tax_collected']['total'], 2) }}</td>
                    <td>${{ number_format($report['net_tax']['amount'], 2) }}</td>
                    <td>{{ $report['net_tax']['status'] == 'payable' ? 'Payable' : 'Receivable' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p style="font-size:11px; color:#888;">Generated on {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html> 
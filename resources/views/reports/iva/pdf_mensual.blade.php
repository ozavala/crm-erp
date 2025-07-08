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
    <h1>{{ __('iva_reports.Monthly VAT Report') }}</h1>
    <p><strong>{{ __('iva_reports.Period') }}:</strong> {{ $report['period']['start'] }} {{ __('iva_reports.to') }} {{ $report['period']['end'] }}</p>
    <div class="resumen">
        <table>
            <tr>
                <th>{{ __('iva_reports.VAT Paid') }}</th>
                <th>{{ __('iva_reports.VAT Collected') }}</th>
                <th>{{ __('iva_reports.Net VAT') }}</th>
                <th>{{ __('iva_reports.Status') }}</th>
            </tr>
            <tr>
                <td>${{ number_format($report['tax_paid']['total'], 2) }}</td>
                <td>${{ number_format($report['tax_collected']['total'], 2) }}</td>
                <td>${{ number_format($report['net_tax']['amount'], 2) }}</td>
                <td>{{ $report['net_tax']['status'] == 'payable' ? __('iva_reports.Payable') : __('iva_reports.Receivable') }}</td>
            </tr>
        </table>
    </div>

    <h2 class="section-title">{{ __('iva_reports.Breakdown VAT Paid') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('iva_reports.Rate') }}</th>
                <th>{{ __('iva_reports.Amount') }}</th>
                <th>{{ __('iva_reports.Operations') }}</th>
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

    <h2 class="section-title">{{ __('iva_reports.Breakdown VAT Collected') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('iva_reports.Rate') }}</th>
                <th>{{ __('iva_reports.Amount') }}</th>
                <th>{{ __('iva_reports.Operations') }}</th>
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

    <p style="font-size:11px; color:#888;">{{ __('iva_reports.Generated on') }} {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html> 
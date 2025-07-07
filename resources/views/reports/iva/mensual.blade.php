@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reporte Mensual de IVA</h1>
    <form method="get" action="{{ route('iva.report.monthly') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="year" class="form-label">Año</label>
                <input type="number" name="year" id="year" class="form-control" value="{{ request('year', now()->year) }}" min="2000" max="2100">
            </div>
            <div class="col-auto">
                <label for="month" class="form-label">Mes</label>
                <input type="number" name="month" id="month" class="form-control" value="{{ request('month', now()->month) }}" min="1" max="12">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Ver reporte</button>
            </div>
        </div>
    </form>

    @isset($report)
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Resumen del período: {{ $report['period']['start'] }} a {{ $report['period']['end'] }}</h5>
            <ul>
                <li><strong>IVA Pagado:</strong> ${{ number_format($report['tax_paid']['total'], 2) }}</li>
                <li><strong>IVA Cobrado:</strong> ${{ number_format($report['tax_collected']['total'], 2) }}</li>
                <li><strong>IVA Neto:</strong> ${{ number_format($report['net_tax']['amount'], 2) }} ({{ $report['net_tax']['status'] == 'payable' ? 'A pagar' : 'A favor' }})</li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4>Desglose IVA Pagado</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tasa</th>
                        <th>Monto</th>
                        <th>Operaciones</th>
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
        </div>
        <div class="col-md-6">
            <h4>Desglose IVA Cobrado</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tasa</th>
                        <th>Monto</th>
                        <th>Operaciones</th>
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
        </div>
    </div>
    @endisset
</div>
@endsection 
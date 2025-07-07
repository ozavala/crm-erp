@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reporte Anual de IVA</h1>
    <form method="get" action="{{ route('iva.report.annual') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="year" class="form-label">Año</label>
                <input type="number" name="year" id="year" class="form-control" value="{{ request('year', now()->year) }}" min="2000" max="2100">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Ver reporte</button>
            </div>
        </div>
    </form>

    @isset($summary)
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Resumen anual: {{ request('year', now()->year) }}</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>IVA Pagado</th>
                        <th>IVA Cobrado</th>
                        <th>IVA Neto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $month => $report)
                    <tr>
                        <td>{{ \Carbon\Carbon::create()->month($month+1)->format('F') }}</td>
                        <td>${{ number_format($report['tax_paid']['total'], 2) }}</td>
                        <td>${{ number_format($report['tax_collected']['total'], 2) }}</td>
                        <td>${{ number_format($report['net_tax']['amount'], 2) }}</td>
                        <td>{{ $report['net_tax']['status'] == 'payable' ? 'A pagar' : 'A favor' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endisset
</div>
@endsection 
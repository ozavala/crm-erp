@extends('layouts.app')
@section('title', 'Reporte de Pérdidas y Ganancias')
@section('content')
<div class="container py-4">
    <h1 class="mb-4">Reporte de Pérdidas y Ganancias</h1>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <label for="from" class="form-label">Desde</label>
            <input type="date" id="from" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-auto">
            <label for="to" class="form-label">Hasta</label>
            <input type="date" id="to" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-auto align-self-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Concepto</th>
                        <th class="text-end">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Ingresos (Ventas)</strong></td>
                        <td class="text-end text-success fw-bold">${{ number_format($ingresos, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Costos (Inventario/Costos)</strong></td>
                        <td class="text-end text-danger">-${{ number_format($costos, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Utilidad Bruta</strong></td>
                        <td class="text-end fw-bold">${{ number_format($utilidadBruta, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Gastos</strong></td>
                        <td class="text-end text-danger">-${{ number_format($gastos, 2) }}</td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>Utilidad Neta</strong></td>
                        <td class="text-end fw-bold">${{ number_format($utilidadNeta, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
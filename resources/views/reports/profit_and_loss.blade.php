@extends('layouts.app')
@section('title', __('reports.Profit and Loss Report'))
@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ __('reports.Profit and Loss Report') }}</h1>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <label for="from" class="form-label">{{ __('reports.From') }}</label>
            <input type="date" id="from" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-auto">
            <label for="to" class="form-label">{{ __('reports.To') }}</label>
            <input type="date" id="to" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-auto align-self-end">
            <button type="submit" class="btn btn-primary">{{ __('reports.Filter') }}</button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('reports.Concept') }}</th>
                        <th class="text-end">{{ __('reports.Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>{{ __('reports.Income (Sales)') }}</strong></td>
                        <td class="text-end text-success fw-bold">${{ number_format($ingresos, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('reports.Costs (Inventory/Costs)') }}</strong></td>
                        <td class="text-end text-danger">-${{ number_format($costos, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('reports.Gross Profit') }}</strong></td>
                        <td class="text-end fw-bold">${{ number_format($utilidadBruta, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('reports.Expenses') }}</strong></td>
                        <td class="text-end text-danger">-${{ number_format($gastos, 2) }}</td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>{{ __('reports.Net Profit') }}</strong></td>
                        <td class="text-end fw-bold">${{ number_format($utilidadNeta, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
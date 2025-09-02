@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Monthly VAT Report</h1>
    <form method="get" action="{{ route('iva.report.monthly') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="year" class="form-label">Year</label>
                <input type="number" name="year" id="year" class="form-control" value="{{ request('year', now()->year) }}" min="2000" max="2100">
            </div>
            <div class="col-auto">
                <label for="month" class="form-label">Month</label>
                <input type="number" name="month" id="month" class="form-control" value="{{ request('month', now()->month) }}" min="1" max="12">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">View Report</button>
            </div>
        </div>
    </form>

    @isset($report)
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Period Summary: {{ $report['period']['start'] }} to {{ $report['period']['end'] }}</h5>
            <div class="d-flex justify-content-between align-items-start">
                <ul>
                    <li><strong>VAT Paid:</strong> ${{ number_format($report['tax_paid']['total'], 2) }}</li>
                    <li><strong>VAT Collected:</strong> ${{ number_format($report['tax_collected']['total'], 2) }}</li>
                    <li><strong>Net VAT:</strong> ${{ number_format($report['net_tax']['amount'], 2) }} ({{ $report['net_tax']['status'] == 'payable' ? 'Payable' : 'Receivable' }})</li>
                </ul>
                <div>
                    <a href="{{ route('iva.report.monthly.excel') }}?year={{ request('year', now()->year) }}&month={{ request('month', now()->month) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4>Breakdown VAT Paid</h4>
            <table class="table table-bordered">
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
                        <td>Taxable Entry For Company 1</td>
                        <td>{{ $item['tax_rate_name'] }} ({{ $item['tax_rate_percentage'] }}%)</td>
                        <td>${{ number_format($item['total_amount'], 2) }}</td>
                        <td>{{ $item['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h4>Breakdown VAT Collected</h4>
            <table class="table table-bordered">
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
        </div>
    </div>
    @endisset
</div>
@endsection 
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Annual VAT Report</h1>
    <form method="get" action="{{ route('iva.report.annual') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="year" class="form-label">Year</label>
                <input type="number" name="year" id="year" class="form-control" value="{{ request('year', now()->year) }}" min="2000" max="2100">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">View Report</button>
            </div>
        </div>
    </form>

    @isset($summary)
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="card-title mb-0">Annual Summary: {{ request('year', now()->year) }}</h5>
                <div>
                    <a href="{{ route('iva.report.annual.excel') }}?year={{ request('year', now()->year) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                </div>
            </div>
            <table class="table table-bordered">
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
    </div>
    @endisset
</div>
@endsection 
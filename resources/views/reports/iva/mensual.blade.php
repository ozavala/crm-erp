@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('iva_reports.Monthly VAT Report') }}</h1>
    <form method="get" action="{{ route('iva.report.monthly') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="year" class="form-label">{{ __('iva_reports.Year') }}</label>
                <input type="number" name="year" id="year" class="form-control" value="{{ request('year', now()->year) }}" min="2000" max="2100">
            </div>
            <div class="col-auto">
                <label for="month" class="form-label">{{ __('iva_reports.Month') }}</label>
                <input type="number" name="month" id="month" class="form-control" value="{{ request('month', now()->month) }}" min="1" max="12">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ __('iva_reports.View Report') }}</button>
            </div>
        </div>
    </form>

    @isset($report)
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('iva_reports.Period Summary') }}: {{ $report['period']['start'] }} {{ __('iva_reports.to') }} {{ $report['period']['end'] }}</h5>
            <ul>
                <li><strong>{{ __('iva_reports.VAT Paid') }}:</strong> ${{ number_format($report['tax_paid']['total'], 2) }}</li>
                <li><strong>{{ __('iva_reports.VAT Collected') }}:</strong> ${{ number_format($report['tax_collected']['total'], 2) }}</li>
                <li><strong>{{ __('iva_reports.Net VAT') }}:</strong> ${{ number_format($report['net_tax']['amount'], 2) }} ({{ $report['net_tax']['status'] == 'payable' ? __('iva_reports.Payable') : __('iva_reports.Receivable') }})</li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4>{{ __('iva_reports.Breakdown VAT Paid') }}</h4>
            <table class="table table-bordered">
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
        </div>
        <div class="col-md-6">
            <h4>{{ __('iva_reports.Breakdown VAT Collected') }}</h4>
            <table class="table table-bordered">
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
        </div>
    </div>
    @endisset
</div>
@endsection 
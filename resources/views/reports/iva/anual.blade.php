@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('iva_reports.Annual VAT Report') }}</h1>
    <form method="get" action="{{ route('iva.report.annual') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="year" class="form-label">{{ __('iva_reports.Year') }}</label>
                <input type="number" name="year" id="year" class="form-control" value="{{ request('year', now()->year) }}" min="2000" max="2100">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ __('iva_reports.View Report') }}</button>
            </div>
        </div>
    </form>

    @isset($summary)
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('iva_reports.Annual Summary') }}: {{ request('year', now()->year) }}</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('iva_reports.Month') }}</th>
                        <th>{{ __('iva_reports.VAT Paid') }}</th>
                        <th>{{ __('iva_reports.VAT Collected') }}</th>
                        <th>{{ __('iva_reports.Net VAT') }}</th>
                        <th>{{ __('iva_reports.Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $month => $report)
                    <tr>
                        <td>{{ \Carbon\Carbon::create()->month($month+1)->format(__('iva_reports.months.' . strtolower(\Carbon\Carbon::create()->month($month+1)->format('F')))) }}</td>
                        <td>${{ number_format($report['tax_paid']['total'], 2) }}</td>
                        <td>${{ number_format($report['tax_collected']['total'], 2) }}</td>
                        <td>${{ number_format($report['net_tax']['amount'], 2) }}</td>
                        <td>{{ $report['net_tax']['status'] == 'payable' ? __('iva_reports.Payable') : __('iva_reports.Receivable') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endisset
</div>
@endsection 
@extends('layouts.app')

@section('title', 'Tax Balance Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Tax Balance Report</h3>
                    <div>
                        <a href="{{ route('reports.tax-balance.pdf') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('reports.tax-balance.excel') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                           class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros de fecha -->
                    <form method="GET" action="{{ route('reports.tax-balance') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                       value="{{ $startDate }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                       value="{{ $endDate }}" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Generate Report
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(isset($report))
                    <!-- Resumen del período -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Report Period</h6>
                                <p class="mb-0">{{ $report['period']['start_formatted'] }} to {{ $report['period']['end_formatted'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Balance consolidado -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Tax Collected (Sales)</h5>
                                    <h3 class="card-text">${{ number_format($report['summary']['total_tax_collected'], 2) }}</h3>
                                    <small>{{ $report['summary']['total_invoices'] }} invoices</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Tax Paid (Purchases)</h5>
                                    <h3 class="card-text">${{ number_format($report['summary']['total_tax_paid'], 2) }}</h3>
                                    <small>{{ $report['summary']['total_bills'] }} bills</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $report['summary']['balance_status'] === 'payable' ? 'bg-danger' : 'bg-info' }} text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Net Tax Balance</h5>
                                    <h3 class="card-text">${{ number_format($report['summary']['net_tax_balance'], 2) }}</h3>
                                    <small>{{ ucfirst($report['summary']['balance_status']) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle por tasa de impuesto -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Sales Tax by Rate</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tax Rate</th>
                                                    <th>Invoices</th>
                                                    <th>Taxable Amount</th>
                                                    <th>Tax Collected</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($report['sales_tax_by_rate'] as $taxRate)
                                                <tr>
                                                    <td>{{ $taxRate->tax_rate_name }} ({{ $taxRate->tax_rate_percentage }}%)</td>
                                                    <td>{{ $taxRate->invoice_count }}</td>
                                                    <td>${{ number_format($taxRate->total_taxable_amount, 2) }}</td>
                                                    <td>${{ number_format($taxRate->total_tax_collected, 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No sales tax data found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Purchase Tax by Rate</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tax Rate</th>
                                                    <th>Bills</th>
                                                    <th>Taxable Amount</th>
                                                    <th>Tax Paid</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($report['purchase_tax_by_rate'] as $taxRate)
                                                <tr>
                                                    <td>{{ $taxRate->tax_rate_name }} ({{ $taxRate->tax_rate_percentage }}%)</td>
                                                    <td>{{ $taxRate->bill_count }}</td>
                                                    <td>${{ number_format($taxRate->total_taxable_amount, 2) }}</td>
                                                    <td>${{ number_format($taxRate->total_tax_paid, 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No purchase tax data found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top clientes y proveedores -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Top 10 Customers by Tax Paid</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>Invoices</th>
                                                    <th>Tax Collected</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($report['top_customers_by_tax'] as $customer)
                                                <tr>
                                                    <td>
                                                        {{ $customer->customer_name ?: $customer->first_name . ' ' . $customer->last_name }}
                                                    </td>
                                                    <td>{{ $customer->invoice_count }}</td>
                                                    <td>${{ number_format($customer->total_tax_collected, 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">No customer data found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Top 10 Suppliers by Tax Paid</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Supplier</th>
                                                    <th>Bills</th>
                                                    <th>Tax Paid</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($report['top_suppliers_by_tax'] as $supplier)
                                                <tr>
                                                    <td>
                                                        {{ $supplier->supplier_name }}
                                                        @if($supplier->contact_person)
                                                            <br><small class="text-muted">{{ $supplier->contact_person }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $supplier->bill_count }}</td>
                                                    <td>${{ number_format($supplier->total_tax_paid, 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">No supplier data found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
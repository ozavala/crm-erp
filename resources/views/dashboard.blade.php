@extends('layouts.app')
@section('title', __('dashboard.Dashboard'))
@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ __('dashboard.Dashboard') }}</h1>
    <div class="row mb-4 g-3">
        <!-- KPI Cards -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-primary p-3"><i class="bi bi-person-lines-fill fs-4"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">{{ __('dashboard.Total Leads') }}</div>
                        <div class="h4 mb-0">{{ $totalLeads }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-success p-3"><i class="bi bi-person-check-fill fs-4"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">{{ __('dashboard.Active Leads') }}</div>
                        <div class="h4 mb-0">{{ $activeLeads }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-info p-3"><i class="bi bi-person-plus-fill fs-4"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">{{ __('dashboard.New Leads') }}</div>
                        <div class="h4 mb-0">{{ $leadStatusCounts['New'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-warning p-3"><i class="bi bi-person-badge-fill fs-4"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">{{ __('dashboard.Qualified Leads') }}</div>
                        <div class="h4 mb-0">{{ $leadStatusCounts['Qualified'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <!-- Ventas del mes -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-success p-3"><i class="bi bi-currency-dollar fs-4"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">{{ __('dashboard.Sales This Month') }}</div>
                        <div class="h4 mb-0">${{ number_format($salesThisMonth ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Facturas pendientes -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-danger p-3"><i class="bi bi-receipt fs-4"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">{{ __('dashboard.Pending Invoices') }}</div>
                        <div class="h4 mb-0">{{ $pendingInvoices ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cotizaciones abiertas -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <span class="badge bg-info p-3"><i class="bi bi-file-earmark-text fs-4"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">{{ __('dashboard.Open Quotations') }}</div>
                        <div class="h4 mb-0">{{ $openQuotations ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Acceso rápido -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div class="mb-2"><i class="bi bi-lightning-charge fs-2 text-warning"></i></div>
                    <a href="{{ route('invoices.create') }}" class="btn btn-outline-primary btn-sm mb-1 w-100">+ {{ __('dashboard.New Invoice') }}</a>
                    <a href="{{ route('quotations.create') }}" class="btn btn-outline-info btn-sm mb-1 w-100">+ {{ __('dashboard.New Quotation') }}</a>
                    <a href="{{ route('customers.create') }}" class="btn btn-outline-success btn-sm w-100">+ {{ __('dashboard.New Customer') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Espacio para gráfico (puedes integrar Chart.js o similar) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">{{ __('dashboard.Sales by Month') }}</h5>
                    <canvas id="salesChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($salesMonths ?? []) !!},
            datasets: [{
                label: '{{ __('dashboard.Sales') }}',
                data: {!! json_encode($salesByMonth ?? []) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection

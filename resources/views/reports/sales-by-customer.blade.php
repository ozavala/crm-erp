@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Sales by Customer Report</h1>

        <form method="GET" action="{{ route('reports.sales-by-customer') }}">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="period">Select Period:</label>
                    <select name="period" id="period" class="form-control">
                        <option value="last_7_days" {{ request('period') == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="last_30_days" {{ request('period') == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="last_90_days" {{ request('period') == 'last_90_days' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <div class="card mb-4">
            <div class="card-header">Sales by Customer Chart</div>
            <div class="card-body">
                <canvas id="salesByCustomerChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Sales by Customer Data</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customerSalesData as $data)
                            <tr>
                                <td>{{ $data->customer_name }}</td>
                                <td>{{ $data->total_sales }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const customerSalesData = @json($customerSalesData);

        const chartData = {
            labels: customerSalesData.map(data => data.customer_name),
            datasets: [{
                label: 'Total Sales',
                data: customerSalesData.map(data => data.total_sales),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar',
            data: chartData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        new Chart(
            document.getElementById('salesByCustomerChart'),
            config
        );
    </script>
@endpush

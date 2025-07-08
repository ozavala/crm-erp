@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('reports.Sales Report') }}</h1>

        <form method="GET" action="{{ route('reports.sales') }}">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="period">{{ __('reports.Select Period:') }}</label>
                    <select name="period" id="period" class="form-control">
                        <option value="last_7_days" {{ request('period') == 'last_7_days' ? 'selected' : '' }}>{{ __('reports.Last 7 Days') }}</option>
                        <option value="last_30_days" {{ request('period') == 'last_30_days' ? 'selected' : '' }}>{{ __('reports.Last 30 Days') }}</option>
                        <option value="last_90_days" {{ request('period') == 'last_90_days' ? 'selected' : '' }}>{{ __('reports.Last 90 Days') }}</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>{{ __('reports.Custom Range') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date">{{ __('reports.Start Date:') }}</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date">{{ __('reports.End Date:') }}</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">{{ __('reports.Filter') }}</button>
                </div>
            </div>
        </form>

        <div class="card mb-4">
            <div class="card-header">{{ __('reports.Sales Chart') }}</div>
            <div class="card-body">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">{{ __('reports.Sales Data') }}</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('reports.Date') }}</th>
                            <th>{{ __('reports.Total Sales') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salesData as $data)
                            <tr>
                                <td>{{ $data->date }}</td>
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
        const salesData = @json($salesData);

        const chartData = {
            labels: salesData.map(data => data.date),
            datasets: [{
                label: '{{ __('reports.Total Sales') }}',
                data: salesData.map(data => data.total_sales),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        const config = {
            type: 'line',
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
            document.getElementById('salesChart'),
            config
        );
    </script>
@endpush


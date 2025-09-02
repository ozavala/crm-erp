@extends('layouts.app')
@section('title', 'Profit and Loss Report')
@section('content')
<div class="container py-4">
    <h1 class="mb-4">Profit and Loss Report</h1>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <label for="from" class="form-label">From</label>
            <input type="date" id="from" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-auto">
            <label for="to" class="form-label">To</label>
            <input type="date" id="to" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-auto align-self-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
    <div class="card mb-4">
        <div class="card-body">
            <h5>Income (Sales) Detail</h5>
            <table class="table table-sm table-bordered mb-2">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ingresosDetalle as $line)
                        @php $account = \App\Models\Account::where('code', $line->account_code)->first(); @endphp
                        <tr>
                            <td>{{ $account ? $account->code : $line->account_code }}</td>
                            <td>{{ $account ? $account->description : '' }}</td>
                            <td class="text-end">${{ number_format($line->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <h5>Costs Detail</h5>
            <table class="table table-sm table-bordered mb-2">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($costosDetalle as $line)
                        @php $account = \App\Models\Account::where('code', $line->account_code)->first(); @endphp
                        <tr>
                            <td>{{ $account ? $account->code : $line->account_code }}</td>
                            <td>{{ $account ? $account->description : '' }}</td>
                            <td class="text-end">${{ number_format($line->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <h5>Expenses Detail</h5>
            <table class="table table-sm table-bordered mb-2">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gastosDetalle as $line)
                        @php $account = \App\Models\Account::where('code', $line->account_code)->first(); @endphp
                        <tr>
                            <td>{{ $account ? $account->code : $line->account_code }}</td>
                            <td>{{ $account ? $account->description : '' }}</td>
                            <td class="text-end">${{ number_format($line->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Concept</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Income (Sales)</strong></td>
                        <td class="text-end text-success fw-bold">${{ number_format($ingresos, 2, '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Costs (Inventory/Costs)</strong></td>
                        <td class="text-end text-danger">-${{ number_format($costos, 2, '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Gross Profit</strong></td>
                        <td class="text-end fw-bold">${{ number_format($utilidadBruta, 2, '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Expenses</strong></td>
                        <td class="text-end text-danger">-${{ number_format($gastos, 2, '.') }}</td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>Net Profit</strong></td>
                        <td class="text-end fw-bold">${{ number_format($utilidadNeta, 2, '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
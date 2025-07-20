<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('partials.Payments') }}</h5>
    </div>
    <div class="card-body">
        @if($model->payments->isNotEmpty())
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('partials.Date') }}</th>
                        <th class="text-end">{{ __('partials.Amount') }}</th>
                        <th>{{ __('partials.Method') }}</th>
                        <th>{{ __('partials.Reference') }}</th>
                        <th>{{ __('partials.Logged By') }}</th>
                        <th>{{ __('partials.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($model->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                            <td>{{ $payment->createdBy->full_name ?? 'N/A' }}</td>
                            <td>
                                <form action="{{ route('payments.destroy', $payment->payment_id) }}" method="POST" onsubmit="return confirm('{{ __('partials.Are you sure you want to delete this payment?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">{{ __('partials.Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
        @else
            <p>{{ __('partials.No payments have been recorded for this', ['model' => class_basename($model)]) }}</p>
        @endif

        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <th class="text-end">{{ __('partials.Total Amount:') }}</th>
                            <td class="text-end">${{ number_format($model->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="text-end">{{ __('partials.Amount Paid:') }}</th>
                            <td class="text-end">${{ number_format($model->amount_paid, 2) }}</td>
                        </tr>
                        <tr class="fw-bold">
                            <th class="text-end">{{ __('partials.Amount Due:') }}</th>
                            <td class="text-end">${{ number_format($model->amount_due, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($model->amount_due > 0)
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('partials.Record a New Payment') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="payable_id" value="{{ $model->getKey() }}">
            <input type="hidden" name="payable_type" value="{{ get_class($model) }}">

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="payment_date" class="form-label">{{ __('partials.Payment Date') }} <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                    @error('payment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="amount" class="form-label">{{ __('partials.Amount') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', number_format($model->amount_due, 2, '.', '')) }}" required max="{{ number_format($model->amount_due, 2, '.', '') }}">
                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="payment_method" class="form-label">{{ __('partials.Payment Method') }}</label>
                    <select name="payment_method" id="payment_method" class="form-select">
                        <option value="Bank Transfer">{{ __('partials.Bank Transfer') }}</option>
                        <option value="Credit Card">{{ __('partials.Credit Card') }}</option>
                        <option value="Cash">{{ __('partials.Cash') }}</option>
                        <option value="Check">{{ __('partials.Check') }}</option>
                        <option value="Other">{{ __('partials.Other') }}</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="reference_number" class="form-label">{{ __('partials.Reference #') }}</label>
                    <input type="text" class="form-control" id="reference_number" name="reference_number" value="{{ old('reference_number') }}">
                </div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">{{ __('partials.Notes') }}</label>
                <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">{{ __('partials.Record Payment') }}</button>
        </form>
    </div>
</div>
@endif
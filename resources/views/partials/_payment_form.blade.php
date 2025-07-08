<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('payments.Add Payment') }}</h5>
    </div>
    <div class="card-body">
        @if(isset($payable) && $payable->amount_due > 0)
            <form action="{{ $form_url }}" method="POST">
                @csrf
                <input type="hidden" name="payable_id" value="{{ $payable->getKey() }}">
                <input type="hidden" name="payable_type" value="{{ get_class($payable) }}">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="payment_date" class="form-label">{{ __('payments.Payment Date') }}</label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="amount" class="form-label">{{ __('payments.Amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', number_format($payable->amount_due, 2, '.', '')) }}" required step="0.01" min="0.01" max="{{ $payable->amount_due }}">
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ __('payments.Invalid amount') }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="payment_method" class="form-label">{{ __('payments.Payment Method') }}</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="Bank Transfer">{{ __('payments.Bank Transfer') }}</option>
                            <option value="Cash">{{ __('payments.Cash') }}</option>
                            <option value="Credit Card">{{ __('payments.Credit Card') }}</option>
                            <option value="Debit Card">{{ __('payments.Debit Card') }}</option>
                            <option value="Cheque">{{ __('payments.Cheque') }}</option>
                            <option value="Other">{{ __('payments.Other') }}</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">{{ __('partials.Notes (Reference, etc.)') }}</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('payments.Save Payment') }}</button>
                <a href="{{ $cancelUrl }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        @else
            <div class="alert alert-success mb-0" role="alert">
                {{ __('partials.This document is fully paid.') }}
            </div>
        @endif
    </div>
</div>

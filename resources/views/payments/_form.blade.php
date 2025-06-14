@csrf
<input type="hidden" name="payable_id" value="{{ $payable->invoice_id ?? $payable->purchase_order_id }}">
<input type="hidden" name="payable_type" value="{{ get_class($payable) }}">

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
        @error('payment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" required>
        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="payment_method" class="form-label">Payment Method</label>
        <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
            <option value="">Select Method</option>
            <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
            <option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
            <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="Check" {{ old('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
            <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="reference_number" class="form-label">Reference Number</label>
        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" value="{{ old('reference_number') }}">
        @error('reference_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<button type="submit" class="btn btn-primary">Record Payment</button>
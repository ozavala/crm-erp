@csrf
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="name" class="form-label">Opportunity Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $opportunity->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $opportunity->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="lead_id" class="form-label">Related Lead (Optional)</label>
        <select class="form-select @error('lead_id') is-invalid @enderror" id="lead_id" name="lead_id">
            <option value="">None</option>
            @foreach($leads as $lead)
                <option value="{{ $lead->lead_id }}" {{ (old('lead_id', $opportunity->lead_id ?? $selectedLeadId ?? '') == $lead->lead_id) ? 'selected' : '' }}>
                    {{ $lead->title }} ({{ $lead->contact_name ?? 'N/A' }})
                </option>
            @endforeach
        </select>
        @error('lead_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="customer_id" class="form-label">Related Customer (Optional)</label>
        <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
            <option value="">None</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->customer_id }}" {{ (old('customer_id', $opportunity->customer_id ?? $selectedCustomerId ?? '') == $customer->customer_id) ? 'selected' : '' }}>
                    {{ $customer->full_name }} ({{ $customer->company_name ?? 'N/A' }})
                </option>
            @endforeach
        </select>
        @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="stage" class="form-label">Stage <span class="text-danger">*</span></label>
        <select class="form-select @error('stage') is-invalid @enderror" id="stage" name="stage" required>
            @foreach($stages as $key => $value)
                <option value="{{ $key }}" {{ old('stage', $opportunity->stage ?? 'Qualification') == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        @error('stage') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="amount" class="form-label">Amount ($)</label>
        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', isset($opportunity->amount) ? number_format($opportunity->amount, 2, '.', '') : '') }}">
        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="probability" class="form-label">Probability (%)</label>
        <input type="number" class="form-control @error('probability') is-invalid @enderror" id="probability" name="probability" value="{{ old('probability', $opportunity->probability ?? '') }}" min="0" max="100">
        @error('probability') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="expected_close_date" class="form-label">Expected Close Date</label>
        <input type="date" class="form-control @error('expected_close_date') is-invalid @enderror" id="expected_close_date" name="expected_close_date" value="{{ old('expected_close_date', $opportunity->expected_close_date ? $opportunity->expected_close_date->format('Y-m-d') : '') }}">
        @error('expected_close_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="assigned_to_user_id" class="form-label">Assigned To</label>
        <select class="form-select @error('assigned_to_user_id') is-invalid @enderror" id="assigned_to_user_id" name="assigned_to_user_id">
            <option value="">None</option>
            @foreach($crmUsers as $user)
                <option value="{{ $user->user_id }}" {{ (old('assigned_to_user_id', $opportunity->assigned_to_user_id ?? Auth::id()) == $user->user_id) ? 'selected' : '' }}>
                    {{ $user->full_name }}
                </option>
            @endforeach
        </select>
        @error('assigned_to_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">{{ isset($opportunity->opportunity_id) ? 'Update Opportunity' : 'Create Opportunity' }}</button>
    <a href="{{ route('opportunities.index') }}" class="btn btn-secondary">Cancel</a>
</div>

@push('scripts')
<script>
    // Add any specific JS for opportunity form if needed, e.g., linking lead selection to customer
</script>
@endpush
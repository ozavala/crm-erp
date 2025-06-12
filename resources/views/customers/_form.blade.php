@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name ?? '') }}" required>
        @error('first_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name ?? '') }}" required>
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone_number" class="form-label">Phone Number</label>
        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $customer->phone_number ?? '') }}">
        @error('phone_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="company_name" class="form-label">Company Name</label>
        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name ?? '') }}">
        @error('company_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
            <option value="">Select Status</option>
            @foreach($statuses as $key => $value)
                <option value="{{ $key }}" {{ old('status', $customer->status ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<hr>
<h5>Primary Address</h5>
{{-- We'll manage one address block for now, indexed at 0 --}}

@php
    // Attempt to get address data from old input first
    $addressDataFromOld = old('addresses.0');
    
    // Determine the base address object
    if (is_array($addressDataFromOld) && count($addressDataFromOld) > 0) {
        // If old input for address is an array (from a failed submission), hydrate a new Address model instance.
        $address = new \App\Models\Address($addressDataFromOld);
        // Manually set is_primary from the array, as it might not be in $fillable for mass assignment or checkbox was unchecked.
        $address->is_primary = !empty($addressDataFromOld['is_primary']);
        if (!empty($addressDataFromOld['address_id'])) {
            $address->address_id = $addressDataFromOld['address_id'];
            $address->exists = true; // Indicate it's an existing address being edited
        }
    } elseif ($customer->addresses->isNotEmpty()) {
        // If no old input, but customer has existing addresses, use the first one.
        $address = $customer->addresses->first();
    } else {
        // No old input and no existing addresses for the customer.
        $address = new \App\Models\Address();
        if (!$customer->exists && empty(old())) { // Only for a truly new customer (create form)
            $address->is_primary = true; // Default to primary for the very first address of a new customer
        }
    }
@endphp
<input type="hidden" name="addresses[0][address_id]" value="{{ $address->address_id ?? '' }}">

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="address_type_0" class="form-label">Address Type</label>
        <input type="text" class="form-control @error('addresses.0.address_type') is-invalid @enderror" id="address_type_0" name="addresses[0][address_type]" value="{{ $address->address_type ?? 'Primary' }}">
        @error('addresses.0.address_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-8 mb-3">
        <label for="street_address_line_1_0" class="form-label">Street Address Line 1</label>
        <input type="text" class="form-control @error('addresses.0.street_address_line_1') is-invalid @enderror" id="street_address_line_1_0" name="addresses[0][street_address_line_1]" value="{{ $address->street_address_line_1 ?? '' }}">
        @error('addresses.0.street_address_line_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="street_address_line_2_0" class="form-label">Street Address Line 2</label>
    <input type="text" class="form-control @error('addresses.0.street_address_line_2') is-invalid @enderror" id="street_address_line_2_0" name="addresses[0][street_address_line_2]" value="{{ $address->street_address_line_2 ?? '' }}">
    @error('addresses.0.street_address_line_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="city_0" class="form-label">City</label>
        <input type="text" class="form-control @error('addresses.0.city') is-invalid @enderror" id="city_0" name="addresses[0][city]" value="{{ $address->city ?? '' }}">
        @error('addresses.0.city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="state_province_0" class="form-label">State/Province</label>
        <input type="text" class="form-control @error('addresses.0.state_province') is-invalid @enderror" id="state_province_0" name="addresses[0][state_province]" value="{{ $address->state_province ?? '' }}">
        @error('addresses.0.state_province') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="postal_code_0" class="form-label">Postal Code</label>
        <input type="text" class="form-control @error('addresses.0.postal_code') is-invalid @enderror" id="postal_code_0" name="addresses[0][postal_code]" value="{{ $address->postal_code ?? '' }}">
        @error('addresses.0.postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row align-items-center">
    <div class="col-md-4 mb-3">
        <label for="country_code_0" class="form-label">Country Code (2 Letter)</label>
        <input type="text" class="form-control @error('addresses.0.country_code') is-invalid @enderror" id="country_code_0" name="addresses[0][country_code]" value="{{ $address->country_code ?? 'US' }}" maxlength="2">
        @error('addresses.0.country_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3 mt-3 form-check">
        <input type="hidden" name="addresses[0][is_primary]" value="0"> {{-- Default to false if checkbox not checked --}}
        <input type="checkbox" class="form-check-input @error('addresses.0.is_primary') is-invalid @enderror" id="is_primary_0" name="addresses[0][is_primary]" value="1" {{ $address->is_primary ? 'checked' : '' }}>
        <label class="form-check-label" for="is_primary_0">Set as Primary Address</label>
        @error('addresses.0.is_primary') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
<hr>

 <div class="mb-3">
     <label for="notes" class="form-label">Notes</label>
     <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $customer->notes ?? '') }}</textarea>
     @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<button type="submit" class="btn btn-primary">{{ isset($customer->customer_id) ? 'Update Customer' : 'Create Customer' }}</button>
<a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
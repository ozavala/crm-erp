<div class="row">
    <div class="col-md-12 mb-3">
        <label class="form-label">Customer Type</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="type" id="typePerson" value="Person" {{ old('type', $customer->type ?? 'Person') == 'Person' ? 'checked' : '' }}>
                <label class="form-check-label" for="typePerson">Person</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="type" id="typeCompany" value="Company" {{ old('type', $customer->type ?? '') == 'Company' ? 'checked' : '' }}>
                <label class="form-check-label" for="typeCompany">Company</label>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Person Fields -->
    <div class="col-md-6 person-fields">
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name ?? '') }}">
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6 person-fields">
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name ?? '') }}">
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <!-- Company Fields -->
    <div class="col-md-12 company-fields" style="display: none;">
        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name ?? '') }}">
            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="legal_id" class="form-label">Legal ID / Tax ID</label>
            <input type="text" class="form-control @error('legal_id') is-invalid @enderror" id="legal_id" name="legal_id" value="{{ old('legal_id', $customer->legal_id ?? '') }}" required>
            @error('legal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email ?? '') }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $customer->phone_number ?? '') }}">
            @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                @foreach($statuses as $key => $value)
                    <option value="{{ $key }}" {{ old('status', $customer->status ?? 'Active') == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<hr class="my-4">

<h4>Primary Address</h4>

@php
    // For both create and edit, we can work with an address object.
    // On create, it's a new empty object. On edit, it's the first associated address or a new one.
    $address = $customer->addresses->first() ?? new \App\Models\Address();
@endphp

{{-- Hidden field for address ID for updates --}}
<input type="hidden" name="addresses[0][address_id]" value="{{ $address->address_id }}">

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="address_type" class="form-label">Address Type</label>
        <input type="text" class="form-control @error('addresses.0.address_type') is-invalid @enderror" id="address_type" name="addresses[0][address_type]" value="{{ old('addresses.0.address_type', $address->address_type) }}" placeholder="e.g., Billing, Shipping">
        @error('addresses.0.address_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="street_address_line_1" class="form-label">Street Address</label>
        <input type="text" class="form-control @error('addresses.0.street_address_line_1') is-invalid @enderror" id="street_address_line_1" name="addresses[0][street_address_line_1]" value="{{ old('addresses.0.street_address_line_1', $address->street_address_line_1) }}">
        @error('addresses.0.street_address_line_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="city" class="form-label">City</label>
        <input type="text" class="form-control @error('addresses.0.city') is-invalid @enderror" id="city" name="addresses[0][city]" value="{{ old('addresses.0.city', $address->city) }}">
        @error('addresses.0.city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="state_province" class="form-label">State / Province</label>
        <input type="text" class="form-control @error('addresses.0.state_province') is-invalid @enderror" id="state_province" name="addresses[0][state_province]" value="{{ old('addresses.0.state_province', $address->state_province) }}">
        @error('addresses.0.state_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="postal_code" class="form-label">Postal Code</label>
        <input type="text" class="form-control @error('addresses.0.postal_code') is-invalid @enderror" id="postal_code" name="addresses[0][postal_code]" value="{{ old('addresses.0.postal_code', $address->postal_code) }}">
        @error('addresses.0.postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="country_code" class="form-label">Country Code</label>
        <input type="text" class="form-control @error('addresses.0.country_code') is-invalid @enderror" id="country_code" name="addresses[0][country_code]" value="{{ old('addresses.0.country_code', $address->country_code) }}" placeholder="e.g., US, CA">
        @error('addresses.0.country_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <div class="form-check">
        <input type="hidden" name="addresses[0][is_primary]" value="0">
        <input class="form-check-input" type="checkbox" name="addresses[0][is_primary]" id="is_primary" value="1" {{ old('addresses.0.is_primary', $address->is_primary) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_primary">Set as Primary Address</label>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const personFields = document.querySelectorAll('.person-fields');
    const companyFields = document.querySelectorAll('.company-fields');

    function toggleFields() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;
        if (selectedType === 'Person') {
            personFields.forEach(el => el.style.display = 'block');
            companyFields.forEach(el => el.style.display = 'none');
        } else {
            personFields.forEach(el => el.style.display = 'none');
            companyFields.forEach(el => el.style.display = 'block');
        }
    }

    typeRadios.forEach(radio => radio.addEventListener('change', toggleFields));

    // Initial check on page load
    toggleFields();
});
</script>
@endpush
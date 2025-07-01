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

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Addresses</h4>
    <button type="button" id="add-address-btn" class="btn btn-sm btn-success">Add Address</button>
</div>

<div id="addresses-container">
    @php
        // Use old input if validation fails, otherwise use existing addresses or a single empty block for create form
        $addresses = old('addresses', $customer->addresses->count() > 0 ? $customer->addresses->toArray() : [['street_address_line_1' => '']]);
    @endphp

    @foreach($addresses as $index => $address)
        <div class="address-block border p-3 rounded mb-3 position-relative">
            <input type="hidden" name="addresses[{{ $index }}][address_id]" value="{{ $address['address_id'] ?? '' }}">

            <button type="button" class="btn-close remove-address-btn position-absolute top-0 end-0 mt-2 me-2" aria-label="Close" title="Remove Address"></button>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="address_type_{{ $index }}" class="form-label">Address Type</label>
                    <input type="text" class="form-control @error('addresses.'.$index.'.address_type') is-invalid @enderror" id="address_type_{{ $index }}" name="addresses[{{ $index }}][address_type]" value="{{ $address['address_type'] ?? '' }}" placeholder="e.g., Billing, Shipping">
                    @error('addresses.'.$index.'.address_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="street_address_line_1_{{ $index }}" class="form-label">Street Address</label>
                    <input type="text" class="form-control @error('addresses.'.$index.'.street_address_line_1') is-invalid @enderror" id="street_address_line_1_{{ $index }}" name="addresses[{{ $index }}][street_address_line_1]" value="{{ $address['street_address_line_1'] ?? '' }}">
                    @error('addresses.'.$index.'.street_address_line_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="city_{{ $index }}" class="form-label">City</label>
                    <input type="text" class="form-control @error('addresses.'.$index.'.city') is-invalid @enderror" id="city_{{ $index }}" name="addresses[{{ $index }}][city]" value="{{ $address['city'] ?? '' }}">
                    @error('addresses.'.$index.'.city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="state_province_{{ $index }}" class="form-label">State / Province</label>
                    <input type="text" class="form-control @error('addresses.'.$index.'.state_province') is-invalid @enderror" id="state_province_{{ $index }}" name="addresses[{{ $index }}][state_province]" value="{{ $address['state_province'] ?? '' }}">
                    @error('addresses.'.$index.'.state_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="postal_code_{{ $index }}" class="form-label">Postal Code</label>
                    <input type="text" class="form-control @error('addresses.'.$index.'.postal_code') is-invalid @enderror" id="postal_code_{{ $index }}" name="addresses[{{ $index }}][postal_code]" value="{{ $address['postal_code'] ?? '' }}">
                    @error('addresses.'.$index.'.postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="country_code_{{ $index }}" class="form-label">Country Code</label>
                    <input type="text" class="form-control @error('addresses.'.$index.'.country_code') is-invalid @enderror" id="country_code_{{ $index }}" name="addresses[{{ $index }}][country_code]" value="{{ $address['country_code'] ?? '' }}" placeholder="e.g., US, CA">
                    @error('addresses.'.$index.'.country_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="hidden" name="addresses[{{ $index }}][is_primary]" value="0">
                    <input class="form-check-input primary-address-checkbox" type="checkbox" name="addresses[{{ $index }}][is_primary]" id="is_primary_{{ $index }}" value="1" {{ !empty($address['is_primary']) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_primary_{{ $index }}">Set as Primary Address</label>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div id="address-template" style="display: none;">
    <div class="address-block border p-3 rounded mb-3 position-relative">
        <input type="hidden" name="addresses[__INDEX__][address_id]" value="">
        <button type="button" class="btn-close remove-address-btn position-absolute top-0 end-0 mt-2 me-2" aria-label="Close" title="Remove Address"></button>
        <div class="row">
            <div class="col-md-6 mb-3"><label for="address_type___INDEX__" class="form-label">Address Type</label><input type="text" class="form-control" id="address_type___INDEX__" name="addresses[__INDEX__][address_type]" placeholder="e.g., Billing, Shipping"></div>
            <div class="col-md-6 mb-3"><label for="street_address_line_1___INDEX__" class="form-label">Street Address</label><input type="text" class="form-control" id="street_address_line_1___INDEX__" name="addresses[__INDEX__][street_address_line_1]"></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3"><label for="city___INDEX__" class="form-label">City</label><input type="text" class="form-control" id="city___INDEX__" name="addresses[__INDEX__][city]"></div>
            <div class="col-md-6 mb-3"><label for="state_province___INDEX__" class="form-label">State / Province</label><input type="text" class="form-control" id="state_province___INDEX__" name="addresses[__INDEX__][state_province]"></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3"><label for="postal_code___INDEX__" class="form-label">Postal Code</label><input type="text" class="form-control" id="postal_code___INDEX__" name="addresses[__INDEX__][postal_code]"></div>
            <div class="col-md-6 mb-3"><label for="country_code___INDEX__" class="form-label">Country Code</label><input type="text" class="form-control" id="country_code___INDEX__" name="addresses[__INDEX__][country_code]" placeholder="e.g., US, CA"></div>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input type="hidden" name="addresses[__INDEX__][is_primary]" value="0">
                <input class="form-check-input primary-address-checkbox" type="checkbox" name="addresses[__INDEX__][is_primary]" id="is_primary___INDEX__" value="1">
                <label class="form-check-label" for="is_primary___INDEX__">Set as Primary Address</label>
            </div>
        </div>
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

    // --- Address Management ---
    const addressesContainer = document.getElementById('addresses-container');
    const addAddressBtn = document.getElementById('add-address-btn');
    const addressTemplate = document.getElementById('address-template').innerHTML;
    let addressIndex = {{ count($addresses) }};

    addAddressBtn.addEventListener('click', () => {
        const newAddressBlock = document.createElement('div');
        newAddressBlock.innerHTML = addressTemplate.replace(/__INDEX__/g, addressIndex);
        addressesContainer.appendChild(newAddressBlock.firstElementChild);
        addressIndex++;
    });

    addressesContainer.addEventListener('click', function(e) {
        // Handle remove button click
        if (e.target && e.target.classList.contains('remove-address-btn')) {
            e.target.closest('.address-block').remove();
        }

        // Handle primary checkbox click
        if (e.target && e.target.classList.contains('primary-address-checkbox')) {
            // If the clicked checkbox is checked
            if (e.target.checked) {
                // Uncheck all other primary checkboxes
                document.querySelectorAll('.primary-address-checkbox').forEach(checkbox => {
                    if (checkbox !== e.target) {
                        checkbox.checked = false;
                    }
                });
            }
        }
    });

});
</script>
@endpush
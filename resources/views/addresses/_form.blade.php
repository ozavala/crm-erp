@csrf
{{-- Note: Managing addressable_id and addressable_type directly in a standalone form is complex.
     These fields are often set programmatically when an address is created via a parent (e.g., Customer).
     For direct creation via this form, the user would need to know the exact model path and ID. --}}
@if(!isset($address) || !$address->exists) {{-- Only show for create form, make read-only or hidden for edit --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="addressable_type" class="form-label">{{ __('addresses.Addressable Type') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('addressable_type') is-invalid @enderror" id="addressable_type" name="addressable_type" value="{{ old('addressable_type', $address->addressable_type ?? 'App\\Models\\Customer') }}" placeholder="{{ __('addresses.e.g., App\\Models\\Customer') }}" required>
        @error('addressable_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="addressable_id" class="form-label">{{ __('addresses.Addressable ID') }} <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('addressable_id') is-invalid @enderror" id="addressable_id" name="addressable_id" value="{{ old('addressable_id', $address->addressable_id ?? '') }}" placeholder="{{ __('addresses.e.g., 1') }}" required>
        @error('addressable_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
@else
    <input type="hidden" name="addressable_type" value="{{ $address->addressable_type }}">
    <input type="hidden" name="addressable_id" value="{{ $address->addressable_id }}">
    <p><strong>{{ __('addresses.Belongs to:') }}</strong> {{ class_basename($address->addressable_type) }} #{{ $address->addressable_id }}</p>
@endif

<div class="row">
    <div class="col-md-12 mb-3">
        <label for="address_type" class="form-label">{{ __('addresses.Address Type') }}</label>
        <input type="text" class="form-control @error('address_type') is-invalid @enderror" id="address_type" name="address_type" value="{{ old('address_type', $address->address_type ?? 'Primary') }}" placeholder="{{ __('addresses.e.g., Billing, Shipping, Primary') }}">
        @error('address_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="street_address_line_1" class="form-label">{{ __('addresses.Street Address Line 1') }} <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('street_address_line_1') is-invalid @enderror" id="street_address_line_1" name="street_address_line_1" value="{{ old('street_address_line_1', $address->street_address_line_1 ?? '') }}" required>
    @error('street_address_line_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="street_address_line_2" class="form-label">{{ __('addresses.Street Address Line 2') }}</label>
    <input type="text" class="form-control @error('street_address_line_2') is-invalid @enderror" id="street_address_line_2" name="street_address_line_2" value="{{ old('street_address_line_2', $address->street_address_line_2 ?? '') }}">
    @error('street_address_line_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="city" class="form-label">{{ __('addresses.City') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $address->city ?? '') }}" required>
        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="state_province" class="form-label">{{ __('addresses.State/Province') }}</label>
        <input type="text" class="form-control @error('state_province') is-invalid @enderror" id="state_province" name="state_province" value="{{ old('state_province', $address->state_province ?? '') }}">
        @error('state_province') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="postal_code" class="form-label">{{ __('addresses.Postal Code') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" required>
        @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="country_code" class="form-label">{{ __('addresses.Country Code (2 Letter)') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('country_code') is-invalid @enderror" id="country_code" name="country_code" value="{{ old('country_code', $address->country_code ?? 'US') }}" maxlength="2" required>
        @error('country_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 form-check">
    <input type="hidden" name="is_primary" value="0"> {{-- Default value for unchecked checkbox --}}
    <input type="checkbox" class="form-check-input @error('is_primary') is-invalid @enderror" id="is_primary" name="is_primary" value="1" {{ old('is_primary', $address->is_primary ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_primary">{{ __('addresses.Set as Primary Address') }}</label>
    @error('is_primary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">{{ isset($address->address_id) ? __('addresses.Update Address') : __('addresses.Create Address') }}</button>
    <a href="{{ route('addresses.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
</div>

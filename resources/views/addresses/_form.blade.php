@csrf
<!-- No se si addressable es un atributo-->
<!--<div class="mb-3">
    <label for="addressable_type" class="form-label">Addressable Type</label>
    <input type="text" class="form-control @error('addressable_type') is-invalid @enderror" id="addressable_type" name="addressable_type" value="{{ old('addressable_type', $address->addressable_type ?? '') }}" required>
    @error('addressable_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>-->

<div class="mb-3">
    <label for="address_type" class="form-label">Address Type</label>
    <select name="address_type" id="address_type" class="form-select">
        <option value="billing" {{ old('address_type', $address->address_type ?? '') == 'billing' ? 'selected' : '' }}>Billing</option>
        <option value="shipping" {{ old('address_type', $address->address_type ?? '') == 'shipping' ? 'selected' : '' }}>Shipping</option>
    </select>
    @error('address_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label for="street_address_line_1" class="form-label    
">Street Address Line 1</label>
    <input type="text" class="form-control @error('street_address_line_1') is-invalid @enderror" id="street_address_line_1" name="street_address_line_1" value="{{ old('street_address_line_1', $address->street_address_line_1 ?? '') }}" required>
    @error('street_address_line_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label for="street_address_line_2" class="form-label
">Street Address Line 2</label>
    <input type="text" class="form-control @error('street_address_line_2') is-invalid @enderror" id="street_address_line_2" name="street_address_line_2" value="{{ old('street_address_line_2', $address->street_address_line_2 ?? '') }}">
    @error('street_address_line_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="city" class="form-label">City</label>
        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $address->city ?? '') }}" required>
        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="state_province" class="form-label">State/Province</label>
        <input type="text" class="form-control @error('state_province') is-invalid @enderror" id="state_province" name="state_province" value="{{ old('state_province', $address->state_province ?? '') }}" required>
        @error('state_province') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="postal_code" class="form-label">Postal Code</label>
        <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" required>
        @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="country_code" class="form-label">Country</label>
        <input type='text' class = form-control @error('country_code') is-invalid @enderror" id="country_code" name="country_code" value="{{ old('country_code', $address->country_code ?? '') }}" placeholder="e.g. EC, US" required>
       <!-- <select name="country_code" id="country_code" class="form-select @error('country_code') is-invalid @enderror">
            <option value="">Select Country</option>
           {{-- @foreach($countries as $code => $name)
                <option value="{{ $code }}" {{ old('country_code', $address->country_code ?? '') == $code ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach--}}
        </select>-->
        @error('country_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
<div class="form-check mb-3">
    <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" value="1" {{ old('is_primary', $address->is_primary ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_primary">Set as Primary Address</label> 
    @error('is_primary') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>  
<button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Save Address' }}</button>
@if(isset($address) && $address->address_id)
    <a href="{{ route('addresses.show', $address->address_id) }}" class="btn btn-secondary">View Address</a>
@endif
@if(isset($cancelUrl))
    <a href="{{ $cancelUrl }}" class="btn btn-secondary">Cancel</a> 
@endif
@if(isset($deleteUrl))
    <form action="{{ $deleteUrl }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Address</button>
    </form>
@endif
@if(isset($address) && $address->addressable)
    <input type="hidden" name="addressable_id" value="{{ $address->addressable_id }}">
    <input type="hidden" name="addressable_type" value="{{ get_class($address->addressable) }}"> 
@endif
@if(isset($address) && $address->notes)
    <div class="mb-3">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $address->notes ?? '') }}</textarea>
        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>      
@endif            


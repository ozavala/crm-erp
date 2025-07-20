@csrf
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $supplier->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="contact_person" class="form-label">Contacto</label>
        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Correo</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $supplier->email ?? '') }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="phone_number" class="form-label">Teléfono</label>
        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $supplier->phone_number ?? '') }}">
        @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="legal_id" class="form-label">{{ __('Legal ID') }} / {{ __('Tax ID') }}</label>
    <input type="text" class="form-control @error('legal_id') is-invalid @enderror" id="legal_id" name="legal_id" value="{{ old('legal_id', $supplier->legal_id ?? '') }}" required>
    @error('legal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<hr>
<h5>Dirección</h5>
@php
    $addressData = old('addresses.0');
    if ($addressData) {
        $address = new \App\Models\Address($addressData);
    } else {
        $address = $supplier->addresses->first() ?? new \App\Models\Address(['is_primary' => !$supplier->exists]);
    }
@endphp
<input type="hidden" name="addresses[0][address_id]" value="{{ $address->address_id ?? '' }}">

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="address_type_0" class="form-label">Tipo</label>
        <input type="text" class="form-control @error('addresses.0.address_type') is-invalid @enderror" id="address_type_0" name="addresses[0][address_type]" value="{{ old('addresses.0.address_type', $address->address_type ?? 'Primary') }}">
        @error('addresses.0.address_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-8 mb-3">
        <label for="street_address_line_1_0" class="form-label">Dirección 1</label>
        <input type="text" class="form-control @error('addresses.0.street_address_line_1') is-invalid @enderror" id="street_address_line_1_0" name="addresses[0][street_address_line_1]" value="{{ old('addresses.0.street_address_line_1', $address->street_address_line_1 ?? '') }}">
        @error('addresses.0.street_address_line_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="street_address_line_2_0" class="form-label">Dirección 2</label>
    <input type="text" class="form-control @error('addresses.0.street_address_line_2') is-invalid @enderror" id="street_address_line_2_0" name="addresses[0][street_address_line_2]" value="{{ old('addresses.0.street_address_line_2', $address->street_address_line_2 ?? '') }}">
    @error('addresses.0.street_address_line_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="city_0" class="form-label">Ciudad</label>
        <input type="text" class="form-control @error('addresses.0.city') is-invalid @enderror" id="city_0" name="addresses[0][city]" value="{{ old('addresses.0.city', $address->city ?? '') }}">
        @error('addresses.0.city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="state_province_0" class="form-label">Provincia</label>
        <input type="text" class="form-control @error('addresses.0.state_province') is-invalid @enderror" id="state_province_0" name="addresses[0][state_province]" value="{{ old('addresses.0.state_province', $address->state_province ?? '') }}">
        @error('addresses.0.state_province') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="postal_code_0" class="form-label">Código Postal</label>
        <input type="text" class="form-control @error('addresses.0.postal_code') is-invalid @enderror" id="postal_code_0" name="addresses[0][postal_code]" value="{{ old('addresses.0.postal_code', $address->postal_code ?? '') }}">
        @error('addresses.0.postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row align-items-center">
    <div class="col-md-4 mb-3">
        <label for="country_code_0" class="form-label">País</label>
        <input type="text" class="form-control @error('addresses.0.country_code') is-invalid @enderror" id="country_code_0" name="addresses[0][country_code]" value="{{ old('addresses.0.country_code', $address->country_code ?? 'US') }}" maxlength="2">
        @error('addresses.0.country_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3 mt-3 form-check">
        <input type="hidden" name="addresses[0][is_primary]" value="0">
        <input type="checkbox" class="form-check-input @error('addresses.0.is_primary') is-invalid @enderror" id="is_primary_0" name="addresses[0][is_primary]" value="1" {{ old('addresses.0.is_primary', $address->is_primary ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_primary_0">Dirección Principal</label>
        @error('addresses.0.is_primary') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
<hr>

<div class="mb-3">
    <label for="notes" class="form-label">Notas</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $supplier->notes ?? '') }}</textarea>
    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancelar</a>
</div>
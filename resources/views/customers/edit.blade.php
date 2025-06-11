@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container">
    <h1>Edit Customer: {{ $customer->full_name }}</h1>

    <form action="{{ route('customers.update', $customer->customer_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name) }}" required>
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name) }}" required>
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $customer->phone_number) }}">
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name) }}">
            @error('company_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                <option value="">Select Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $customer->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <fieldset class="mb-3">
            <legend class="col-form-label col-sm-2 pt-0">Address</legend>
            <div class="mb-3">
                <label for="address_street" class="form-label">Street</label>
                <input type="text" class="form-control @error('address_street') is-invalid @enderror" id="address_street" name="address_street" value="{{ old('address_street', $customer->address_street) }}">
                @error('address_street') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="address_city" class="form-label">City</label>
                    <input type="text" class="form-control @error('address_city') is-invalid @enderror" id="address_city" name="address_city" value="{{ old('address_city', $customer->address_city) }}">
                    @error('address_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="address_state" class="form-label">State/Province</label>
                    <input type="text" class="form-control @error('address_state') is-invalid @enderror" id="address_state" name="address_state" value="{{ old('address_state', $customer->address_state) }}">
                    @error('address_state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label for="address_postal_code" class="form-label">Postal Code</label>
                    <input type="text" class="form-control @error('address_postal_code') is-invalid @enderror" id="address_postal_code" name="address_postal_code" value="{{ old('address_postal_code', $customer->address_postal_code) }}">
                    @error('address_postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="address_country" class="form-label">Country</label>
                    <input type="text" class="form-control @error('address_country') is-invalid @enderror" id="address_country" name="address_country" value="{{ old('address_country', $customer->address_country) }}">
                    @error('address_country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </fieldset>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $customer->notes) }}</textarea>
            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Customer</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
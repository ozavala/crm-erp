@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="container">
    <h1>Company Settings</h1>
    <p>These details will appear on documents like Purchase Orders and Invoices.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="company_email" class="form-label">Company Email</label>
                        <input type="email" class="form-control" id="company_email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="company_address_line_1" class="form-label">Address Line 1</label>
                    <input type="text" class="form-control" id="company_address_line_1" name="company_address_line_1" value="{{ old('company_address_line_1', $settings['company_address_line_1'] ?? '') }}">
                </div>

                <div class="mb-3">
                    <label for="company_address_line_2" class="form-label">Address Line 2 (City, State, Postal Code)</label>
                    <input type="text" class="form-control" id="company_address_line_2" name="company_address_line_2" value="{{ old('company_address_line_2', $settings['company_address_line_2'] ?? '') }}">
                </div>

                <div class="mb-3">
                    <label for="company_phone" class="form-label">Company Phone</label>
                    <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}">
                </div>

                <div class="mb-3">
                    <label for="company_logo" class="form-label">Company Logo</label>
                    <input type="file" class="form-control" id="company_logo" name="company_logo">
                    @if(isset($settings['company_logo']) && $settings['company_logo'])
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Company Logo" style="max-height: 100px;">
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection
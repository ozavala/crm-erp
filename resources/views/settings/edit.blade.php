@extends('layouts.app')

@section('title', 'Application Settings')

@section('content')
<div class="container">
    <h1>Application Settings</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="card mb-4">
            <div class="card-header">Company Settings</div>
            <div class="card-body">
                <p class="text-muted">These details will appear on documents like Purchase Orders and Invoices.</p>
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
            </div>
        </div>
 
        <div class="card mb-4">
            <div class="card-header">Outgoing Mail (SMTP)</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="mail_mailer" class="form-label">Mailer</label>
                        <input type="text" class="form-control" id="mail_mailer" name="mail_mailer" value="{{ old('mail_mailer', $settings['mail_mailer'] ?? 'smtp') }}" readonly>
                        <small class="text-muted">Currently only 'smtp' is supported.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mail_host" class="form-label">Host</label>
                        <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="mail_port" class="form-label">Port</label>
                        <input type="number" class="form-control" id="mail_port" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="mail_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="mail_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="mail_encryption" class="form-label">Encryption</label>
                        <select class="form-select" id="mail_encryption" name="mail_encryption">
                            <option value="tls" @if(old('mail_encryption', $settings['mail_encryption'] ?? '') == 'tls') selected @endif>TLS</option>
                            <option value="ssl" @if(old('mail_encryption', $settings['mail_encryption'] ?? '') == 'ssl') selected @endif>SSL</option>
                            <option value="starttls" @if(old('mail_encryption', $settings['mail_encryption'] ?? '') == 'starttls') selected @endif>STARTTLS</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="mail_from_address" class="form-label">From Address</label>
                        <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="mail_from_name" class="form-label">From Name</label>
                        <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
 
        <button type="submit" class="btn btn-primary">Save All Settings</button>
    </form>
</div>
@endsection
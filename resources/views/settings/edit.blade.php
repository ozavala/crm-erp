@extends('layouts.app')

@section('title', 'Application Settings')

@section('content')
<div class="container">
    <h1>Settings</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Core Settings --}}
    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="card mb-4">
            <div class="card-header">{{ __('messages.Core Settings') }}</div>
            <div class="card-body">
                <div class="row">
                    @foreach($coreSettings as $setting)
                        <div class="col-md-6 mb-3">
                            @if($setting->key === 'default_country_tax')
                                <label for="default_country_tax" class="form-label">{{ __('messages.Default Country Tax') }}</label>
                                <select class="form-select" id="default_country_tax" name="default_country_tax">
                                    <option value="ecuador" {{ old('default_country_tax', $setting->value) == 'ecuador' ? 'selected' : '' }}>Ecuador</option>
                                    <option value="spain" {{ old('default_country_tax', $setting->value) == 'spain' ? 'selected' : '' }}>España</option>
                                    <option value="mexico" {{ old('default_country_tax', $setting->value) == 'mexico' ? 'selected' : '' }}>México</option>
                                    <option value="argentina" {{ old('default_country_tax', $setting->value) == 'argentina' ? 'selected' : '' }}>Argentina</option>
                                    <option value="colombia" {{ old('default_country_tax', $setting->value) == 'colombia' ? 'selected' : '' }}>Colombia</option>
                                </select>
                            @elseif($setting->key === 'tax_includes_services')
                                <label for="tax_includes_services" class="form-label">{{ __('messages.Tax Includes Services') }}</label>
                                <select class="form-select" id="tax_includes_services" name="tax_includes_services">
                                    <option value="true" {{ old('tax_includes_services', $setting->value) == 'true' ? 'selected' : '' }}>{{ __('messages.Yes') }}</option>
                                    <option value="false" {{ old('tax_includes_services', $setting->value) == 'false' ? 'selected' : '' }}>{{ __('messages.No') }}</option>
                                </select>
                            @elseif($setting->key === 'tax_includes_transport')
                                <label for="tax_includes_transport" class="form-label">{{ __('messages.Tax Includes Transport') }}</label>
                                <select class="form-select" id="tax_includes_transport" name="tax_includes_transport">
                                    <option value="true" {{ old('tax_includes_transport', $setting->value) == 'true' ? 'selected' : '' }}>{{ __('messages.Yes') }}</option>
                                    <option value="false" {{ old('tax_includes_transport', $setting->value) == 'false' ? 'selected' : '' }}>{{ __('messages.No') }}</option>
                                </select>
                            @elseif($setting->key === 'company_name')
                                <label for="company_name" class="form-label">{{ __('messages.Company Name') }}</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $setting->value) }}">
                            @elseif($setting->key === 'company_address_line_1')
                                <label for="company_address_line_1" class="form-label">{{ __('messages.Address') }}</label>
                                <input type="text" class="form-control" id="company_address_line_1" name="company_address_line_1" value="{{ old('company_address_line_1', $setting->value) }}">
                            @elseif($setting->key === 'company_address_line_2')
                                <label for="company_address_line_2" class="form-label">{{ __('messages.Address 2') }}</label>
                                <input type="text" class="form-control" id="company_address_line_2" name="company_address_line_2" value="{{ old('company_address_line_2', $setting->value) }}">
                            @elseif($setting->key === 'company_email')
                                <label for="company_email" class="form-label">{{ __('messages.Email') }}</label>
                                <input type="email" class="form-control" id="company_email" name="company_email" value="{{ old('company_email', $setting->value) }}">
                            @elseif($setting->key === 'company_phone')
                                <label for="company_phone" class="form-label">{{ __('messages.Phone') }}</label>
                                <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{ old('company_phone', $setting->value) }}">
                            @elseif($setting->key === 'company_logo')
                                <label for="company_logo" class="form-label">{{ __('messages.Logo') }}</label>
                                <input type="file" class="form-control" id="company_logo" name="company_logo">
                            @elseif($setting->key === 'default_locale')
                                <label for="default_locale" class="form-label">{{ __('messages.Default Locale') }}</label>
                                <input type="text" class="form-control" id="default_locale" name="default_locale" value="{{ old('default_locale', $setting->value) }}">
                            @elseif($setting->key === 'default_currency')
                                <label for="default_currency" class="form-label">{{ __('messages.Default Currency') }}</label>
                                <input type="text" class="form-control" id="default_currency" name="default_currency" value="{{ old('default_currency', $setting->value) }}">
                            @else
                                <label for="{{ $setting->key }}" class="form-label">{{ __('messages.Setting') }}</label>
                                <input type="text" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('settings.edit') }}" class="btn btn-secondary">Delete</a>
    </form>

    {{-- Custom Settings --}}
    <div class="card mt-5">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('messages.Custom Settings') }}</span>
            <form action="{{ route('settings.custom.store') }}" method="POST" class="d-flex gap-2 align-items-center mb-0">
                @csrf
                <input type="text" name="key" class="form-control form-control-sm" placeholder="{{ __('messages.New Setting Key') }}" required>
                <input type="text" name="value" class="form-control form-control-sm" placeholder="{{ __('messages.New Setting Value') }}">
                <button type="submit" class="btn btn-success btn-sm">{{ __('messages.Add') }}</button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customSettings as $setting)
                        <tr>
                            <td>{{ $setting->key }}</td>
                            <td>{{ $setting->value }}</td>
                            <td>
                                <form action="{{ route('settings.custom.destroy', $setting) }}" method="POST" onsubmit="return confirm('{{ __('messages.Are you sure?') }}');" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">{{ __('messages.No results found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <form action="{{ route('settings.custom.store') }}" method="POST" class="d-flex gap-2 align-items-center mb-0">
                @csrf
                <input type="text" name="key" class="form-control form-control-sm" placeholder="{{ __('messages.New Setting Key') }}" required>
                <input type="text" name="value" class="form-control form-control-sm" placeholder="{{ __('messages.New Setting Value') }}">
                <button type="submit" class="btn btn-success btn-sm">{{ __('messages.Add Personal Configuration') }}</button>
            </form>
        </div>
    </div>
    <a href="{{ route('settings.edit') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
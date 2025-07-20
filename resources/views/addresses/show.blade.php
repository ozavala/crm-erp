@extends('layouts.app')

@section('title', __('addresses.Address Details'))

@section('content')
<div class="container">
    <h1>{{ __('addresses.Address') }} #{{ $address->address_id }}</h1>

    <div class="card">
        <div class="card-header">
            {{ __('addresses.Details') }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('addresses.Address ID:') }}</strong> {{ $address->address_id }}</p>
            <p><strong>{{ __('addresses.Address Type:') }}</strong> {{ $address->address_type ?: __('N/A') }}</p>
            <p><strong>{{ __('addresses.Street Address Line 1:') }}</strong> {{ $address->street_address_line_1 }}</p>
            <p><strong>{{ __('addresses.Street Address Line 2:') }}</strong> {{ $address->street_address_line_2 ?: __('N/A') }}</p>
            <p><strong>{{ __('addresses.City:') }}</strong> {{ $address->city }}</p>
            <p><strong>{{ __('addresses.State/Province:') }}</strong> {{ $address->state_province ?: __('N/A') }}</p>
            <p><strong>{{ __('addresses.Postal Code:') }}</strong> {{ $address->postal_code }}</p>
            <p><strong>{{ __('addresses.Country Code:') }}</strong> {{ $address->country_code }}</p>
            <p><strong>{{ __('addresses.Is Primary:') }}</strong> {!! $address->is_primary ? '<span class="badge bg-success">' . __('Yes') . '</span>' : '<span class="badge bg-secondary">' . __('No') . '</span>' !!}</p>

            @if($address->addressable)
                <hr>
                <h5>{{ __('addresses.Associated With:') }}</h5>
                <p>
                    <strong>{{ __('addresses.Type:') }}</strong> {{ class_basename($address->addressable_type) }}<br>
                    <strong>{{ __('addresses.ID:') }}</strong> {{ $address->addressable_id }}
                    {{-- You could try to link to the parent record if you have a consistent URL structure
                    @php $parentName = strtolower(Str::plural(class_basename($address->addressable_type))); @endphp
                    @if(Route::has($parentName . '.show'))
                        <a href="{{ route($parentName . '.show', $address->addressable_id) }}">(View Parent)</a>
                    @endif
                    --}}
                </p>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('addresses.edit', $address->address_id) }}" class="btn btn-warning">{{ __('addresses.Edit') }}</a>
                <form action="{{ route('addresses.destroy', $address->address_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm(__('addresses.Are you sure?'));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('addresses.Delete') }}</button>
                </form>
            </div>
            <a href="{{ route('addresses.index') }}" class="btn btn-secondary">{{ __('addresses.Back to List') }}</a>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', __('messages.Details') . ' - ' . $supplier->name)

@section('content')
<div class="container">
    <h1>Proveedor: {{ $supplier->full_name }}</h1>

    <div class="card">
        <div class="card-header">
            {{ __('suppliers.supplier_id') }}: {{ $supplier->supplier_id }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre:</strong> {{ $supplier->name }}</p>
                    <p><strong>Contacto:</strong> {{ $supplier->contact_person}}</p>
                    <p><strong>Correo:</strong> {{ $supplier->email ?: 'N/A' }}</p>
                    <p><strong>Teléfono:</strong> {{ $supplier->phone_number ?: 'N/A' }}</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Legal ID') }} / {{ __('Tax ID') }}</label>
                        <div>{{ $supplier->legal_id }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Direcciones</h5>
                    @forelse ($supplier->addresses as $address)
                        <div class="mb-2 p-2 border rounded {{ $address->is_primary ? 'border-primary' : '' }}">
                            <strong>{{ $address->address_type ?: __('messages.Address') }} {{ $address->is_primary ? '(' . __('messages.Primary') . ')' : '' }}</strong><br>
                            {{ $address->street_address_line_1 }}<br>
                            @if($address->street_address_line_2)
                                {{ $address->street_address_line_2 }}<br>
                            @endif
                            {{ $address->city }}, {{ $address->state_province }} {{ $address->postal_code }}<br>
                            {{ $address->country_code }}
                        </div>
                    @empty
                        <p>{{ __('suppliers.no_addresses_on_file') }}</p>
                    @endforelse
                    {{-- Old address fields (can be removed after migration) --}}
                    @if(empty($supplier->addresses->first()) && ($supplier->address_street || $supplier->address_city))
                        <p class="text-muted small"><em>{{ __('messages.Legacy Address') }}: {{ $supplier->address_street }}, {{ $supplier->address_city }}, {{ $supplier->address_state }} {{ $supplier->address_postal_code }} {{ $supplier->address_country }}</em></p>
                    @endif
                </div>
            </div>

           
            <hr>

            <p><strong>{{ __('messages.Created By') }}:</strong> {{ $supplier->createdBy ? $supplier->createdBy->full_name : __('messages.N/A') }} ({{ $supplier->createdBy ? $supplier->createdBy->username : __('messages.N/A') }})</p>
            <p><strong>{{ __('messages.Created At') }}:</strong> {{ $supplier->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('messages.Updated At') }}:</strong> {{ $supplier->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-warning">Editar</a>
                <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Volver al listado</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Contactos</h2>
            <a href="{{ route('contacts.create', ['supplier_id' => $supplier->supplier_id]) }}" class="btn btn-primary btn-sm">Nuevo Contacto</a>
        </div>
        <div class="card-body">
            @if($supplier->contacts->isNotEmpty())
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->contacts as $contact)
                            <tr>
                                <td><a href="{{ route('contacts.show', $contact) }}">{{ $contact->first_name }} {{ $contact->last_name }}</a></td>
                                <td>{{ $contact->title }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>
                                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-secondary btn-sm">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No se encontraron resultados</p>
            @endif
        </div>
    </div>

    {{-- Notes Section --}}
    @include('partials._notes', ['model' => $supplier])

     {{-- Tasks Section --}}
    @include('partials._tasks', ['model' => $supplier])
</div>
@endsection
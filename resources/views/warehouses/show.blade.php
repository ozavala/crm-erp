@extends('layouts.app')

@section('title', __('warehouses.details'))

@section('content')
<div class="container">
    <h1>{{ __('warehouses.warehouse') }}: {{ $warehouse->name }} <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'danger' }}">{{ $warehouse->is_active ? __('warehouses.active') : __('warehouses.inactive') }}</span></h1>

    <div class="card">
        <div class="card-header">
            {{ __('warehouses.id') }}: {{ $warehouse->warehouse_id }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('warehouses.name') }}:</strong> {{ $warehouse->name }}</p>
            <p><strong>{{ __('warehouses.location') }}:</strong> {{ $warehouse->location ?: __('N/A') }}</p>
            <p><strong>{{ __('warehouses.address') }}:</strong></p>
            <p>{{ nl2br(e($warehouse->address)) ?: __('N/A') }}</p>
            <hr>
            <p><strong>{{ __('warehouses.created_at') }}:</strong> {{ $warehouse->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('warehouses.updated_at') }}:</strong> {{ $warehouse->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="{{ route('warehouses.edit', $warehouse->warehouse_id) }}" class="btn btn-warning">{{ __('warehouses.edit') }}</a>
                <form action="{{ route('warehouses.destroy', $warehouse->warehouse_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm(__('warehouses.delete_confirm'));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('warehouses.delete') }}</button>
                </form>
            </div>
            <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">{{ __('warehouses.back_to_list') }}</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5>{{ __('warehouses.inventory_in_this_warehouse') }}</h5>
        </div>
        <div class="card-body">
            {{-- Placeholder for listing products and their quantities in this warehouse --}}
            <p>{{ __('warehouses.inventory_placeholder') }}</p>
        </div>
    </div>
</div>
@endsection
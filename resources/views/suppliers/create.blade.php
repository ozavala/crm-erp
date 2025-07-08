@extends('layouts.app')

@section('title', 'Create New Supplier')

@section('content')
<div class="container">
    <h1>Crear Proveedor</h1>

    <form action="{{ route('suppliers.store') }}" method="POST">
        @include('suppliers._form', ['supplier' => new \App\Models\Supplier()])
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
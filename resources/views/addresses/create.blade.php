@extends('layouts.app')

@section('title', 'Create New Address')

@section('content')
<div class="container">
    <h1>Create New Address</h1>
    <p class="text-muted">Note: Addresses are typically created via their parent record (e.g., Customer, Supplier). Creating a standalone address requires specifying the parent type and ID.</p>

    <form action="{{ route('addresses.store') }}" method="POST">
        @include('addresses._form', ['address' => new \App\Models\Address()])
    </form>
</div>
@endsection
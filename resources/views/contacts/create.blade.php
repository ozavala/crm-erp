@extends('layouts.app')

@section('title', 'Create New Contact')

@section('content')
<div class="container">
    <h1>Create New Contact</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contacts.store') }}" method="POST">
                @csrf
                @include('contacts._form')
            </form>
        </div>
    </div>
</div>
@endsection
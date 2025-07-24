@extends('calendar.layout')

@section('header')
    Edit Appointment
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('appointments.show', $appointment) }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Appointment
        </a>
    </div>

    <form method="POST" action="{{ route('appointments.update', $appointment) }}">
        @csrf
        @method('PUT')
        @include('appointments.form')
    </form>
@endsection
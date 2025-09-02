@extends('calendar.layout')

@section('header')
    Create Appointment
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('calendar') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Calendar
        </a>
    </div>

    <form method="POST" action="{{ route('appointments.store') }}">
        @csrf
        @include('appointments.form')
    </form>
@endsection
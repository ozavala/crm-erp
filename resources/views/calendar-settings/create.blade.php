@extends('calendar.layout')

@section('header')
    Add New Calendar Setting
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('calendar-settings.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Calendar Settings
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" action="{{ route('calendar-settings.store') }}">
            @csrf
            @include('calendar-settings.form')
        </form>
    </div>
@endsection
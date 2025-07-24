@extends('calendar.layout')

@section('header')
    Create New Task
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Tasks
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            @include('tasks.form')
        </form>
    </div>
@endsection
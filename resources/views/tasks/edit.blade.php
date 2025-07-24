@extends('calendar.layout')

@section('header')
    Edit Task
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Task
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')
            @include('tasks.form')
        </form>
    </div>
@endsection
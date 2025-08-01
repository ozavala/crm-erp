@extends('layouts.app')

@section('content')
    @livewire('kanban-board')
@endsection

@push('scripts')
    @vite('resources/js/kanban.js')
@endpush

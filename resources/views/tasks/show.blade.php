@extends('calendar.layout')

@section('header')
    Task Details
@endsection

@section('content')
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Tasks
        </a>
        <div class="space-x-2">
            <a href="{{ route('tasks.edit', $task) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this task?')">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ $task->title }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    @if($task->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                    @elseif($task->status == 'completed') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Description
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $task->description }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Start Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $task->start_date ? $task->start_date->format('F j, Y') . ' at ' . $task->start_date->format('g:i A') : 'Not specified' }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Due Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $task->due_date ? $task->due_date->format('F j, Y') . ' at ' . $task->due_date->format('g:i A') : 'No due date' }}
                        @if($task->due_date && $task->due_date->isPast() && $task->status != 'completed')
                            <span class="text-red-500 ml-2">Overdue</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Priority
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($task->priority == 'high') bg-red-100 text-red-800
                            @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Assigned To
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Created By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $task->createdBy->name }}
                    </dd>
                </div>
                @if($task->related_to_type)
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Related To
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ class_basename($task->related_to_type) }} #{{ $task->related_to_id }}
                        @if($task->relatedTo)
                            - {{ $task->relatedTo->name ?? $task->relatedTo->title ?? 'Unknown' }}
                        @endif
                    </dd>
                </div>
                @endif
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Notes
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $task->notes ?: 'No notes' }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Calendar
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($task->calendarEvent)
                            <span class="text-green-600">Added to Calendar</span>
                            @if($task->calendarEvent->calendarSetting)
                                <span class="ml-2">{{ $task->calendarEvent->calendarSetting->name }}</span>
                            @endif
                            @if($task->calendarEvent->google_event_id)
                                <span class="ml-2 text-green-600">(Synced with Google Calendar)</span>
                            @endif
                        @else
                            <span class="text-gray-500">Not added to calendar</span>
                            <form method="POST" action="{{ route('tasks.add-to-calendar', $task) }}" class="inline ml-2">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-900">
                                    Add to Calendar
                                </button>
                            </form>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    @if($task->status != 'completed')
        <div class="mt-6 flex justify-end">
            <form method="POST" action="{{ route('tasks.mark-as-completed', $task) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Mark as Completed
                </button>
            </form>
        </div>
    @endif
@endsection
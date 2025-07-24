@extends('calendar.layout')

@section('header')
    Tasks
@endsection

@section('content')
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">All Tasks</h2>
        <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create New Task
        </a>
    </div>

    <div class="mb-4 flex justify-between">
        <div class="flex space-x-2">
            <a href="{{ route('tasks.index', ['status' => 'all']) }}" class="px-3 py-2 rounded {{ request()->get('status', '') == 'all' || !request()->has('status') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                All
            </a>
            <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="px-3 py-2 rounded {{ request()->get('status') == 'pending' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Pending
            </a>
            <a href="{{ route('tasks.index', ['status' => 'in_progress']) }}" class="px-3 py-2 rounded {{ request()->get('status') == 'in_progress' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                In Progress
            </a>
            <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="px-3 py-2 rounded {{ request()->get('status') == 'completed' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Completed
            </a>
        </div>
        <div>
            <a href="{{ route('calendar.index') }}" class="text-blue-600 hover:text-blue-800">
                View Calendar &rarr;
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Due Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Priority
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Assigned To
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tasks as $task)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $task->title }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ Str::limit($task->description, 50) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $task->due_date ? $task->due_date->format('M j, Y') : 'No due date' }}
                            </div>
                            @if($task->due_date && $task->due_date->isPast() && $task->status != 'completed')
                                <div class="text-sm text-red-500">
                                    Overdue
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($task->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($task->status == 'completed') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($task->priority == 'high') bg-red-100 text-red-800
                                @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('tasks.show', $task) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                            <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this task?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No tasks found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
@endsection
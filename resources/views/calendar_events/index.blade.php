@extends('calendar.layout')

@section('header')
    Calendar Events
@endsection

@section('content')
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">All Calendar Events</h2>
        <div class="space-x-2">
            <a href="{{ route('calendar-events.export-form') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-file-export mr-1"></i> Export Calendar
            </a>
            <a href="{{ route('calendar.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-calendar-alt mr-1"></i> View Calendar
            </a>
        </div>
    </div>

    <div class="mb-4 bg-white shadow-md rounded-lg p-4">
        <form method="GET" action="{{ route('calendar-events.index') }}" class="flex flex-wrap gap-4">
            <div>
                <label for="calendar_id" class="block text-sm font-medium text-gray-700">Calendar</label>
                <select name="calendar_id" id="calendar_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Calendars</option>
                    @foreach($calendarSettings as $setting)
                        <option value="{{ $setting->calendar_id }}" {{ request('calendar_id') == $setting->calendar_id ? 'selected' : '' }}>
                            {{ $setting->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="related_type" class="block text-sm font-medium text-gray-700">Event Type</label>
                <select name="related_type" id="related_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Types</option>
                    <option value="appointment" {{ request('related_type') == 'appointment' ? 'selected' : '' }}>Appointments</option>
                    <option value="task" {{ request('related_type') == 'task' ? 'selected' : '' }}>Tasks</option>
                </select>
            </div>
            
            <div>
                <label for="sync_status" class="block text-sm font-medium text-gray-700">Sync Status</label>
                <select name="sync_status" id="sync_status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('sync_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="synced" {{ request('sync_status') == 'synced' ? 'selected' : '' }}>Synced</option>
                    <option value="failed" {{ request('sync_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Event
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Calendar
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sync Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Last Synced
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($calendarEvents as $event)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                @if($event->related)
                                    @if($event->related_type === 'appointment')
                                        <a href="{{ route('appointments.show', $event->related->appointment_id) }}">
                                            {{ $event->related->title }}
                                        </a>
                                    @elseif($event->related_type === 'task')
                                        <a href="{{ route('tasks.show', $event->related->task_id) }}">
                                            {{ $event->related->title }}
                                        </a>
                                    @endif
                                @else
                                    <span class="text-red-500">Missing related item</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($event->related_type === 'appointment') bg-blue-100 text-blue-800
                                @elseif($event->related_type === 'task') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($event->related_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $event->google_calendar_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($event->sync_status === 'synced') bg-green-100 text-green-800
                                @elseif($event->sync_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($event->sync_status === 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($event->sync_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $event->last_synced_at ? $event->last_synced_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('calendar-events.show', $event) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                            
                            @if($event->sync_status !== 'synced')
                                <form method="POST" action="{{ route('calendar-events.sync', $event) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 mr-2">Sync</button>
                                </form>
                            @endif
                            
                            <form method="POST" action="{{ route('calendar-events.destroy', $event) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this calendar event?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No calendar events found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $calendarEvents->links() }}
    </div>

    <div class="mt-6 flex justify-end">
        <form method="POST" action="{{ route('calendar-events.sync-all') }}" class="inline">
            @csrf
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Sync All Pending Events
            </button>
        </form>
    </div>
@endsection
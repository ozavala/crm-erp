@extends('calendar.layout')

@section('header')
    Appointments
@endsection

@section('content')
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">All Appointments</h2>
        <a href="{{ route('appointments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create New Appointment
        </a>
    </div>

    <div class="mb-4 flex justify-between">
        <div class="flex space-x-2">
            <a href="{{ route('appointments.index', ['status' => 'all']) }}" class="px-3 py-2 rounded {{ request()->get('status', '') == 'all' || !request()->has('status') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                All
            </a>
            <a href="{{ route('appointments.index', ['status' => 'scheduled']) }}" class="px-3 py-2 rounded {{ request()->get('status') == 'scheduled' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Scheduled
            </a>
            <a href="{{ route('appointments.index', ['status' => 'completed']) }}" class="px-3 py-2 rounded {{ request()->get('status') == 'completed' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Completed
            </a>
            <a href="{{ route('appointments.index', ['status' => 'cancelled']) }}" class="px-3 py-2 rounded {{ request()->get('status') == 'cancelled' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Cancelled
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
                        Date & Time
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Priority
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Participants
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($appointments as $appointment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $appointment->title }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ Str::limit($appointment->description, 50) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $appointment->start_date->format('M j, Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $appointment->start_date->format('g:i A') }} - {{ $appointment->end_date->format('g:i A') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($appointment->status == 'scheduled') bg-blue-100 text-blue-800
                                @elseif($appointment->status == 'completed') bg-green-100 text-green-800
                                @elseif($appointment->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($appointment->priority == 'high') bg-red-100 text-red-800
                                @elseif($appointment->priority == 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($appointment->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $appointment->participants->count() }} participant(s)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('appointments.show', $appointment) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                            <a href="{{ route('appointments.edit', $appointment) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this appointment?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No appointments found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $appointments->links() }}
    </div>
@endsection
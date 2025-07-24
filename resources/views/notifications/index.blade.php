@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
        
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.mark-all-as-read') }}">
            @csrf
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Mark All as Read
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <div class="p-4 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }} hover:bg-gray-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-0.5">
                            @if($notification->type == 'App\\Notifications\\AppointmentReminder')
                                <svg class="h-10 w-10 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="h-10 w-10 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex justify-between items-baseline">
                                <p class="text-sm font-medium text-gray-900">
                                    @if($notification->type == 'App\\Notifications\\AppointmentReminder')
                                        Appointment Reminder
                                    @else
                                        {{ class_basename($notification->type) }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="mt-1 text-sm text-gray-700">
                                @if($notification->type == 'App\\Notifications\\AppointmentReminder')
                                    <p><strong>{{ $notification->data['title'] }}</strong></p>
                                    <p>{{ $notification->data['message'] }}</p>
                                    <div class="mt-2">
                                        <a href="{{ url('/appointments/' . $notification->data['appointment_id']) }}" class="text-blue-600 hover:text-blue-800">
                                            View Appointment
                                        </a>
                                    </div>
                                @else
                                    <p>{{ json_encode($notification->data) }}</p>
                                @endif
                            </div>
                            @if(!$notification->read_at)
                                <div class="mt-2">
                                    <form method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                            Mark as Read
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    No notifications found.
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
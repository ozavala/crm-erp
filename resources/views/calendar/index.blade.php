@extends('calendar.layout')

@section('header')
    Calendar
@endsection

@section('content')
    <div class="mb-4">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    New Appointment
                </a>
                <a href="{{ route('calendar-settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                    Calendar Settings
                </a>
                <a href="{{ route('calendar-events.export-form') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                    <i class="fas fa-file-export mr-1"></i> Export Calendar
                </a>
            </div>
            <div>
                <form action="{{ route('calendar') }}" method="GET" class="flex items-center">
                    <label for="user_id" class="mr-2">Filter by User:</label>
                    <select name="user_id" id="user_id" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="ml-2 inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Filter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="flex justify-between items-center">
            <div>
                <button id="month-view" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Month
                </button>
                <button id="week-view" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                    Week
                </button>
                <button id="day-view" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                    Day
                </button>
            </div>
            <div>
                <button id="prev" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Previous
                </button>
                <button id="today" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                    Today
                </button>
                <button id="next" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                    Next
                </button>
            </div>
        </div>
    </div>

    <div id="calendar" class="mt-4"></div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false, // We'll use our custom header
            events: {
                url: '{{ route('calendar.appointments.json') }}',
                method: 'GET',
                extraParams: {
                    user_id: '{{ request('user_id') }}'
                }
            },
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            eventClick: function(info) {
                window.location.href = info.event.url;
            },
            dayMaxEvents: true, // allow "more" link when too many events
            height: 'auto'
        });
        
        calendar.render();
        
        // Custom header buttons
        document.getElementById('month-view').addEventListener('click', function() {
            calendar.changeView('dayGridMonth');
            updateViewButtons('month-view');
        });
        
        document.getElementById('week-view').addEventListener('click', function() {
            calendar.changeView('timeGridWeek');
            updateViewButtons('week-view');
        });
        
        document.getElementById('day-view').addEventListener('click', function() {
            calendar.changeView('timeGridDay');
            updateViewButtons('day-view');
        });
        
        document.getElementById('prev').addEventListener('click', function() {
            calendar.prev();
        });
        
        document.getElementById('today').addEventListener('click', function() {
            calendar.today();
        });
        
        document.getElementById('next').addEventListener('click', function() {
            calendar.next();
        });
        
        function updateViewButtons(activeButton) {
            // Reset all buttons to gray
            document.getElementById('month-view').classList.remove('bg-blue-600', 'hover:bg-blue-700', 'active:bg-blue-800');
            document.getElementById('month-view').classList.add('bg-gray-600', 'hover:bg-gray-700', 'active:bg-gray-800');
            
            document.getElementById('week-view').classList.remove('bg-blue-600', 'hover:bg-blue-700', 'active:bg-blue-800');
            document.getElementById('week-view').classList.add('bg-gray-600', 'hover:bg-gray-700', 'active:bg-gray-800');
            
            document.getElementById('day-view').classList.remove('bg-blue-600', 'hover:bg-blue-700', 'active:bg-blue-800');
            document.getElementById('day-view').classList.add('bg-gray-600', 'hover:bg-gray-700', 'active:bg-gray-800');
            
            // Set active button to blue
            document.getElementById(activeButton).classList.remove('bg-gray-600', 'hover:bg-gray-700', 'active:bg-gray-800');
            document.getElementById(activeButton).classList.add('bg-blue-600', 'hover:bg-blue-700', 'active:bg-blue-800');
        }
    });
</script>
@endsection
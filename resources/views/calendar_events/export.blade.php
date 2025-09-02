@extends('calendar.layout')

@section('header')
    Export Calendar Events
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('calendar-events.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Calendar Events
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Export Calendar Events to iCalendar</h2>
        
        <form method="GET" action="{{ route('calendar-events.export') }}">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', now()->addMonths(1)->format('Y-m-d')) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>

                <div>
                    <label for="calendar_setting_id" class="block text-sm font-medium text-gray-700">Calendar</label>
                    <select name="calendar_setting_id" id="calendar_setting_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Calendars</option>
                        @foreach($calendarSettings as $setting)
                            <option value="{{ $setting->id }}" {{ old('calendar_setting_id') == $setting->id ? 'selected' : '' }}>
                                {{ $setting->name }} ({{ ucfirst($setting->provider) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="related_type" class="block text-sm font-medium text-gray-700">Event Type</label>
                    <select name="related_type" id="related_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Types</option>
                        <option value="appointment" {{ old('related_type') == 'appointment' ? 'selected' : '' }}>Appointments</option>
                        <option value="task" {{ old('related_type') == 'task' ? 'selected' : '' }}>Tasks</option>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Export to iCalendar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-white shadow-md rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">About iCalendar Export</h3>
        <p class="text-gray-600 mb-4">
            The iCalendar format (.ics) is a standard calendar format that can be imported into most calendar applications, including:
        </p>
        <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
            <li>Google Calendar</li>
            <li>Microsoft Outlook</li>
            <li>Apple Calendar</li>
            <li>Mozilla Thunderbird</li>
            <li>And many other calendar applications</li>
        </ul>
        <p class="text-gray-600">
            After exporting, you can import the .ics file into your preferred calendar application to view your CRM events alongside your personal events.
        </p>
    </div>
@endsection
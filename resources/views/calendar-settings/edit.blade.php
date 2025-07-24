@extends('calendar.layout')

@section('header')
    Edit Calendar Setting
@endsection

@section('content')
    <div class="mb-4">
        <a href="{{ route('calendar-settings.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Calendar Settings
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" action="{{ route('calendar-settings.update', $calendarSetting) }}">
            @csrf
            @method('PUT')
            @include('calendar-settings.form')
        </form>
    </div>

    @if($calendarSetting->provider == 'google')
        <div class="mt-6 bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Google Calendar Connection</h3>
            
            @if($calendarSetting->access_token)
                <div class="mb-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Connected
                    </span>
                    <p class="mt-2 text-sm text-gray-600">
                        Last token refresh: {{ $calendarSetting->updated_at->format('F j, Y g:i A') }}
                    </p>
                </div>
                
                <div class="flex space-x-4">
                    <form method="POST" action="{{ route('google-calendar.refresh-token', $calendarSetting) }}">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Refresh Token
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('google-calendar.disconnect', $calendarSetting) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to disconnect from Google Calendar?')">
                            Disconnect
                        </button>
                    </form>
                </div>
            @else
                <p class="mb-4 text-gray-600">
                    This calendar setting is not currently connected to Google Calendar.
                </p>
                <a href="{{ route('google-calendar.connect', ['calendar_setting_id' => $calendarSetting->id]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0C5.372 0 0 5.373 0 12s5.372 12 12 12c6.627 0 12-5.373 12-12S18.627 0 12 0zm.14 19.018c-3.868 0-7-3.14-7-7.018 0-3.878 3.132-7.018 7-7.018 1.89 0 3.47.697 4.682 1.829l-1.974 1.978v-.004c-.735-.702-1.667-1.062-2.708-1.062-2.31 0-4.187 1.956-4.187 4.273 0 2.315 1.877 4.277 4.187 4.277 2.096 0 3.522-1.202 3.816-2.852H12.14v-2.737h6.585c.088.47.135.96.135 1.474 0 4.01-2.677 6.86-6.72 6.86z"/>
                    </svg>
                    Connect with Google
                </a>
            @endif
        </div>
    @endif

    @if($calendarSetting->provider == 'outlook')
        <div class="mt-6 bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Microsoft Outlook Connection</h3>
            
            @if($calendarSetting->access_token)
                <div class="mb-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Connected
                    </span>
                    <p class="mt-2 text-sm text-gray-600">
                        Last token refresh: {{ $calendarSetting->updated_at->format('F j, Y g:i A') }}
                    </p>
                </div>
                
                <div class="flex space-x-4">
                    <form method="POST" action="{{ route('outlook-calendar.refresh-token', $calendarSetting) }}">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Refresh Token
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('outlook-calendar.disconnect', $calendarSetting) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to disconnect from Microsoft Outlook?')">
                            Disconnect
                        </button>
                    </form>
                </div>
            @else
                <p class="mb-4 text-gray-600">
                    This calendar setting is not currently connected to Microsoft Outlook.
                </p>
                <a href="{{ route('outlook-calendar.connect', ['calendar_setting_id' => $calendarSetting->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.18 0H12v6.16L16.5 9.5l4.5-2.47V0h.18zM12 7.59v6.2L16.5 17l4.5-3.21V7.59L16.5 10.5 12 7.59zM2.6 2.77v18.46L12 24v-6.42L6.75 14.4v-4.8L12 6.42V0L2.6 2.77z"/>
                    </svg>
                    Connect with Microsoft
                </a>
            @endif
        </div>
    @endif
@endsection
<div class="space-y-6">
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Validation Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Calendar Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $calendarSetting->name ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>

    <div>
        <label for="provider" class="block text-sm font-medium text-gray-700">Calendar Provider</label>
        <select name="provider" id="provider" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="local" {{ (old('provider', $calendarSetting->provider ?? '') == 'local') ? 'selected' : '' }}>Local (CRM Only)</option>
            <option value="google" {{ (old('provider', $calendarSetting->provider ?? '') == 'google') ? 'selected' : '' }}>Google Calendar</option>
            <option value="outlook" {{ (old('provider', $calendarSetting->provider ?? '') == 'outlook') ? 'selected' : '' }}>Microsoft Outlook</option>
        </select>
    </div>

    <div id="google-settings" class="{{ (old('provider', $calendarSetting->provider ?? '') != 'google') ? 'hidden' : '' }}">
        <div>
            <label for="calendar_id" class="block text-sm font-medium text-gray-700">Google Calendar ID</label>
            <input type="text" name="calendar_id" id="calendar_id" value="{{ old('calendar_id', $calendarSetting->calendar_id ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            <p class="mt-1 text-sm text-gray-500">
                This is usually your Gmail address or a specific calendar ID from Google Calendar.
            </p>
        </div>

        <div>
            <label for="access_token" class="block text-sm font-medium text-gray-700">Access Token</label>
            <input type="password" name="access_token" id="access_token" value="{{ old('access_token', $calendarSetting->access_token ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>

        <div>
            <label for="refresh_token" class="block text-sm font-medium text-gray-700">Refresh Token</label>
            <input type="password" name="refresh_token" id="refresh_token" value="{{ old('refresh_token', $calendarSetting->refresh_token ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>

        <div>
            <label for="token_expires_at" class="block text-sm font-medium text-gray-700">Token Expiry Date</label>
            <input type="datetime-local" name="token_expires_at" id="token_expires_at" value="{{ old('token_expires_at', $calendarSetting->token_expires_at ? $calendarSetting->token_expires_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
    </div>

    <div id="outlook-settings" class="{{ (old('provider', $calendarSetting->provider ?? '') != 'outlook') ? 'hidden' : '' }}">
        <div>
            <label for="outlook_calendar_id" class="block text-sm font-medium text-gray-700">Outlook Calendar ID</label>
            <input type="text" name="outlook_calendar_id" id="outlook_calendar_id" value="{{ old('outlook_calendar_id', $calendarSetting->calendar_id ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>

        <div>
            <label for="outlook_access_token" class="block text-sm font-medium text-gray-700">Access Token</label>
            <input type="password" name="outlook_access_token" id="outlook_access_token" value="{{ old('outlook_access_token', $calendarSetting->access_token ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>

        <div>
            <label for="outlook_refresh_token" class="block text-sm font-medium text-gray-700">Refresh Token</label>
            <input type="password" name="outlook_refresh_token" id="outlook_refresh_token" value="{{ old('outlook_refresh_token', $calendarSetting->refresh_token ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>

        <div>
            <label for="outlook_token_expires_at" class="block text-sm font-medium text-gray-700">Token Expiry Date</label>
            <input type="datetime-local" name="outlook_token_expires_at" id="outlook_token_expires_at" value="{{ old('outlook_token_expires_at', $calendarSetting->token_expires_at ? $calendarSetting->token_expires_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
    </div>

    <div>
        <label for="user_id" class="block text-sm font-medium text-gray-700">User (Optional)</label>
        <select name="user_id" id="user_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="">Company-wide Calendar</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ (old('user_id', $calendarSetting->user_id ?? '') == $user->id) ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-sm text-gray-500">
            If no user is selected, this will be a company-wide calendar setting.
        </p>
    </div>

    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $calendarSetting->is_active ?? true) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="is_active" class="font-medium text-gray-700">Active</label>
            <p class="text-gray-500">Enable or disable this calendar integration.</p>
        </div>
    </div>

    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="is_default" name="is_default" type="checkbox" value="1" {{ old('is_default', $calendarSetting->is_default ?? false) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="is_default" class="font-medium text-gray-700">Default Calendar</label>
            <p class="text-gray-500">Set as the default calendar for new appointments and events.</p>
        </div>
    </div>

    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="sync_events" name="sync_events" type="checkbox" value="1" {{ old('sync_events', $calendarSetting->sync_events ?? true) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="sync_events" class="font-medium text-gray-700">Sync Events</label>
            <p class="text-gray-500">Automatically sync events between this CRM and the external calendar.</p>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ isset($calendarSetting) ? 'Update Calendar Setting' : 'Create Calendar Setting' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const providerSelect = document.getElementById('provider');
        const googleSettings = document.getElementById('google-settings');
        const outlookSettings = document.getElementById('outlook-settings');

        function toggleProviderSettings() {
            const selectedProvider = providerSelect.value;
            
            if (selectedProvider === 'google') {
                googleSettings.classList.remove('hidden');
                outlookSettings.classList.add('hidden');
            } else if (selectedProvider === 'outlook') {
                googleSettings.classList.add('hidden');
                outlookSettings.classList.remove('hidden');
            } else {
                googleSettings.classList.add('hidden');
                outlookSettings.classList.add('hidden');
            }
        }

        providerSelect.addEventListener('change', toggleProviderSettings);
        toggleProviderSettings();
    });
</script>
@endpush
<div class="space-y-6">
    <div>
        <x-label for="title" :value="__('Title')" />
        <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $appointment->title ?? '')" required autofocus />
        @error('title')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <x-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $appointment->description ?? '') }}</textarea>
        @error('description')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <x-label for="location" :value="__('Location')" />
        <x-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $appointment->location ?? '')" />
        @error('location')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-label for="start_datetime" :value="__('Start Date and Time')" />
            <x-input id="start_datetime" class="block mt-1 w-full" type="datetime-local" name="start_datetime" :value="old('start_datetime', isset($appointment) ? $appointment->start_datetime->format('Y-m-d\TH:i') : '')" required />
            @error('start_datetime')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-label for="end_datetime" :value="__('End Date and Time')" />
            <x-input id="end_datetime" class="block mt-1 w-full" type="datetime-local" name="end_datetime" :value="old('end_datetime', isset($appointment) ? $appointment->end_datetime->format('Y-m-d\TH:i') : '')" required />
            @error('end_datetime')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center">
        <input id="all_day" type="checkbox" name="all_day" value="1" {{ old('all_day', isset($appointment) && $appointment->all_day ? 'checked' : '') }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <label for="all_day" class="ml-2 block text-sm text-gray-900">All Day Event</label>
        @error('all_day')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <x-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @foreach(['scheduled', 'completed', 'cancelled', 'rescheduled'] as $status)
                <option value="{{ $status }}" {{ old('status', $appointment->status ?? 'scheduled') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <x-label :value="__('Participants')" />
        <div class="mt-2 space-y-4">
            <div>
                <x-label for="user_participants" :value="__('CRM Users')" />
                <select id="user_participants" name="user_participants[]" multiple class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($users as $user)
                        <option value="{{ $user->user_id }}" {{ in_array($user->user_id, old('user_participants', $userParticipantIds ?? [])) ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_participants')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="customer_participants" :value="__('Customers')" />
                <select id="customer_participants" name="customer_participants[]" multiple class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}" {{ in_array($customer->customer_id, old('customer_participants', $customerParticipantIds ?? [])) ? 'selected' : '' }}>
                            {{ $customer->first_name }} {{ $customer->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('customer_participants')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="contact_participants" :value="__('Contacts')" />
                <select id="contact_participants" name="contact_participants[]" multiple class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($contacts as $contact)
                        <option value="{{ $contact->contact_id }}" {{ in_array($contact->contact_id, old('contact_participants', $contactParticipantIds ?? [])) ? 'selected' : '' }}>
                            {{ $contact->first_name }} {{ $contact->last_name }} ({{ $contact->contactable_type }})
                        </option>
                    @endforeach
                </select>
                @error('contact_participants')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center">
        <input id="sync_to_google_calendar" type="checkbox" name="sync_to_google_calendar" value="1" {{ old('sync_to_google_calendar', isset($appointment) && $appointment->calendarEvent ? 'checked' : '') }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <label for="sync_to_google_calendar" class="ml-2 block text-sm text-gray-900">Sync to Google Calendar</label>
    </div>

    <div id="google_calendar_section" class="{{ old('sync_to_google_calendar', isset($appointment) && $appointment->calendarEvent ? '' : 'hidden') }}">
        <x-label for="google_calendar_id" :value="__('Google Calendar')" />
        <select id="google_calendar_id" name="google_calendar_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @foreach($calendarSettings as $calendarSetting)
                <option value="{{ $calendarSetting->google_calendar_id }}" {{ old('google_calendar_id', isset($appointment) && $appointment->calendarEvent ? $appointment->calendarEvent->google_calendar_id : '') == $calendarSetting->google_calendar_id ? 'selected' : '' }}>
                    {{ $calendarSetting->user_id ? $calendarSetting->user->name . "'s Calendar" : 'Company Calendar' }}
                    {{ $calendarSetting->is_primary ? '(Primary)' : '' }}
                </option>
            @endforeach
        </select>
        @error('google_calendar_id')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center justify-end mt-4">
        <x-button class="ml-3">
            {{ isset($appointment) ? __('Update Appointment') : __('Create Appointment') }}
        </x-button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const syncCheckbox = document.getElementById('sync_to_google_calendar');
        const googleCalendarSection = document.getElementById('google_calendar_section');
        
        syncCheckbox.addEventListener('change', function() {
            if (this.checked) {
                googleCalendarSection.classList.remove('hidden');
            } else {
                googleCalendarSection.classList.add('hidden');
            }
        });
    });
</script>
@endpush
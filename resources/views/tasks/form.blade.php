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
        <label for="title" class="block text-sm font-medium text-gray-700">Task Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $task->title ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description', $task->description ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="pending" {{ (old('status', $task->status ?? '') == 'pending') ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ (old('status', $task->status ?? '') == 'in_progress') ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ (old('status', $task->status ?? '') == 'completed') ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ (old('status', $task->status ?? '') == 'cancelled') ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
            <select name="priority" id="priority" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="low" {{ (old('priority', $task->priority ?? '') == 'low') ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ (old('priority', $task->priority ?? '') == 'medium') ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ (old('priority', $task->priority ?? '') == 'high') ? 'selected' : '' }}>High</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', isset($task) && $task->start_date ? $task->start_date->format('Y-m-d\TH:i') : '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>

        <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
            <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
    </div>

    <div>
        <label for="assigned_to_id" class="block text-sm font-medium text-gray-700">Assigned To</label>
        <select name="assigned_to_id" id="assigned_to_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="">Unassigned</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ (old('assigned_to_id', $task->assigned_to_id ?? '') == $user->id) ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="related_to_type" class="block text-sm font-medium text-gray-700">Related To</label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <select name="related_to_type" id="related_to_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Not Related</option>
                    <option value="App\Models\Customer" {{ (old('related_to_type', $task->related_to_type ?? '') == 'App\Models\Customer') ? 'selected' : '' }}>Customer</option>
                    <option value="App\Models\Lead" {{ (old('related_to_type', $task->related_to_type ?? '') == 'App\Models\Lead') ? 'selected' : '' }}>Lead</option>
                    <option value="App\Models\Opportunity" {{ (old('related_to_type', $task->related_to_type ?? '') == 'App\Models\Opportunity') ? 'selected' : '' }}>Opportunity</option>
                    <option value="App\Models\Project" {{ (old('related_to_type', $task->related_to_type ?? '') == 'App\Models\Project') ? 'selected' : '' }}>Project</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <input type="text" name="related_to_id" id="related_to_id" value="{{ old('related_to_id', $task->related_to_id ?? '') }}" placeholder="ID of related entity" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
        </div>
        <p class="mt-1 text-sm text-gray-500">
            Optionally link this task to another entity in the system.
        </p>
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" id="notes" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('notes', $task->notes ?? '') }}</textarea>
    </div>

    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="add_to_calendar" name="add_to_calendar" type="checkbox" value="1" {{ old('add_to_calendar', isset($task) && $task->calendarEvent ? true : false) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="add_to_calendar" class="font-medium text-gray-700">Add to Calendar</label>
            <p class="text-gray-500">Show this task on the calendar.</p>
        </div>
    </div>

    <div id="calendar-settings" class="{{ old('add_to_calendar', isset($task) && $task->calendarEvent ? true : false) ? '' : 'hidden' }}">
        <div>
            <label for="calendar_setting_id" class="block text-sm font-medium text-gray-700">Calendar</label>
            <select name="calendar_setting_id" id="calendar_setting_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">Default Calendar</option>
                @foreach($calendarSettings as $setting)
                    <option value="{{ $setting->id }}" {{ (old('calendar_setting_id', isset($task) && $task->calendarEvent ? $task->calendarEvent->calendar_setting_id : '') == $setting->id) ? 'selected' : '' }}>
                        {{ $setting->name }} ({{ ucfirst($setting->provider) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-start mt-4">
            <div class="flex items-center h-5">
                <input id="sync_with_google" name="sync_with_google" type="checkbox" value="1" {{ old('sync_with_google', isset($task) && $task->calendarEvent && $task->calendarEvent->google_event_id ? true : false) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
                <label for="sync_with_google" class="font-medium text-gray-700">Sync with Google Calendar</label>
                <p class="text-gray-500">Sync this task with Google Calendar (if a Google Calendar is selected).</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ isset($task) ? 'Update Task' : 'Create Task' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addToCalendarCheckbox = document.getElementById('add_to_calendar');
        const calendarSettings = document.getElementById('calendar-settings');

        function toggleCalendarSettings() {
            if (addToCalendarCheckbox.checked) {
                calendarSettings.classList.remove('hidden');
            } else {
                calendarSettings.classList.add('hidden');
            }
        }

        addToCalendarCheckbox.addEventListener('change', toggleCalendarSettings);
        toggleCalendarSettings();
    });
</script>
@endpush
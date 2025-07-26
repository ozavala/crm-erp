# Calendar Interface Implementation

This document outlines the steps taken to implement the calendar user interface and its associated backend API, including integration with `Spatie/Laravel-Google-Calendar` and multi-tenant authorization.

## Backend Implementation

1.  **`app/Http/Controllers/CalendarEventController.php`**: This controller was significantly modified to integrate with the `GoogleCalendarService` and enforce authorization. Key changes include:
    *   **Dependency Injection**: The `GoogleCalendarService` is injected into the controller's constructor.
    *   **`index` method**: Fetches events from Google Calendar using `GoogleCalendarService::importEventsFromGoogle` to ensure local data is up-to-date, and then retrieves events from the local database within the requested date range. Includes `authorize('viewAny', CalendarEvent::class)`.
    *   **`store` method**: Creates a new `Appointment` record (as `GoogleCalendarService` expects a related model) and then a `CalendarEvent` record. It then calls `GoogleCalendarService::syncAppointmentToGoogle` to push the new event to Google Calendar. Includes `authorize('create', CalendarEvent::class)`.
    *   **`show` method**: Includes `authorize('view', $calendarEvent)` to ensure the user has permission to view the specific event.
    *   **`update` method**: Updates the local `CalendarEvent` and its related `Appointment`. It then marks the `CalendarEvent` as pending and calls `GoogleCalendarService::syncAppointmentToGoogle` to synchronize changes with Google Calendar. Includes `authorize('update', $calendarEvent)`.
    *   **`destroy` method**: Calls `GoogleCalendarService::deleteFromGoogle` to remove the event from Google Calendar, then deletes the related `Appointment` and the local `CalendarEvent` record. Includes `authorize('delete', $calendarEvent)`.

2.  **`routes/api.php`**: (No further changes were made in this step, as the previous step already added the resource route for `calendar-events` and the import for `CalendarEventController`.)

3.  **`app/Models/CalendarEvent.php`**: (No further changes were made in this step, as the previous steps already updated the `$fillable` and `$casts` properties and confirmed the `related` polymorphic relationship.)

4.  **`app/Policies/CalendarEventPolicy.php`**: A new policy was created to define authorization logic for `CalendarEvent`s. This policy ensures:
    *   Users can only view, create, update, or delete calendar events within their `owner_company_id` scope.
    *   For events related to `Appointment`s, users who are participants of the appointment also have access.
    *   The policy defines `viewAny`, `view`, `create`, `update`, and `delete` methods.

5.  **`app/Providers/AuthServiceProvider.php`**: The `CalendarEventPolicy` was registered in the `$policies` array to enable automatic policy discovery and enforcement.

## Frontend Implementation

1.  **`resources/views/calendar/index.blade.php`**: (No further changes were made in this step.)

2.  **`public/js/calendar.js`**: This JavaScript file was updated to align with the new API and Google Calendar integration:
    *   **Event Fetching**: The `events` function now passes `start` and `end` parameters to the API for filtering, allowing the backend to import events from Google Calendar within the visible range.
    *   **Data Formatting**: Ensures that `start` and `end` dates are correctly formatted for API requests and that `google_event_id` is included in the event data.
    *   **Event Creation/Update**: The `dateClick` and `eventClick` functions now send `start` and `end` dates in the correct format to the API.

3.  **`routes/web.php`**: (No further changes were made in this step.)

## Usage

To access the calendar interface, navigate to `/calendar` in your web browser (e.g., `http://your-app-url/calendar`).

**Authentication Note:** The API calls from `public/js/calendar.js` to `/api/calendar-events` require authentication. You will need to uncomment and provide a valid authentication token (e.g., a Bearer token) in the `headers` of the `fetch` requests within `public/js/calendar.js` for the API interactions (fetching, creating, updating, deleting events) to work correctly. For example, you'll need to uncomment and replace `YOUR_AUTH_TOKEN` with an actual token in lines like:

```javascript
// 'Authorization': 'Bearer ' + YOUR_AUTH_TOKEN
```

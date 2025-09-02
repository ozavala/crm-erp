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

# Kanban Interface Implementation

This section details the implementation of the Kanban-style interface for the application's main navigation and administration, leveraging Livewire for dynamic interactions.

## Backend Implementation

1.  **Livewire Component (`app/Livewire/KanbanBoard.php`)**:
    *   A new Livewire component, `KanbanBoard`, was created to manage the state and logic of the Kanban board.
    *   It includes properties for `allColumns` (the full dataset of columns and cards), `columns` (the filtered columns for display), `search` (for filtering cards), and `wipLimitReached` (to indicate if a WIP limit has been hit).
    *   The `mount()` method initializes `allColumns` with sample data, including `id`, `title`, `wipLimit`, `cards` (each with `id`, `title`, `icon`, `description`, `notifications`, `shortcut_url`, and `category`).
    *   The `updatedSearch()` method triggers `applySearch()` whenever the `search` property changes, enabling real-time filtering.
    *   The `applySearch()` method filters cards based on the `search` term, matching against card `title` and `description`.
    *   The `onCardDrop()` method handles drag-and-drop events, updating the card's position within `allColumns`. It also includes logic to check and enforce `wipLimit` for the target column, dispatching a `wip-limit-reached` event if the limit is exceeded.
    *   Placeholder `editCard()` and `viewCard()` methods were added to demonstrate future functionality for card actions.

## Frontend Implementation

1.  **Livewire View (`resources/views/livewire/kanban-board.blade.php`)**:
    *   This Blade file renders the Kanban board using the `KanbanBoard` Livewire component.
    *   It includes a search input field (`wire:model.live="search"`).
    *   The columns and cards are rendered dynamically using `@foreach` loops.
    *   Livewire's `wire:sortable` and `wire:sortable-group` directives are used to enable drag-and-drop functionality for cards between and within columns.
    *   Each card displays its `icon`, `title`, `description`, and `notifications` count.
    *   A "Go" button (`shortcut-button`) is provided for direct navigation.
    *   "Edit" and "View" buttons (`quick-action-button`) are included with `wire:click` directives to call the respective Livewire methods.
    *   A `wip-limit-warning` message is displayed conditionally if `wipLimitReached` is true.
    *   Columns exceeding their WIP limit are given the `wip-limit-exceeded` class.
    *   Cards are assigned a CSS class based on their `category` property (e.g., `critical`, `frequent`, `settings`) for color-coding.

2.  **Kanban Styles (`resources/css/kanban.css`)**:
    *   This CSS file provides the styling for the Kanban board, columns, and cards.
    *   It includes styles for the card header, title, notifications, body, description, and footer.
    *   Styles for the `shortcut-button` and `quick-action-button` are defined.
    *   Responsive adjustments are included using media queries to ensure the layout adapts to different screen sizes (e.g., columns stack vertically on smaller screens).
    *   Specific styles for `wip-limit-warning` and `wip-limit-exceeded` classes are added for visual feedback on WIP limits.
    *   CSS rules for card categories (`.kanban-card.critical`, `.kanban-card.frequent`, `.kanban-card.settings`) are defined to apply different `border-left` colors.
    *   Styles for drag-and-drop visual feedback (`.kanban-card.sortable-chosen`, `.kanban-card.sortable-ghost`) are included.

3.  **Kanban JavaScript (`resources/js/kanban.js`)**:
    *   This file now primarily contains Livewire hooks (`livewire:init`, `morph.removed`, `morph.added`) for potential future JavaScript interactions related to Livewire component lifecycle events. Livewire 3 handles drag-and-drop animations automatically via Sortable.js, so explicit DOM manipulation for sorting is no longer needed here.

4.  **Web Routes (`routes/web.php`)**:
    *   A new route `/kanban` was added to display the Kanban board, rendering the `livewire.kanban.index` view. This route is protected by the `auth` middleware.

5.  **Blade View (`resources/views/livewire/kanban/index.blade.php`)**:
    *   This view extends the main application layout (`layouts.app`).
    *   It includes the `@livewire('kanban-board')` directive to render the Livewire component.
    *   The `@vite('resources/js/kanban.js')` directive is used to include the Kanban JavaScript file.

6.  **Main JavaScript (`resources/js/app.js`)**:
    *   The `resources/css/kanban.css` file is imported into `resources/js/app.js` to ensure Vite processes and bundles the CSS correctly.

## Usage

To access the Kanban interface, navigate to `/kanban` in your web browser (e.g., `http://your-app-url/kanban`). Ensure you are authenticated to access this route.
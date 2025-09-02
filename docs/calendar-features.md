# Calendar Features Documentation

## Overview

The calendar functionality in the CRM/ERP system provides a comprehensive solution for managing appointments, tasks, and events. It includes integration with Google Calendar, notifications for upcoming appointments, and export capabilities.

## Key Features

1. **Calendar View**: A visual calendar interface using FullCalendar.js
2. **Appointments Management**: Create, edit, and delete appointments
3. **Task Integration**: Tasks with due dates appear on the calendar
4. **Google Calendar Integration**: Sync events with Google Calendar
5. **Notifications**: Reminders for upcoming appointments
6. **Export Functionality**: Export calendar events to iCalendar format
7. **Multi-tenancy Support**: Calendar events are scoped to owner companies

## Data Model

### Core Entities

1. **Appointment**
   - Represents a scheduled meeting or event
   - Has participants (polymorphic relationship to users, customers, etc.)
   - Can be synced with Google Calendar

2. **Task**
   - Represents a to-do item with a due date
   - Can be assigned to users
   - Can be displayed on the calendar

3. **CalendarEvent**
   - Links appointments and tasks to external calendars
   - Tracks synchronization status with external calendars

4. **CalendarSetting**
   - Stores configuration for calendar integrations
   - Supports multiple calendar providers (currently Google Calendar)

5. **AppointmentParticipant**
   - Tracks participants for appointments
   - Uses polymorphic relationships to support different participant types

6. **AppointmentReminder**
   - Tracks which reminders have been sent for appointments
   - Prevents duplicate reminders

## User Interface

### Calendar Views

1. **Month View**: Default view showing a monthly calendar
2. **Week View**: Detailed view of a specific week
3. **Day View**: Detailed view of a specific day

### Appointment Management

1. **Create Appointment**: Form to create a new appointment with participants
2. **Edit Appointment**: Form to modify an existing appointment
3. **View Appointment**: Detailed view of an appointment
4. **Delete Appointment**: Remove an appointment

### Task Management

1. **Create Task**: Form to create a new task with calendar integration
2. **Edit Task**: Form to modify an existing task
3. **View Task**: Detailed view of a task
4. **Delete Task**: Remove a task

### Calendar Settings

1. **Calendar Configuration**: Manage calendar settings
2. **Google Calendar Integration**: Connect to Google Calendar
3. **Default Calendar**: Set default calendar for new events

## Google Calendar Integration

### Setup

1. Configure Google Calendar API credentials
2. Connect user's Google Calendar account
3. Select which calendars to sync

### Synchronization

1. **Two-way sync**: Changes in either system are reflected in the other
2. **Manual sync**: Force synchronization of events
3. **Automatic sync**: Events are automatically synced on creation/update

## Notifications

### Appointment Reminders

1. **Email Notifications**: Sent to appointment participants
2. **In-app Notifications**: Displayed in the notification center
3. **Configurable Timing**: Reminders at 24 hours, 1 hour, and 15 minutes before appointments

### Notification Management

1. **View Notifications**: See all notifications in the notification center
2. **Mark as Read**: Mark notifications as read
3. **Clear All**: Mark all notifications as read

## Export Functionality

### iCalendar Export

1. **Filter Events**: Select which events to export
2. **Date Range**: Specify the date range for export
3. **Calendar Selection**: Choose which calendars to export from

### Import to External Calendars

Instructions for importing the exported iCalendar file into:
1. Google Calendar
2. Microsoft Outlook
3. Apple Calendar
4. Other calendar applications

## Multi-tenancy Support

All calendar entities include an `owner_company_id` field to ensure data isolation between different companies using the system.

## Technical Implementation

### Controllers

1. **AppointmentController**: Manages appointments
2. **CalendarEventController**: Handles calendar events and export
3. **CalendarSettingController**: Manages calendar settings
4. **NotificationController**: Handles notifications

### Services

1. **GoogleCalendarService**: Handles integration with Google Calendar API

### Commands

1. **SendAppointmentReminders**: Scheduled command to send reminders for upcoming appointments

### Notifications

1. **AppointmentReminder**: Notification class for appointment reminders

## Best Practices

1. **Calendar Organization**: Keep separate calendars for different purposes
2. **Regular Sync**: Ensure calendars are regularly synchronized
3. **Proper Categorization**: Use appropriate event types for different activities
4. **Notification Management**: Keep notifications under control by marking them as read

## Troubleshooting

### Common Issues

1. **Sync Failures**: Check Google Calendar API credentials and permissions
2. **Missing Events**: Verify event visibility settings
3. **Notification Issues**: Check email configuration and notification settings

### Support

For additional support, contact the system administrator or refer to the API documentation for more technical details.
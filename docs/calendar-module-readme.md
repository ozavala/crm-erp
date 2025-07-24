# Calendar Module

## Quick Start Guide

This document provides a quick overview of how to set up and use the calendar module in the CRM/ERP system.

## Setup

### Prerequisites

1. PHP 8.1 or higher
2. Laravel 11
3. MySQL/PostgreSQL database
4. Google API credentials (for Google Calendar integration)

### Installation

The calendar module is integrated into the main CRM/ERP application. No separate installation is required.

### Configuration

#### Google Calendar Integration

1. Create a project in the [Google Cloud Console](https://console.cloud.google.com/)
2. Enable the Google Calendar API
3. Create OAuth 2.0 credentials
4. Add the credentials to your `.env` file:

```
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=your-app-url/google-calendar/callback
```

## Usage

### Accessing the Calendar

1. Log in to the CRM/ERP system
2. Navigate to the Calendar section from the main menu

### Managing Appointments

#### Creating an Appointment

1. Click "New Appointment" on the calendar page
2. Fill in the appointment details:
   - Title
   - Description
   - Start date and time
   - End date and time
   - Location (optional)
   - Participants
   - Priority
3. Click "Create Appointment"

#### Editing an Appointment

1. Click on an appointment in the calendar
2. Click "Edit" button
3. Modify the appointment details
4. Click "Update Appointment"

#### Deleting an Appointment

1. Click on an appointment in the calendar
2. Click "Delete" button
3. Confirm deletion

### Managing Tasks with Calendar Integration

#### Creating a Task with Calendar Integration

1. Navigate to the Tasks section
2. Click "Create New Task"
3. Fill in the task details
4. Check "Add to Calendar"
5. Select the calendar
6. Click "Create Task"

#### Viewing Tasks in the Calendar

Tasks with due dates will appear in the calendar with a different color to distinguish them from appointments.

### Calendar Settings

#### Connecting to Google Calendar

1. Navigate to Calendar Settings
2. Click "Connect with Google"
3. Follow the authentication process
4. Select which calendars to sync

#### Managing Calendar Settings

1. Navigate to Calendar Settings
2. Add, edit, or remove calendar connections
3. Set default calendars
4. Configure sync settings

### Exporting Calendar Events

#### Exporting to iCalendar Format

1. Click "Export Calendar" on the calendar page
2. Select the date range
3. Choose which calendars to include
4. Select event types (appointments, tasks, or both)
5. Click "Export to iCalendar"
6. Save the .ics file

#### Importing to External Calendars

The exported .ics file can be imported into:

1. **Google Calendar**:
   - Open Google Calendar
   - Click the "+" next to "Other calendars"
   - Select "Import"
   - Upload the .ics file

2. **Microsoft Outlook**:
   - Open Outlook
   - Go to Calendar view
   - Click "File" > "Import and Export"
   - Select "Import an iCalendar (.ics) file"
   - Browse to the .ics file

3. **Apple Calendar**:
   - Open Calendar
   - Select "File" > "Import"
   - Select the .ics file

### Notifications

#### Viewing Notifications

1. Click the bell icon in the top navigation bar
2. View all notifications
3. Click on a notification to view the related appointment

#### Managing Notifications

1. Mark individual notifications as read
2. Mark all notifications as read
3. Navigate to the related appointment from the notification

## Troubleshooting

### Common Issues

1. **Google Calendar Sync Issues**
   - Verify API credentials
   - Check that the correct scopes are enabled
   - Ensure the user has granted necessary permissions

2. **Missing Events**
   - Check that the event is within the displayed date range
   - Verify that the correct calendars are selected for display
   - Ensure the event has not been deleted

3. **Notification Problems**
   - Check that the scheduled task for sending reminders is running
   - Verify email configuration
   - Check notification settings

## Additional Resources

- [Detailed Documentation](calendar-features.md)
- [API Documentation](api-documentation.md)
- [Google Calendar API Documentation](https://developers.google.com/calendar)

## Support

For additional support, contact the system administrator or development team.
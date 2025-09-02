# Calendar API Documentation

This document provides detailed information about the Calendar API endpoints available in the CRM/ERP system.

## Authentication

All API requests require authentication. The API uses Laravel Sanctum for authentication.

To authenticate:

1. Obtain an API token by sending a POST request to `/api/login` with your credentials
2. Include the token in the Authorization header of all requests:
   `Authorization: Bearer your-api-token`

## Base URL

All URLs referenced in the documentation have the following base:

```
https://your-crm-erp-domain.com/api
```

## Appointments

### List Appointments

Retrieves a list of appointments.

**Endpoint:** `GET /appointments`

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| page | integer | Page number for pagination |
| per_page | integer | Number of items per page |
| start_date | date | Filter by start date (format: YYYY-MM-DD) |
| end_date | date | Filter by end date (format: YYYY-MM-DD) |
| status | string | Filter by status (scheduled, completed, cancelled, rescheduled) |
| user_id | integer | Filter by assigned user ID |

**Response:**

```json
{
  "data": [
    {
      "appointment_id": 1,
      "owner_company_id": 1,
      "title": "Client Meeting",
      "description": "Discuss project requirements",
      "location": "Office",
      "start_datetime": "2025-08-01T10:00:00Z",
      "end_datetime": "2025-08-01T11:00:00Z",
      "all_day": false,
      "status": "scheduled",
      "google_calendar_event_id": null,
      "created_by_user_id": 1,
      "created_at": "2025-07-24T12:00:00Z",
      "updated_at": "2025-07-24T12:00:00Z"
    }
  ],
  "links": {
    "first": "https://your-crm-erp-domain.com/api/appointments?page=1",
    "last": "https://your-crm-erp-domain.com/api/appointments?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "https://your-crm-erp-domain.com/api/appointments",
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

### Get Appointment

Retrieves a specific appointment by ID.

**Endpoint:** `GET /appointments/{id}`

**Response:**

```json
{
  "data": {
    "appointment_id": 1,
    "owner_company_id": 1,
    "title": "Client Meeting",
    "description": "Discuss project requirements",
    "location": "Office",
    "start_datetime": "2025-08-01T10:00:00Z",
    "end_datetime": "2025-08-01T11:00:00Z",
    "all_day": false,
    "status": "scheduled",
    "google_calendar_event_id": null,
    "created_by_user_id": 1,
    "created_at": "2025-07-24T12:00:00Z",
    "updated_at": "2025-07-24T12:00:00Z",
    "participants": [
      {
        "id": 1,
        "appointment_id": 1,
        "participantable_type": "App\\Models\\CrmUser",
        "participantable_id": 2,
        "status": "accepted",
        "is_organizer": false,
        "participantable": {
          "user_id": 2,
          "full_name": "Jane Doe",
          "email": "jane@example.com"
        }
      }
    ]
  }
}
```

### Create Appointment

Creates a new appointment.

**Endpoint:** `POST /appointments`

**Request Body:**

```json
{
  "title": "Client Meeting",
  "description": "Discuss project requirements",
  "location": "Office",
  "start_datetime": "2025-08-01T10:00:00Z",
  "end_datetime": "2025-08-01T11:00:00Z",
  "all_day": false,
  "status": "scheduled",
  "participants": [
    {
      "participantable_type": "App\\Models\\CrmUser",
      "participantable_id": 2
    }
  ]
}
```

**Response:**

```json
{
  "data": {
    "appointment_id": 1,
    "owner_company_id": 1,
    "title": "Client Meeting",
    "description": "Discuss project requirements",
    "location": "Office",
    "start_datetime": "2025-08-01T10:00:00Z",
    "end_datetime": "2025-08-01T11:00:00Z",
    "all_day": false,
    "status": "scheduled",
    "google_calendar_event_id": null,
    "created_by_user_id": 1,
    "created_at": "2025-07-24T12:00:00Z",
    "updated_at": "2025-07-24T12:00:00Z"
  }
}
```

### Update Appointment

Updates an existing appointment.

**Endpoint:** `PUT /appointments/{id}`

**Request Body:**

```json
{
  "title": "Updated Client Meeting",
  "description": "Discuss project requirements and timeline",
  "location": "Conference Room",
  "start_datetime": "2025-08-01T11:00:00Z",
  "end_datetime": "2025-08-01T12:00:00Z",
  "all_day": false,
  "status": "scheduled"
}
```

**Response:**

```json
{
  "data": {
    "appointment_id": 1,
    "owner_company_id": 1,
    "title": "Updated Client Meeting",
    "description": "Discuss project requirements and timeline",
    "location": "Conference Room",
    "start_datetime": "2025-08-01T11:00:00Z",
    "end_datetime": "2025-08-01T12:00:00Z",
    "all_day": false,
    "status": "scheduled",
    "google_calendar_event_id": null,
    "created_by_user_id": 1,
    "created_at": "2025-07-24T12:00:00Z",
    "updated_at": "2025-07-24T13:00:00Z"
  }
}
```

### Delete Appointment

Deletes an appointment.

**Endpoint:** `DELETE /appointments/{id}`

**Response:**

```json
{
  "message": "Appointment deleted successfully"
}
```

### Update Appointment Status

Updates the status of an appointment.

**Endpoint:** `PATCH /appointments/{id}/status`

**Request Body:**

```json
{
  "status": "completed"
}
```

**Response:**

```json
{
  "data": {
    "appointment_id": 1,
    "status": "completed",
    "updated_at": "2025-07-24T14:00:00Z"
  }
}
```

## Appointment Participants

### List Appointment Participants

Retrieves the participants of an appointment.

**Endpoint:** `GET /appointments/{id}/participants`

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "appointment_id": 1,
      "participantable_type": "App\\Models\\CrmUser",
      "participantable_id": 2,
      "status": "accepted",
      "is_organizer": false,
      "participantable": {
        "user_id": 2,
        "full_name": "Jane Doe",
        "email": "jane@example.com"
      }
    }
  ]
}
```

### Add Participant to Appointment

Adds a participant to an appointment.

**Endpoint:** `POST /appointments/{id}/participants`

**Request Body:**

```json
{
  "participantable_type": "App\\Models\\CrmUser",
  "participantable_id": 3,
  "is_organizer": false
}
```

**Response:**

```json
{
  "data": {
    "id": 2,
    "appointment_id": 1,
    "participantable_type": "App\\Models\\CrmUser",
    "participantable_id": 3,
    "status": "pending",
    "is_organizer": false,
    "created_at": "2025-07-24T15:00:00Z",
    "updated_at": "2025-07-24T15:00:00Z"
  }
}
```

### Update Participant Status

Updates the status of a participant.

**Endpoint:** `PATCH /appointment-participants/{id}/status`

**Request Body:**

```json
{
  "status": "accepted"
}
```

**Response:**

```json
{
  "data": {
    "id": 2,
    "status": "accepted",
    "updated_at": "2025-07-24T16:00:00Z"
  }
}
```

### Remove Participant from Appointment

Removes a participant from an appointment.

**Endpoint:** `DELETE /appointment-participants/{id}`

**Response:**

```json
{
  "message": "Participant removed successfully"
}
```

## Calendar Events

### List Calendar Events

Retrieves a list of calendar events.

**Endpoint:** `GET /calendar-events`

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| page | integer | Page number for pagination |
| per_page | integer | Number of items per page |
| calendar_id | string | Filter by Google Calendar ID |
| related_type | string | Filter by related type (appointment, task) |
| sync_status | string | Filter by sync status (pending, synced, failed) |

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "owner_company_id": 1,
      "google_calendar_id": "primary",
      "google_event_id": "event123",
      "related_type": "appointment",
      "related_id": 1,
      "sync_status": "synced",
      "last_synced_at": "2025-07-24T12:00:00Z",
      "created_at": "2025-07-24T12:00:00Z",
      "updated_at": "2025-07-24T12:00:00Z"
    }
  ],
  "links": {
    "first": "https://your-crm-erp-domain.com/api/calendar-events?page=1",
    "last": "https://your-crm-erp-domain.com/api/calendar-events?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "https://your-crm-erp-domain.com/api/calendar-events",
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

### Get Calendar Event

Retrieves a specific calendar event by ID.

**Endpoint:** `GET /calendar-events/{id}`

**Response:**

```json
{
  "data": {
    "id": 1,
    "owner_company_id": 1,
    "google_calendar_id": "primary",
    "google_event_id": "event123",
    "related_type": "appointment",
    "related_id": 1,
    "sync_status": "synced",
    "last_synced_at": "2025-07-24T12:00:00Z",
    "created_at": "2025-07-24T12:00:00Z",
    "updated_at": "2025-07-24T12:00:00Z",
    "related": {
      "appointment_id": 1,
      "title": "Client Meeting",
      "start_datetime": "2025-08-01T10:00:00Z",
      "end_datetime": "2025-08-01T11:00:00Z"
    }
  }
}
```

### Sync Calendar Event

Syncs a calendar event with Google Calendar.

**Endpoint:** `POST /calendar-events/{id}/sync`

**Response:**

```json
{
  "data": {
    "id": 1,
    "sync_status": "synced",
    "last_synced_at": "2025-07-24T17:00:00Z",
    "google_event_id": "event123",
    "updated_at": "2025-07-24T17:00:00Z"
  }
}
```

### Delete Calendar Event

Deletes a calendar event.

**Endpoint:** `DELETE /calendar-events/{id}`

**Response:**

```json
{
  "message": "Calendar event deleted successfully"
}
```

## Calendar Settings

### List Calendar Settings

Retrieves a list of calendar settings.

**Endpoint:** `GET /calendar-settings`

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "owner_company_id": 1,
      "user_id": null,
      "name": "Company Calendar",
      "provider": "google",
      "calendar_id": "primary",
      "access_token": "redacted",
      "refresh_token": "redacted",
      "token_expires_at": "2025-08-24T12:00:00Z",
      "is_active": true,
      "is_default": true,
      "sync_events": true,
      "created_at": "2025-07-24T12:00:00Z",
      "updated_at": "2025-07-24T12:00:00Z"
    }
  ]
}
```

### Get Calendar Setting

Retrieves a specific calendar setting by ID.

**Endpoint:** `GET /calendar-settings/{id}`

**Response:**

```json
{
  "data": {
    "id": 1,
    "owner_company_id": 1,
    "user_id": null,
    "name": "Company Calendar",
    "provider": "google",
    "calendar_id": "primary",
    "access_token": "redacted",
    "refresh_token": "redacted",
    "token_expires_at": "2025-08-24T12:00:00Z",
    "is_active": true,
    "is_default": true,
    "sync_events": true,
    "created_at": "2025-07-24T12:00:00Z",
    "updated_at": "2025-07-24T12:00:00Z"
  }
}
```

### Create Calendar Setting

Creates a new calendar setting.

**Endpoint:** `POST /calendar-settings`

**Request Body:**

```json
{
  "name": "Personal Calendar",
  "provider": "google",
  "calendar_id": "personal@gmail.com",
  "user_id": 1,
  "is_active": true,
  "is_default": false,
  "sync_events": true
}
```

**Response:**

```json
{
  "data": {
    "id": 2,
    "owner_company_id": 1,
    "user_id": 1,
    "name": "Personal Calendar",
    "provider": "google",
    "calendar_id": "personal@gmail.com",
    "access_token": null,
    "refresh_token": null,
    "token_expires_at": null,
    "is_active": true,
    "is_default": false,
    "sync_events": true,
    "created_at": "2025-07-24T18:00:00Z",
    "updated_at": "2025-07-24T18:00:00Z"
  }
}
```

### Update Calendar Setting

Updates an existing calendar setting.

**Endpoint:** `PUT /calendar-settings/{id}`

**Request Body:**

```json
{
  "name": "Updated Personal Calendar",
  "is_active": true,
  "is_default": true,
  "sync_events": true
}
```

**Response:**

```json
{
  "data": {
    "id": 2,
    "owner_company_id": 1,
    "user_id": 1,
    "name": "Updated Personal Calendar",
    "provider": "google",
    "calendar_id": "personal@gmail.com",
    "access_token": "redacted",
    "refresh_token": "redacted",
    "token_expires_at": "2025-08-24T12:00:00Z",
    "is_active": true,
    "is_default": true,
    "sync_events": true,
    "created_at": "2025-07-24T18:00:00Z",
    "updated_at": "2025-07-24T19:00:00Z"
  }
}
```

### Delete Calendar Setting

Deletes a calendar setting.

**Endpoint:** `DELETE /calendar-settings/{id}`

**Response:**

```json
{
  "message": "Calendar setting deleted successfully"
}
```

## Export Calendar

### Export Calendar Events to iCalendar

Exports calendar events to iCalendar format.

**Endpoint:** `GET /calendar-events/export`

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| start_date | date | Start date for events to export (format: YYYY-MM-DD) |
| end_date | date | End date for events to export (format: YYYY-MM-DD) |
| calendar_setting_id | integer | ID of the calendar setting to export from |
| related_type | string | Type of events to export (appointment, task) |

**Response:**

The response is an iCalendar (.ics) file with the appropriate headers:

```
Content-Type: text/calendar; charset=utf-8
Content-Disposition: attachment; filename="calendar_export_2025-07-24_200000.ics"
```

## Error Handling

All API endpoints follow a consistent error format:

```json
{
  "message": "Error message",
  "errors": {
    "field_name": [
      "Error description"
    ]
  }
}
```

Common HTTP status codes:

- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error

## Rate Limiting

API requests are subject to rate limiting. The current limits are:

- 60 requests per minute per user
- 1000 requests per day per user

Rate limit headers are included in all responses:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1627142400
```

## Webhooks

The API supports webhooks for real-time notifications of calendar events.

### Register Webhook

**Endpoint:** `POST /webhooks/calendar`

**Request Body:**

```json
{
  "url": "https://your-application.com/webhook-endpoint",
  "events": ["appointment.created", "appointment.updated", "appointment.deleted"]
}
```

**Response:**

```json
{
  "data": {
    "id": "webhook123",
    "url": "https://your-application.com/webhook-endpoint",
    "events": ["appointment.created", "appointment.updated", "appointment.deleted"],
    "created_at": "2025-07-24T20:00:00Z"
  }
}
```

### Webhook Payload

When an event occurs, a POST request will be sent to the registered webhook URL with the following payload:

```json
{
  "event": "appointment.created",
  "data": {
    "appointment_id": 1,
    "title": "Client Meeting",
    "start_datetime": "2025-08-01T10:00:00Z",
    "end_datetime": "2025-08-01T11:00:00Z"
  },
  "timestamp": "2025-07-24T20:00:00Z"
}
```
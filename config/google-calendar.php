<?php

return [

    /*
     * Path to the service account credentials JSON file.
     */
    'service_account_credentials_json' => storage_path('app/google-calendar/service-account-credentials.json'),

    /*
     * The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),

    /*
     * The email address of the user account to impersonate.
     */
    'user_to_impersonate' => env('GOOGLE_CALENDAR_IMPERSONATE'),
];

<?php

return [

    /*
     * The GA4 property ID (the number shown under Admin → Property details),
     * not the "G-XXXX" measurement ID.
     */
    'property_id' => env('ANALYTICS_PROPERTY_ID'),

    /*
     * Path to the Google service account credentials JSON file. It lives in
     * storage/app, outside the public directory, and is ignored by Git.
     */
    'service_account_credentials_json' => env('ANALYTICS_CREDENTIALS_PATH') ?: storage_path('app/analytics/service-account-credentials.json'),

    /*
     * How long (in minutes) a dashboard report is cached before GA4 is queried
     * again. The dashboard has a "Refresh" button to bypass it.
     */
    'cache_lifetime_in_minutes' => (int) env('ANALYTICS_CACHE_MINUTES', 30),

    /*
     * Longest custom date range (in days) the dashboard accepts.
     */
    'max_range_days' => 366,

    'cache' => [
        'store' => env('CACHE_STORE', 'database'),
    ],
];

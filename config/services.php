<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'adzuna' => [
        'app_id' => env('ADZUNA_APP_ID'),
        'app_key' => env('ADZUNA_APP_KEY'),
        'country' => env('ADZUNA_COUNTRY', 'in'),
    ],

    'remotive' => [
        'api_url' => env('REMOTIVE_API_URL', 'https://remotive.com/api/remote-jobs'),
    ],

    'jobicy' => [
        'api_url' => env('JOBICY_API_URL', 'https://jobicy.com/api/v2/remote-jobs'),
    ],

    'job_fetch' => [
        'verify_ssl' => filter_var(env('JOB_FETCH_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN),
        'timeout' => (int) env('JOB_FETCH_TIMEOUT', 20),
        'user_agent' => env('JOB_FETCH_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 (compatible; InternTrack/1.0; +https://interntrack.dev)'),
        'max_age_days' => (int) env('JOB_MAX_AGE_DAYS', 90),
        'auto_deactivate_stale' => filter_var(env('JOB_AUTO_DEACTIVATE_STALE', true), FILTER_VALIDATE_BOOLEAN),
        'require_description' => filter_var(env('JOB_REQUIRE_DESCRIPTION', true), FILTER_VALIDATE_BOOLEAN),
        'require_valid_url' => filter_var(env('JOB_REQUIRE_VALID_URL', true), FILTER_VALIDATE_BOOLEAN),
    ],

];

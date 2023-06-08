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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'line' => [
        'useMockUser' => env('LINE_USE_MOCK_USER'),
        'oaUrl' => env('LINE_OA_URL'),
        'loginChannelId' => env('LINE_LOGIN_CHANNEL_ID'),
        'liffId' => env('LINE_LIFF_ID'),
        'liffUrl' => env('LINE_LIFF_URL'),
        'channelAccessToken' => env('LINE_CHANNEL_ACCESS_TOKEN'),
        'channelSecret' => env('LINE_CHANNEL_SECRET'),
    ],
    
    'admin' => [
        'session_lifetime' => env('ADMIN_SESSION_LIFETIME', 10),
        'passwordExpiredDays' => env('PASSWORD_EXPIRED_DAYS', 120),
        'accountInactiveDays' => env('ACCOUNT_INACTIVE_DAYS', 90),
        'adminIpRestrict' => env('ADMIN_IP_RESTRICT', false),
        'loginFailedTimesLimit' => env('LOGIN_FAILED_TIMES_LIMIT', 5),
        'loginFailedDurationMinutes' => env('LOGIN_FAILED_DURATION_MINUTES', 10),
        'enablePassowrdLogin' => env('ENABLE_PASSWORD_LOGIN', true),
        'enableSamlLogin' => env('ENABLE_SAML_LOGIN', true),
        'disableCsrfTokenCheck' => env('DISABLE_CSRF_TOKEN_CHECK', false),
    ],

    'theme' => [
        'jackie' => env('JACKIE'),
        'horsebit' => env('HORSEBIT'),
        'bamboo' => env('BAMBOO'),
    ],
    
    'ga' => [
        'gtagId' => env('GTAG_ID'),
    ],
];

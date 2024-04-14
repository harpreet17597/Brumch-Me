<?php

return [
    'front_end_url' => env('FRONT_END_APP_URL', 'http://127.0.0.1:800'),
    'business_url' => env('BUSINESS_URL', 'http://127.0.0.1:8000/business'),
    'image_path' => [
        'avatar' => 'avatar/',
        'ads' => 'ad-management-pictures/',
        'driver' => env('FILESYSTEM_DRIVER', 's3'),
    ],
    'DISPLAY_DATETIME_FORMAT' => 'd-m-Y',
    'DISPLAY_DATEANDTIME_FORMAT' => 'd-m-Y h:i A',
    'ADMIN_EMAIL_ADDRESSS' => env('ADMIN_EMAIL_ADDRESSS', 'hello@getmonogamish.com'),
    'SUPPORT_EMAIL_ADDRESSS' => env('SUPPORT_EMAIL_ADDRESSS', 'hello@getmonogamish.com'),
    'APP_TIMEZONE' => env('APP_TIMEZONE', 'Australia/Melbourne'),
    'default_image' => [
        'avatar' => 'assets/img/blank.png',
    ],
    'twilio_sid' => env('TWILIO_ACCOUNT_SID',),
    'twilio_auth_token' => env('TWILIO_AUTH_TOKEN'),
    'twilio_from_number' => env('TWILIO_SMS_FROM'),
];

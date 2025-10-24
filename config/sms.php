<?php

return [
    'api_key' => env('SMS_API_KEY'),
    'base_url' => env('SMS_BASE_URL'),
    'url' => env('SMS_URL'),
    'template' => [
        'otp' => env('SMS_OTP_TEMPLATE'),
        'complete' => env('SMS_COMPLETE_TEMPLATE'),
    ]
];

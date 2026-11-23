<?php

return [
    'default' => env('SMS_DRIVER', 'Kavenegar'),
    'timeout' => (int) env('SMS_TIMEOUT', 15),
    'mode' => env('SMS_MODE', 'sync'),
    'queue' => [
        'name' => env('SMS_QUEUE', 'sms'),
        'tries' => (int) env('SMS_QUEUE_TRIES', 3),
        'timeout' => (int) env('SMS_QUEUE_TIMEOUT', 60),
    ],
    'drivers' => [
        'Kavenegar' => [
            'key' => env('KAVENEGAR_API_KEY', ''),
            'type' => env('KAVENEGAR_TYPE', 'sms'),
        ],
        'Smsir' => [
            'key' => env('SMSIR_API_KEY', ''),
        ],
        'Melipayamak' => [
            'username' => env('MELIPAYAMAK_USERNAME', ''),
            'password' => env('MELIPAYAMAK_PASSWORD', ''),
            'from' => env('MELIPAYAMAK_FROM', ''),
        ],
    ],
];

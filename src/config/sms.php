<?php

return [
    'default' => env('SMS_DRIVER', 'Kavenegar'),
    'timeout' => (int) env('SMS_TIMEOUT', 15),
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

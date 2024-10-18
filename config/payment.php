<?php

return [
    'default' => 'paypal',
    'payments' => [
        'paypal' => [
            'clientId' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'testMode' => env('PAYPAL_TEST_MODE', true),
        ],
    ],
];

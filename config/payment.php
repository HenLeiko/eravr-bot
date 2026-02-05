<?php

// конфигурационный файл для системы оплаты

return [

    'payment' => [
        'terminal_key' => env('PAYMENT_TERMINAL_KEY'),
        'terminal_password' => env('PAYMENT_TERMINAL_PASSWORD'),
        'terminal_url' => env('PAYMENT_TERMINAL_URL'),
    ],

    'data_config' => [
        'TerminalKey' => env('PAYMENT_TERMINAL_KEY'),
        'Receipt' => [
            'FfdVersion' => '1.2',
            'Taxation' => 'usn_income_outcome',
            'Email' => 'testmail@testmail.com',
            'Phone' => '+79999999999',
            'Items' => [[
                'Quantity' => 1.0,
                'PaymentMethod' => 'advance',
                'PaymentObject' => 'payment',
                'Tax' => 'none',
                'MeasurementUnit' => 'раз'
            ]]
        ]
    ]
];

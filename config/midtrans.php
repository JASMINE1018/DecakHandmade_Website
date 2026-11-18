<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    */
    'server_key' => env('MIDTRANS_SERVER_KEY') ?: 'SB-Mid-server-niHT7RA_UhZtZHPNq2ICPDR1',
    'client_key' => env('MIDTRANS_CLIENT_KEY') ?: 'Mid-client-dqgtddAwBv0q4t73',
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    /*
    |--------------------------------------------------------------------------
    | Default Manual Bank Transfer Details (Fallback if Midtrans not configured)
    |--------------------------------------------------------------------------
    */
    'manual_bank' => [
        'bank_name' => env('BANK_TRANSFER_NAME', 'BCA'),
        'account_number' => env('BANK_TRANSFER_ACCOUNT', '1234567890'),
        'account_holder' => env('BANK_TRANSFER_HOLDER', 'Decak Handmade'),
    ],
];

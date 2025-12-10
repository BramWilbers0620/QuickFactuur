<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trial Configuration
    |--------------------------------------------------------------------------
    */
    'trial_days' => env('QUICKFACTUUR_TRIAL_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | VAT Configuration
    |--------------------------------------------------------------------------
    */
    'vat_rate' => env('QUICKFACTUUR_VAT_RATE', 0.21),
    'vat_percentage' => env('QUICKFACTUUR_VAT_PERCENTAGE', 21),

    /*
    |--------------------------------------------------------------------------
    | Subscription Prices (in EUR for display)
    |--------------------------------------------------------------------------
    */
    'prices' => [
        'monthly' => env('QUICKFACTUUR_PRICE_MONTHLY', 5),
        'yearly' => env('QUICKFACTUUR_PRICE_YEARLY', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    */
    'company' => [
        'name' => env('QUICKFACTUUR_COMPANY_NAME', 'QuickFactuur'),
        'support_email' => env('QUICKFACTUUR_SUPPORT_EMAIL', 'support@quickfactuur.nl'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    */
    'invoice' => [
        'prefix' => env('QUICKFACTUUR_INVOICE_PREFIX', 'INV'),
        'vendor_name' => env('QUICKFACTUUR_VENDOR_NAME', 'QuickFactuur'),
    ],
];

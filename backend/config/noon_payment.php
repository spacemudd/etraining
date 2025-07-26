<?php
return [
    /**
     * Register route for NoonPaymentController
     */
    "register_routes" => false,
    
    'jisr' => [
        'business_id' => env('NOON_PAYMENT_JISR_BUSINESS_ID'),
        'app_name' => env('NOON_PAYMENT_JISR_APP_NAME'),
        'app_key' => env('NOON_PAYMENT_JISR_APP_KEY'),
        'token_identifier' => env('NOON_PAYMENT_JISR_TOKEN_IDENTIFIER'),
        'return_url' => env('NOON_PAYMENT_JISR_RETURN_URL'),
        'mode' => env('NOON_PAYMENT_JISR_MODE'),
        'order_category' => env('NOON_PAYMENT_JISR_ORDER_CATEGORY'),
        'channel' => env('NOON_PAYMENT_JISR_CHANNEL'),
        'payment_api' => env('NOON_PAYMENT_JISR_PAYMENT_API'),
        'auth_key' => base64_encode(env('NOON_PAYMENT_JISR_BUSINESS_ID') . "." . env("NOON_PAYMENT_JISR_APP_NAME") . ":" . env("NOON_PAYMENT_JISR_APP_KEY")),
    ],
    'jasarah' => [
        'business_id' => env('NOON_PAYMENT_BUSINESS_ID'),
        'app_name' => env('NOON_PAYMENT_APP_NAME'),
        'app_key' => env('NOON_PAYMENT_APP_KEY'),
        'token_identifier' => env('NOON_PAYMENT_TOKEN_IDENTIFIER'),
        'return_url' => env('NOON_PAYMENT_RETURN_URL'),
        'mode' => env('NOON_PAYMENT_MODE'),
        'order_category' => env('NOON_PAYMENT_ORDER_CATEGORY'),
        'channel' => env('NOON_PAYMENT_CHANNEL'),
        'payment_api' => env('NOON_PAYMENT_PAYMENT_API'),
        'auth_key' => base64_encode(env('NOON_PAYMENT_BUSINESS_ID') . "." . env("NOON_PAYMENT_APP_NAME") . ":" . env("NOON_PAYMENT_APP_KEY")),
    ],

    'mode' => env('NOON_PAYMENT_MODE', 'Live'),
    'order_category' => env('NOON_PAYMENT_ORDER_CATEGORY', 'pay'),
    'channel' => env('NOON_PAYMENT_CHANNEL', 'web'),
    'payment_api' => env('NOON_PAYMENT_PAYMENT_API'),
    'payment_api_test' => env('NOON_PAYMENT_PAYMENT_API_TEST'),

    /**
     *  Base64(BusinessIdentifier.ApplicationIdentifier:ApplicationKey)
     */
    "auth_key" => base64_encode(env('NOON_PAYMENT_BUSINESS_ID_JISR').".".env("NOON_PAYMENT_APP_NAME_JISR").":".env("NOON_PAYMENT_APP_KEY_JISR")),

    "token_identifier" => env('NOON_PAYMENT_TOKEN_IDENTIFIER'),
    "return_url" => env('NOON_PAYMENT_RETURN_URL'),
];


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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // config/services.php
'tripay' => [
    'api_key' => env('TRIPAY_API_KEY'),
    'private_key' => env('TRIPAY_PRIVATE_KEY'),
    'merchant_code' => env('TRIPAY_MERCHANT_CODE'),
    'callback_url' => env('TRIPAY_CALLBACK_URL', rtrim(env('APP_URL', ''), '/') . '/webhooks/tripay'),
    'payment_url' => env('TRIPAY_PAYMENT_URL'),
    'status_url' => env(
        'TRIPAY_STATUS_URL',
        str_contains((string) env('TRIPAY_PAYMENT_URL', ''), 'api-sandbox')
            ? 'https://tripay.co.id/api-sandbox/transaction/check-status'
            : 'https://tripay.co.id/api/transaction/check-status'
    ),
],

'raja_ongkir' => [
    'api_key' => env('SHIPPING_API_KEY'),
    'province_url' => env('SHIPPING_PROVINCE_URL'),
    'city_url' => env('SHIPPING_CITY_URL'),
    'district_url' => env('SHIPPING_DISTRICT_URL'),
    'subdistrict_url' => env('SHIPPING_SUBDISTRICT_URL'),
    'calculate_cost_url' => env('SHIPPING_CALCULATE_COST_URL'),
    'origin_city_id' => env('SHIPPING_ORIGIN_CITY_ID'),
    'origin_district_id' => env('SHIPPING_ORIGIN_DISTRICT_ID'),
],

];

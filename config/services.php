<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],


    'paystack' => [
        'public' => env('PAYSTACK_PUBLIC_KEY'),
        'secret' => env('PAYSTACK_SECRET_KEY'),
    ],

    'cheqpay' => [
        'public' => env('CHEQPAY_PUBLIC_KEY'),
        'secret' => env('CHEQPAY_SECRET_KEY'),
        'base_url' => env('CHEQPAY_BASE_URL', 'https://api.cheqpay.com/v1'),
        'currency' => env('CHEQPAY_CURRENCY', 'NGN'),
        'init_path' => env('CHEQPAY_INIT_PATH', '/transactions/initialize'),
        'verify_path' => env('CHEQPAY_VERIFY_PATH', '/transactions/verify/'),
        'signature_header' => env('CHEQPAY_SIGNATURE_HEADER', 'x-cheqpay-signature'),
    ],

];

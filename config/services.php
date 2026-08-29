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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
    ],

    'msg91' => [
        'auth_key' => env('MSG91_AUTH_KEY'),
        'template_id' => env('MSG91_OTP_TEMPLATE_ID'),
        'transactional_template_id' => env('MSG91_TRANSACTIONAL_TEMPLATE_ID'),
        'sender_id' => env('MSG91_SENDER_ID'),
    ],

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'merchant_id' => env('PAYPAL_MERCHANT_ID'),
        'sandbox' => env('PAYPAL_SANDBOX', true),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    ],

    'exchange_rate' => [
        'inr_usd_fallback' => env('EXCHANGE_RATE_INR_USD_FALLBACK', 0.012),
    ],

    'instamojo' => [
        'api_key' => env('INSTAMOJO_API_KEY'),
        'auth_token' => env('INSTAMOJO_AUTH_TOKEN'),
        'salt' => env('INSTAMOJO_SALT'),
        'sandbox' => env('INSTAMOJO_SANDBOX', true),
    ],

];

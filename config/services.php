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

    /*
    |--------------------------------------------------------------------------
    | Alpha SMS (Bangladesh)
    |--------------------------------------------------------------------------
    | REST API for sending SMS to Bangladeshi numbers.
    | Get your API key from: https://alphasms.com.bd
    */
    'alpha_sms' => [
        'api_key'   => env('ALPHA_SMS_API_KEY', ''),
        'sender_id' => env('ALPHA_SMS_SENDER_ID', 'ClothStore'),
        'base_url'  => env('ALPHA_SMS_URL', 'https://alphasms.com.bd/api/v1/send'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Notification Settings
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'email' => env('ADMIN_EMAIL', ''),
    ],

];

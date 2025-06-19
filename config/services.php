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

    'resend'   => [
        'key' => env('RESEND_KEY'),
    ],

    'ses'      => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack'    => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'wilayah'  => [
        'api_key'         => env('WILAYAH_API_KEY'),
        'ongkir_api_key'  => env('ONGKIR_API_KEY'),
        'base_url'        => env('WILAYAH_BASE_URL', 'https://api.binderbyte.com/wilayah'),
        'ongkir_base_url'        => env('WILAYAH_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
        'timeout'         => env('WILAYAH_TIMEOUT', 30),
        'connect_timeout' => env('WILAYAH_CONNECT_TIMEOUT', 10),
        'cache_timeout'   => env('WILAYAH_CACHE_TIMEOUT', 86400),
    ],
    'ongkir'  => [
        'api_key'         => env('ONGKIR_API_KEY'),
        'base_url'        => env('WILAYAH_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
        'timeout'         => env('WILAYAH_TIMEOUT', 30),
        'connect_timeout' => env('WILAYAH_CONNECT_TIMEOUT', 10),
        'cache_timeout'   => env('WILAYAH_CACHE_TIMEOUT', 86400),
    ],

];

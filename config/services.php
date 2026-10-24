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

    /*
    | The ARTi licensing Worker (Cloudflare). Laravel is only its client; the admin token stays in .env.
    | `url` is the API base including /v1, e.g. http://127.0.0.1:8760/v1 for `wrangler dev`.
    */
    'licensing' => [
        'url' => env('LICENSE_API_URL'),
        'admin_token' => env('LICENSE_ADMIN_TOKEN'),
        'installer_url' => env('LICENSE_INSTALLER_URL'),
    ],

    /*
    | Which payment gateway new orders use: `midtrans` (default), `xendit` or `mayar`. An existing order keeps the gateway it
    | was created with, so switching never strands a payment in progress.
    */
    'payments' => [
        'driver' => env('PAYMENT_GATEWAY', 'midtrans'),
    ],

    /*
    | Midtrans (payment gateway). The server key lives in .env only: the sandbox key (starts with "SB-Mid-server-")
    | while MIDTRANS_IS_PRODUCTION is false, the live key once it is true. The notification URL is set in the Midtrans
    | dashboard (Settings, Configuration, Payment Notification URL): https://<site>/webhooks/midtrans
    */
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
        'snap_url' => env('MIDTRANS_SNAP_URL', env('MIDTRANS_IS_PRODUCTION', false) ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com'),
        'api_url' => env('MIDTRANS_API_URL', env('MIDTRANS_IS_PRODUCTION', false) ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com'),
        // Snap `enabled_payments` behind the two cards on the landing form. Names must be active in the Midtrans
        // dashboard (Settings, Snap Preferences, Payment); edit here, comma separated in .env, if one is not.
        'methods' => [
            'va' => array_filter(explode(',', (string) env('MIDTRANS_METHODS_VA', 'bca_va,bni_va,bri_va,permata_va,echannel,gopay,shopeepay'))),
            'qris' => array_filter(explode(',', (string) env('MIDTRANS_METHODS_QRIS', 'other_qris'))),
        ],
    ],

    /*
    | Mayar (payment gateway, API v2). The API key lives in .env only and must match the environment: a sandbox key with
    | api.mayar.io, a production key with api.mayar.id (MAYAR_API_URL overrides). Mayar signs nothing, so the webhook URL
    | carries a secret: register https://<site>/webhooks/mayar/<MAYAR_WEBHOOK_SECRET> in the Mayar dashboard (Integration, Webhook).
    */
    'mayar' => [
        'api_key' => env('MAYAR_API_KEY'),
        'api_url' => env('MAYAR_API_URL', env('MAYAR_IS_PRODUCTION', false) ? 'https://api.mayar.id/hl/v2' : 'https://api.mayar.io/hl/v2'),
        'webhook_secret' => env('MAYAR_WEBHOOK_SECRET'),
        // The dashboard's "Webhook Token" (Integration, API Keys & Token); empty skips the check.
        'webhook_token' => env('MAYAR_WEBHOOK_TOKEN'),
    ],

    /*
    | Xendit (payment gateway). Secret key and callback token live in .env only; use the test-mode key
    | in development. The callback token is the one shown next to the webhook URL in the Xendit dashboard.
    */
    'xendit' => [
        'url' => env('XENDIT_API_URL', 'https://api.xendit.co'),
        'secret_key' => env('XENDIT_SECRET_KEY'),
        'callback_token' => env('XENDIT_CALLBACK_TOKEN'),
    ],

    /*
    | WhatsApp delivery of purchases. `none` (default) sends nothing; `log` records that a message would have gone
    | out (recipient tail and length only). A real provider is added as another driver once one is chosen.
    */
    'whatsapp' => [
        'driver' => env('WHATSAPP_DRIVER', 'none'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];

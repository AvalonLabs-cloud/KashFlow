<?php

return [
    'base_url' => env('FLUTTERWAVE_END_POINT'),
    'secret_key' => env('FLUTTERWAVE_CLIENT_SECRET'),
    'flutterwave_secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
    'token_generation_url' => env('FLUTTERWAVE_TOKEN_GENERATION_URL'),
    'client_id' => env('FLUTTERWAVE_CLIENT_ID'),
    'grant_type' => 'client_credentials',
    'default_narration' => 'fuck off',
    'bvn' => '12945688901',
    'only_currency_unless_the_client_fucking_pays' => 'NGN',
    'account_type' => 'static',
    'token_validity_duration' => 8 * 60,
    'default_country_to_get_banks' => 'NG',
    'payment_method' => 'bank_transfer',
    'type_of_accounts' => true,
    'test_user_info' => [
        'firstname' => 'flutterwave',
        'lastname' => 'emmanuella',
        'phone' => '09182026039',
        'email' => 'teststesttest@gmail.com',
        'password' => 'Hashirama@1234',
    ],
    'pin_token_limit' => 10,
    'otp_lenght' => 6,
    'otp_medium' => 'sms',
    'webhooksite' => 'https://webhook.site/964e6c79-f102-44ea-a6f8-dd97d85cb4a9',
    'bvn_verification_redirect_url' => env('BVN_VERIFICATION_REDIRECT_URL'),
    'IndulgeMFB' => '090772',

];

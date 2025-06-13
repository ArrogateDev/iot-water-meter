<?php

return [
    'mch_id' => env('WX_MCH_ID'),
    'app_id' => env('WX_APPID'),
    'private_key' => storage_path('app/wx/apiclient_key.pem'),
    'certificate' => storage_path('app/wx/apiclient_cert.pem'),
    'secret_key' => env('WX_SECRET_KEY'),
    'http' => [
        'throw' => true,
        'timeout' => 5.0,
    ],
];

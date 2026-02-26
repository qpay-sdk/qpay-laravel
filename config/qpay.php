<?php

return [
    'base_url' => env('QPAY_BASE_URL', 'https://merchant.qpay.mn'),
    'username' => env('QPAY_USERNAME'),
    'password' => env('QPAY_PASSWORD'),
    'invoice_code' => env('QPAY_INVOICE_CODE'),
    'callback_url' => env('QPAY_CALLBACK_URL'),
    'webhook_path' => env('QPAY_WEBHOOK_PATH', '/qpay/webhook'),
];

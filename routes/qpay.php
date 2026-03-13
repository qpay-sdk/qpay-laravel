<?php

use Illuminate\Support\Facades\Route;
use QPay\Laravel\Http\Controllers\WebhookController;

Route::get(config('qpay.webhook_path', '/qpay/webhook'), WebhookController::class)
    ->name('qpay.webhook');

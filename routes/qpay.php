<?php

use Illuminate\Support\Facades\Route;
use QPay\Laravel\Http\Controllers\WebhookController;

Route::post(config('qpay.webhook_path', '/qpay/webhook'), WebhookController::class)
    ->name('qpay.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

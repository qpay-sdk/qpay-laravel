# QPay Laravel

[![CI](https://github.com/qpay-sdk/qpay-laravel/actions/workflows/ci.yml/badge.svg)](https://github.com/qpay-sdk/qpay-laravel/actions)
[![Packagist](https://img.shields.io/packagist/v/qpay-sdk/laravel)](https://packagist.org/packages/qpay-sdk/laravel)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

QPay V2 payment integration for Laravel.

## Install

```bash
composer require qpay-sdk/laravel
php artisan qpay:install
```

## Configuration

Add to `.env`:

```
QPAY_BASE_URL=https://merchant.qpay.mn
QPAY_USERNAME=your_username
QPAY_PASSWORD=your_password
QPAY_INVOICE_CODE=your_invoice_code
QPAY_CALLBACK_URL=https://yoursite.com/qpay/webhook
```

## Usage

```php
use QPay\Laravel\Facades\QPay;
use QPay\Models\CreateSimpleInvoiceRequest;

$invoice = QPay::createSimpleInvoice(new CreateSimpleInvoiceRequest(
    invoiceCode: config('qpay.invoice_code'),
    senderInvoiceNo: 'ORDER-001',
    amount: 10000,
    callbackUrl: config('qpay.callback_url'),
));

// $invoice->invoiceId, $invoice->qrImage, $invoice->urls
```

## Blade Components

```blade
<x-qpay-qr-code :qr-image="$invoice->qrImage" />
<x-qpay-payment-button :urls="$invoice->urls" :short-url="$invoice->qPayShortUrl" />
```

## Webhook

Listen for payment events:

```php
use QPay\Laravel\Events\PaymentReceived;

Event::listen(PaymentReceived::class, function ($event) {
    // $event->paymentId
    // $event->result->rows
});
```

## License

MIT

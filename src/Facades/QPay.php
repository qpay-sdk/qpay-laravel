<?php

namespace QPay\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use QPay\QPayClient;

/**
 * @method static \QPay\Models\InvoiceResponse createInvoice(\QPay\Models\CreateInvoiceRequest $request)
 * @method static \QPay\Models\InvoiceResponse createSimpleInvoice(\QPay\Models\CreateSimpleInvoiceRequest $request)
 * @method static void cancelInvoice(string $invoiceId)
 * @method static \QPay\Models\PaymentDetail getPayment(string $paymentId)
 * @method static \QPay\Models\PaymentCheckResponse checkPayment(\QPay\Models\PaymentCheckRequest $request)
 * @method static \QPay\Models\PaymentListResponse listPayments(\QPay\Models\PaymentListRequest $request)
 *
 * @see \QPay\QPayClient
 */
class QPay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return QPayClient::class;
    }
}

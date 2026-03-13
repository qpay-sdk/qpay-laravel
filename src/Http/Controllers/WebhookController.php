<?php

namespace QPay\Laravel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use QPay\Laravel\Events\PaymentReceived;
use QPay\Laravel\Events\PaymentFailed;
use QPay\Models\PaymentCheckRequest;
use QPay\QPayClient;

class WebhookController extends Controller
{
    public function __invoke(Request $request, QPayClient $client): Response
    {
        $paymentId = $request->query('qpay_payment_id');

        if (! $paymentId) {
            return response('Missing qpay_payment_id', 400);
        }

        try {
            $result = $client->checkPayment(new PaymentCheckRequest(
                objectType: 'INVOICE',
                objectId: $paymentId,
            ));

            if (count($result->rows) > 0) {
                event(new PaymentReceived($paymentId, $result));
            } else {
                event(new PaymentFailed($paymentId, 'No payment found'));
            }

            return response('SUCCESS', 200);
        } catch (\Throwable $e) {
            event(new PaymentFailed($paymentId, $e->getMessage()));
            return response('FAILED', 500);
        }
    }
}

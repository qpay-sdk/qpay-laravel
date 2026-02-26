<?php

namespace QPay\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use QPay\Laravel\Events\PaymentReceived;
use QPay\Laravel\Events\PaymentFailed;
use QPay\Models\PaymentCheckRequest;
use QPay\QPayClient;

class WebhookController extends Controller
{
    public function __invoke(Request $request, QPayClient $client): JsonResponse
    {
        $invoiceId = $request->input('invoice_id');

        if (! $invoiceId) {
            return response()->json(['error' => 'Missing invoice_id'], 400);
        }

        try {
            $result = $client->checkPayment(new PaymentCheckRequest(
                objectType: 'INVOICE',
                objectId: $invoiceId,
            ));

            if (count($result->rows) > 0) {
                event(new PaymentReceived($invoiceId, $result));
                return response()->json(['status' => 'paid']);
            }

            event(new PaymentFailed($invoiceId, 'No payment found'));
            return response()->json(['status' => 'unpaid']);
        } catch (\Throwable $e) {
            event(new PaymentFailed($invoiceId, $e->getMessage()));
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

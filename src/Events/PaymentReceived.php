<?php

namespace QPay\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use QPay\Models\PaymentCheckResponse;

class PaymentReceived
{
    use Dispatchable;

    public function __construct(
        public readonly string $invoiceId,
        public readonly PaymentCheckResponse $result,
    ) {}
}

<?php

namespace QPay\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PaymentFailed
{
    use Dispatchable;

    public function __construct(
        public readonly string $paymentId,
        public readonly string $reason,
    ) {}
}

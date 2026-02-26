<?php

namespace QPay\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PaymentFailed
{
    use Dispatchable;

    public function __construct(
        public readonly string $invoiceId,
        public readonly string $reason,
    ) {}
}

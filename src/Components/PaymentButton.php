<?php

namespace QPay\Laravel\Components;

use Illuminate\View\Component;

class PaymentButton extends Component
{
    public function __construct(
        public array $urls = [],
        public string $shortUrl = '',
    ) {}

    public function render()
    {
        return view('qpay::components.payment-button');
    }
}

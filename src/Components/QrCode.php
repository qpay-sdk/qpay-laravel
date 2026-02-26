<?php

namespace QPay\Laravel\Components;

use Illuminate\View\Component;

class QrCode extends Component
{
    public function __construct(
        public string $qrImage = '',
        public string $qrText = '',
        public int $size = 256,
    ) {}

    public function render()
    {
        return view('qpay::components.qr-code');
    }
}

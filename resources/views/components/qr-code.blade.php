<div class="qpay-qr-code" style="text-align:center;">
    @if($qrImage)
        <img src="data:image/png;base64,{{ $qrImage }}" alt="QPay QR Code" width="{{ $size }}" height="{{ $size }}">
    @elseif($qrText)
        <p style="word-break:break-all;font-family:monospace;font-size:12px;">{{ $qrText }}</p>
    @endif
</div>

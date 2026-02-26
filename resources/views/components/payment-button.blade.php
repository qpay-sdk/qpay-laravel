<div class="qpay-payment-buttons">
    @if($shortUrl)
        <a href="{{ $shortUrl }}" class="qpay-btn qpay-btn-primary" target="_blank">QPay-ээр төлөх</a>
    @endif
    @foreach($urls as $link)
        <a href="{{ $link['link'] ?? '' }}" class="qpay-btn" target="_blank">
            @if(isset($link['logo']))
                <img src="{{ $link['logo'] }}" alt="{{ $link['name'] ?? '' }}" width="24" height="24">
            @endif
            {{ $link['name'] ?? 'Pay' }}
        </a>
    @endforeach
</div>
<style>
.qpay-payment-buttons { display:flex; flex-wrap:wrap; gap:8px; justify-content:center; }
.qpay-btn { display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border:1px solid #ddd; border-radius:8px; text-decoration:none; color:#333; font-size:14px; }
.qpay-btn:hover { background:#f5f5f5; }
.qpay-btn-primary { background:#00B462; color:#fff; border-color:#00B462; }
.qpay-btn-primary:hover { background:#009e56; color:#fff; }
</style>

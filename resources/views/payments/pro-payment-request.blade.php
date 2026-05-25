@php
    $assembly = $paymentRequest->assembly;
    $integration = $paymentRequest->integration;
    $asset = optional($paymentRequest->bill?->property)->account_no
        ?: optional($paymentRequest->bill?->business)->business_name
        ?: null;
    $paid = $paymentRequest->isPaid();
    $expired = $paymentRequest->expires_at && $paymentRequest->expires_at->isPast() && ! $paid;
    $statusClass = $paid ? 'good' : ($expired ? 'danger' : 'warn');
    $publicUrl = route('public.pro-payment-requests.show', $paymentRequest);
    $gatewayUrl = $paymentRequest->payment_url && $paymentRequest->payment_url !== $publicUrl ? $paymentRequest->payment_url : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Request - Olives BI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary:#1a5c2a; --accent:#d4a017; --line:rgba(26,92,42,.14); --muted:#6b7280; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; font-family:Inter,sans-serif; background:#f1f7f3; color:#17251b; display:grid; place-items:center; padding:1rem; }
        .pay-shell { width:min(760px,100%); background:#fff; border:1px solid var(--line); border-radius:18px; overflow:hidden; box-shadow:0 24px 70px rgba(26,92,42,.16); }
        .pay-head { background:linear-gradient(135deg,var(--primary),var(--accent)); color:#fff; padding:1.35rem 1.5rem; display:flex; justify-content:space-between; gap:1rem; align-items:flex-start; }
        .brand { font-family:Poppins,sans-serif; font-weight:800; font-size:1.1rem; }
        .brand em { color:#fef3c7; font-style:normal; }
        .head-sub { color:rgba(255,255,255,.75); font-size:.82rem; margin-top:.28rem; line-height:1.5; }
        .status { display:inline-flex; border-radius:999px; padding:.28rem .62rem; font-size:.68rem; text-transform:uppercase; font-weight:900; white-space:nowrap; }
        .status.good { background:#dcfce7; color:#166534; }
        .status.warn { background:#fff7ed; color:#92400e; }
        .status.danger { background:#fee2e2; color:#991b1b; }
        .pay-body { padding:1.5rem; display:grid; gap:1rem; }
        .amount { font-family:Poppins,sans-serif; font-weight:800; font-size:clamp(1.8rem,5vw,2.65rem); color:var(--primary); line-height:1; }
        .desc { color:var(--muted); line-height:1.6; font-size:.95rem; }
        .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.75rem; }
        .tile { border:1px solid var(--line); border-radius:12px; padding:.85rem; background:#fbfefc; }
        .label { color:var(--muted); font-size:.66rem; font-weight:900; text-transform:uppercase; letter-spacing:.45px; margin-bottom:.28rem; }
        .value { font-weight:800; overflow-wrap:anywhere; }
        .actions { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; border-top:1px solid var(--line); padding-top:1rem; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.45rem; border:1px solid var(--line); border-radius:10px; padding:.75rem .95rem; text-decoration:none; font-weight:900; color:var(--primary); background:#eef8f1; }
        .btn.primary { background:var(--accent); border-color:var(--accent); color:#fff; }
        .notice { border:1px solid #fed7aa; background:#fff7ed; color:#92400e; border-radius:12px; padding:.8rem .9rem; font-weight:800; line-height:1.45; }
        .notice.good { border-color:#bbf7d0; background:#f0fdf4; color:#166534; }
        @media (max-width:640px) { .pay-head { flex-direction:column; } .grid { grid-template-columns:1fr; } .btn { width:100%; } }
    </style>
</head>
<body>
    <main class="pay-shell">
        <header class="pay-head">
            <div>
                <div class="brand">Olives <em>BI</em></div>
                <div class="head-sub">{{ $assembly->name ?? 'Olives Pro' }} secure payment request</div>
            </div>
            <span class="status {{ $statusClass }}">{{ $paid ? 'Paid' : ($expired ? 'Expired' : $paymentRequest->status) }}</span>
        </header>

        <section class="pay-body">
            @if ($verification)
                <div class="notice {{ ($verification['paid'] ?? false) ? 'good' : '' }}">
                    {{ ($verification['paid'] ?? false) ? 'Payment verified successfully.' : ($verification['message'] ?? 'Payment has not been verified yet.') }}
                </div>
            @endif

            <div>
                <div class="amount">{{ strtoupper($paymentRequest->currency_code) }} {{ number_format((float) $paymentRequest->amount, 2) }}</div>
                <div class="desc">{{ $paymentRequest->description ?: 'Olives BI payment request' }}</div>
            </div>

            <div class="grid">
                <div class="tile"><div class="label">Reference</div><div class="value">{{ $paymentRequest->payment_reference ?: $paymentRequest->uuid }}</div></div>
                <div class="tile"><div class="label">Provider</div><div class="value">{{ $integration?->providerLabel() ?: ucfirst((string) $paymentRequest->provider) }}</div></div>
                <div class="tile"><div class="label">Payer</div><div class="value">{{ $paymentRequest->payer_name ?: 'N/A' }}</div></div>
                <div class="tile"><div class="label">Asset</div><div class="value">{{ $asset ?: ucfirst($paymentRequest->purpose) }}</div></div>
            </div>

            @if ($expired)
                <div class="notice">This payment request has expired. Contact the issuing office for a new link.</div>
            @elseif ($paid)
                <div class="notice good">This payment request has already been paid.</div>
            @else
                <div class="actions">
                    @if ($gatewayUrl)
                        <a class="btn primary" href="{{ $gatewayUrl }}"><i class="bi bi-shield-check"></i> Continue to gateway</a>
                    @else
                        <div class="notice">This provider is configured for offline or merchant-assisted settlement. Contact the issuing office with the reference above.</div>
                    @endif
                    <a class="btn" href="{{ route('public.pro-payment-requests.callback', $paymentRequest) }}"><i class="bi bi-arrow-repeat"></i> Check payment status</a>
                </div>
            @endif
        </section>
    </main>
</body>
</html>

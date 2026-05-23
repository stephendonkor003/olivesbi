<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $success ? 'Thank You!' : 'Payment Incomplete' }} — Olives BI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary:#1a5c2a; --accent:#4cc76a; --dark-bg:#071a0d; }
        body { font-family:'Inter',sans-serif; background:#f0f7f1; min-height:100vh; display:flex; flex-direction:column; }
        .don-nav {
            background:rgba(7,26,13,.97); padding:.7rem 0;
            box-shadow:0 2px 20px rgba(0,0,0,.25);
        }
        .brand { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.3rem; color:#fff; text-decoration:none; display:flex; align-items:center; gap:.5rem; }
        .brand .leaf { width:32px; height:32px; background:linear-gradient(135deg,var(--accent),#2d9e4b); border-radius:50% 10px 50% 10px; display:flex; align-items:center; justify-content:center; }
        .brand span { color:var(--accent); }
        main { flex:1; display:flex; align-items:center; justify-content:center; padding:3rem 1rem; }
        .result-card {
            background:#fff; border-radius:24px; padding:3rem 2.5rem;
            box-shadow:0 8px 48px rgba(26,92,42,.12);
            text-align:center; max-width:480px; width:100%;
        }
        .result-icon { font-size:4rem; margin-bottom:1rem; }
        .result-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.8rem; margin-bottom:.5rem; }
        .result-sub { color:#6b7280; font-size:.95rem; line-height:1.6; margin-bottom:1.75rem; }
        .ref-box {
            background:#f0f7f1; border-radius:10px; padding:.65rem 1rem;
            font-size:.8rem; color:#4b6e52; margin-bottom:1.75rem;
            word-break:break-all;
        }
        .ref-box span { font-weight:700; color:var(--primary); }
        .btn-go {
            display:inline-flex; align-items:center; gap:.5rem;
            background:linear-gradient(135deg,var(--primary),var(--accent));
            color:#fff; border:none; border-radius:10px;
            padding:.75rem 2rem; font-weight:700; font-size:.95rem;
            text-decoration:none; transition:all .25s;
        }
        .btn-go:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(76,199,106,.4); color:#fff; }
        .btn-ghost {
            display:inline-flex; align-items:center; gap:.5rem;
            background:transparent; border:1.5px solid rgba(26,92,42,.2);
            color:var(--primary); border-radius:10px;
            padding:.75rem 2rem; font-weight:600; font-size:.9rem;
            text-decoration:none; transition:all .25s;
        }
        .btn-ghost:hover { background:var(--primary); color:#fff; }
        .success-color { color:var(--primary); }
        .fail-color { color:#dc2626; }
        footer { background:var(--dark-bg); padding:1rem 0; text-align:center; color:rgba(255,255,255,.3); font-size:.78rem; }
        footer span { color:var(--accent); }
    </style>
</head>
<body>

<nav class="don-nav">
    <div class="container">
        <a href="{{ route('auth.LandingPage') }}" class="brand">
            <div class="leaf">🌿</div>
            Olives <span>BI</span>
        </a>
    </div>
</nav>

<main>
    <div class="result-card">
        @if($success)
            <div class="result-icon">🎉</div>
            <div class="result-title success-color">Thank You!</div>
            <p class="result-sub">
                Your generous donation via <strong>{{ $gateway }}</strong> has been received.
                @if(!empty($amount) && !empty($currency))
                    You contributed <strong>{{ $currency }} {{ $amount }}</strong>.
                @endif
                Together we're protecting forests, rivers, and wetlands for future generations. 🌍
            </p>

            @if(!empty($reference))
                <div class="ref-box">
                    Transaction Reference: <span>{{ $reference }}</span>
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('donation.index') }}" class="btn-go">
                    <i class="bi bi-heart-fill"></i> Donate Again
                </a>
                <a href="{{ route('auth.LandingPage') }}" class="btn-ghost">
                    <i class="bi bi-house"></i> Home
                </a>
            </div>

            <div style="margin-top:1.75rem; padding-top:1.25rem; border-top:1px solid rgba(26,92,42,.08);">
                <p style="font-size:.8rem; color:#9ca3af; margin-bottom:.5rem;">Share your support</p>
                <a href="https://twitter.com/intent/tweet?text=I+just+donated+to+%40OlivesBI+to+protect+the+environment+🌍🌿"
                   target="_blank"
                   style="display:inline-flex; align-items:center; gap:.4rem; background:#1da1f2; color:#fff; border-radius:8px; padding:.4rem 1rem; font-size:.8rem; font-weight:600; text-decoration:none;">
                    <i class="bi bi-twitter-x"></i> Share on X
                </a>
            </div>

        @else
            <div class="result-icon">😔</div>
            <div class="result-title fail-color">Payment Incomplete</div>
            <p class="result-sub">
                Your {{ $gateway }} payment could not be verified or was cancelled.
                No charge was made. Please try again or choose a different payment method.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('donation.index') }}" class="btn-go">
                    <i class="bi bi-arrow-clockwise"></i> Try Again
                </a>
                <a href="{{ route('auth.LandingPage') }}" class="btn-ghost">
                    <i class="bi bi-house"></i> Home
                </a>
            </div>
        @endif
    </div>
</main>

<footer>
    <span>🌿 Olives BI</span> — Environmental Protection Intelligence &nbsp;·&nbsp; © {{ date('Y') }}
</footer>

</body>
</html>

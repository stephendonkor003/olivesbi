<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support Olives BI — Environmental Protection Donations</title>
    <meta name="description" content="Donate to Olives BI and help protect forests, wetlands, rivers and land worldwide.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:       #1a5c2a;
            --primary-light: #2d9e4b;
            --accent:        #4cc76a;
            --light-green:   #e8f5e9;
            --dark-bg:       #071a0d;
            --paystack:      #00c3f7;
            --flutterwave:   #f5a623;
            --stripe:        #635bff;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: #f0f7f1; color: #1a2e1e; }

        /* ── NAVBAR ── */
        #mainNav {
            position: sticky;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: .6rem 0;
            background: rgba(7,26,13,.97);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,.3);
        }
        .navbar-brand {
            display: flex; align-items: center; gap: .5rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 800; font-size: 1.4rem;
            color: #fff !important; text-decoration: none;
            letter-spacing: -.5px;
        }
        .navbar-brand .brand-leaf {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            border-radius: 50% 12px 50% 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 0 12px rgba(76,199,106,.5);
        }
        .navbar-brand span.brand-bi { color: var(--accent); }
        .nav-link-custom {
            color: rgba(255,255,255,.82) !important;
            font-weight: 500; font-size: .9rem;
            transition: color .2s;
            padding: .4rem .85rem !important;
            border-radius: 6px;
        }
        .nav-link-custom:hover { color: var(--accent) !important; background: rgba(255,255,255,.06); }
        .btn-nav-login {
            background: transparent;
            border: 1.5px solid rgba(255,255,255,.4);
            color: #fff; border-radius: 8px;
            padding: .4rem 1.2rem; font-weight: 500; font-size: .88rem;
            transition: all .25s;
        }
        .btn-nav-login:hover { border-color: var(--accent); color: var(--accent); }
        .btn-nav-cta {
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            border: none; color: #fff; border-radius: 8px;
            padding: .4rem 1.4rem; font-weight: 600; font-size: .88rem;
            transition: all .25s; box-shadow: 0 2px 10px rgba(76,199,106,.35);
        }
        .btn-nav-cta:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(76,199,106,.45); color: #fff; }
        .navbar-toggler { border: 1.5px solid rgba(255,255,255,.4); }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,0.85%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ── HERO STRIP ── */
        .don-hero {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary) 100%);
            position: relative; overflow: hidden;
            padding: 4rem 0 3rem;
            text-align: center;
        }
        .don-hero::before {
            content: '';
            position: absolute; inset: 0;
            background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1600&q=40') center/cover;
            opacity: .15;
        }
        .don-hero-content { position: relative; z-index: 2; }
        .don-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800; font-size: clamp(1.8rem,4vw,3rem);
            color: #fff; margin-bottom: .75rem;
        }
        .don-hero h1 span { color: var(--accent); }
        .don-hero p { color: rgba(255,255,255,.7); font-size: 1rem; max-width: 520px; margin: 0 auto; line-height: 1.7; }
        .cause-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(76,199,106,.15);
            border: 1px solid rgba(76,199,106,.35);
            color: #a8e6bc; border-radius: 50px;
            padding: .3rem .9rem; font-size: .78rem;
            font-weight: 700; letter-spacing: .5px;
            text-transform: uppercase; margin-bottom: 1rem;
        }

        /* ── CAUSE CARDS ── */
        .cause-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.4rem;
            border: 1.5px solid rgba(26,92,42,.07);
            text-align: center;
            transition: transform .3s, box-shadow .3s;
            height: 100%;
        }
        .cause-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(26,92,42,.12); }
        .cause-icon { font-size: 2rem; margin-bottom: .75rem; }
        .cause-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: .95rem; color: var(--primary); margin-bottom: .4rem; }
        .cause-text { font-size: .82rem; color: #6b7280; line-height: 1.55; }

        /* ── DONATION FORM ── */
        .don-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 4px 32px rgba(26,92,42,.1);
            border: 1px solid rgba(26,92,42,.06);
        }
        .don-card h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700; font-size: 1.4rem;
            color: var(--dark-bg); margin-bottom: .35rem;
        }
        .don-card .sub { color: #6b7280; font-size: .87rem; margin-bottom: 1.75rem; }

        .section-lbl {
            font-size: .78rem; font-weight: 700;
            color: var(--primary); text-transform: uppercase;
            letter-spacing: .6px; margin-bottom: .6rem;
        }
        .amount-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: .5rem; margin-bottom: 1rem; }
        .amount-btn {
            padding: .6rem .5rem;
            border: 1.5px solid rgba(26,92,42,.18);
            background: #fff; color: var(--primary);
            border-radius: 9px; font-weight: 700;
            font-size: .88rem; cursor: pointer;
            transition: all .2s; text-align: center;
        }
        .amount-btn:hover,
        .amount-btn.selected {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
        .amount-btn.selected { box-shadow: 0 2px 10px rgba(26,92,42,.25); }

        .form-control-don {
            border: 1.5px solid rgba(26,92,42,.18);
            border-radius: 10px; padding: .65rem 1rem;
            font-size: .9rem; width: 100%;
            transition: border .2s;
            outline: none;
        }
        .form-control-don:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(45,158,75,.12); }

        /* Gateway buttons */
        .gateway-grid { display: flex; flex-direction: column; gap: .75rem; margin-top: 1.5rem; }
        .gateway-btn {
            width: 100%; padding: .9rem 1.5rem;
            border: none; border-radius: 12px;
            font-weight: 700; font-size: .95rem;
            cursor: pointer; display: flex; align-items: center;
            justify-content: center; gap: .6rem;
            transition: all .25s; position: relative;
            overflow: hidden;
        }
        .gateway-btn::after {
            content: ''; position: absolute; inset: 0;
            background: rgba(255,255,255,0);
            transition: background .2s;
        }
        .gateway-btn:hover::after { background: rgba(255,255,255,.1); }
        .gateway-btn:active { transform: scale(.98); }

        .btn-paystack {
            background: linear-gradient(135deg, #0b6477, #00c3f7);
            color: #fff;
            box-shadow: 0 4px 16px rgba(0,195,247,.3);
        }
        .btn-paystack:hover { box-shadow: 0 6px 24px rgba(0,195,247,.45); transform: translateY(-1px); }

        .btn-flutterwave {
            background: linear-gradient(135deg, #f07700, #f5a623);
            color: #fff;
            box-shadow: 0 4px 16px rgba(245,166,35,.3);
        }
        .btn-flutterwave:hover { box-shadow: 0 6px 24px rgba(245,166,35,.45); transform: translateY(-1px); }

        .btn-stripe {
            background: linear-gradient(135deg, #3d37c5, #635bff);
            color: #fff;
            box-shadow: 0 4px 16px rgba(99,91,255,.3);
        }
        .btn-stripe:hover { box-shadow: 0 6px 24px rgba(99,91,255,.45); transform: translateY(-1px); }

        .gateway-logo { font-size: 1.1rem; }
        .gateway-text { font-size: .88rem; }
        .gateway-tag {
            margin-left: auto;
            background: rgba(255,255,255,.2);
            border-radius: 4px; padding: .15rem .5rem;
            font-size: .7rem; font-weight: 600;
        }

        /* ── SIDEBAR ── */
        .sidebar-card {
            background: var(--light-green);
            border-radius: 16px; padding: 1.75rem;
            border: 1.5px solid rgba(26,92,42,.1);
        }
        .sidebar-card h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700; font-size: 1rem;
            color: var(--primary); margin-bottom: 1rem;
        }
        .impact-item {
            display: flex; align-items: flex-start; gap: .75rem;
            margin-bottom: .85rem;
        }
        .impact-icon {
            width: 36px; height: 36px; flex-shrink: 0;
            background: #fff; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            box-shadow: 0 1px 6px rgba(26,92,42,.1);
        }
        .impact-text { font-size: .83rem; color: #2d5a3b; line-height: 1.5; }
        .impact-text strong { display: block; font-weight: 700; color: var(--primary); font-size: .85rem; }

        .trust-badges { margin-top: 1.5rem; }
        .trust-badge {
            display: flex; align-items: center; gap: .5rem;
            font-size: .78rem; color: #4b6e52;
            margin-bottom: .5rem;
        }
        .trust-badge i { color: var(--primary-light); }

        .secure-strip {
            background: #fff;
            border-radius: 10px; padding: .75rem 1rem;
            margin-top: 1rem;
            display: flex; align-items: center; gap: .5rem;
            font-size: .78rem; color: #4b6e52;
            border: 1px solid rgba(26,92,42,.1);
        }
        .secure-strip i { color: var(--primary-light); font-size: 1rem; }

        /* ── FOOTER ── */
        .don-footer {
            background: var(--dark-bg);
            padding: 1.5rem 0; text-align: center;
            color: rgba(255,255,255,.4); font-size: .8rem; margin-top: 4rem;
        }
        .don-footer span { color: var(--accent); }

        /* Alert */
        .alert-don {
            border-radius: 10px; padding: .75rem 1rem;
            margin-bottom: 1.25rem; font-size: .85rem;
        }
        .alert-error { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; }

        @media (max-width: 768px) {
            .don-card { padding: 1.5rem; }
            .amount-grid { grid-template-columns: repeat(3, 1fr); }
        }
    </style>
</head>
<body>

<!-- NAVIGATION -->
<nav id="mainNav" class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('auth.LandingPage') }}">
            <div class="brand-leaf">🌿</div>
            Olives <span class="brand-bi ms-1">BI</span>
        </a>

        <button class="navbar-toggler border-0 ms-auto me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('auth.LandingPage') }}#features">Features</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('auth.LandingPage') }}#coverage">Coverage</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('auth.LandingPage') }}#how-it-works">How It Works</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('auth.LandingPage') }}#mission">Mission</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('locality.check') }}">Check Locality</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('igf.index') }}" style="font-weight:600;">Business We Support</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('auth.LandingPage') }}#contact">Contact</a></li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="{{ route('donation.index') }}"
                       style="color:var(--accent) !important; font-weight:700;">
                        <i class="bi bi-heart-fill me-1" style="font-size:.72rem;"></i>Support
                    </a>
                </li>
            </ul>
            <div class="d-flex gap-2 mt-3 mt-lg-0">
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-nav-cta">Dashboard</a>
                @else
                    <a href="{{ route('auth.index') }}" class="btn btn-nav-login">Sign In</a>
                    <a href="{{ route('auth.register') }}" class="btn btn-nav-cta">Get Started</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- HERO STRIP -->
<div class="don-hero">
    <div class="container don-hero-content">
        <div class="cause-badge">
            <i class="bi bi-heart-fill"></i> Make a Difference
        </div>
        <h1>Support <span>Environmental</span> Protection</h1>
        <p>Your donation powers Olives BI's mission to safeguard forests, rivers, wetlands,
           and land for future generations — from local districts to the entire globe.</p>
    </div>
</div>

<!-- CAUSE CARDS -->
<div class="container" style="margin-top:-2rem; position:relative; z-index:5;">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="cause-card">
                <div class="cause-icon">🌲</div>
                <div class="cause-title">Forest Protection</div>
                <p class="cause-text">Fund monitoring of gazetted forest reserves against encroachment.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="cause-card">
                <div class="cause-icon">💧</div>
                <div class="cause-title">Clean Rivers</div>
                <p class="cause-text">Support river quality monitoring and pollution detection programs.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="cause-card">
                <div class="cause-icon">🦢</div>
                <div class="cause-title">Wetland Conservation</div>
                <p class="cause-text">Protect Ramsar and other critical wetland ecosystems from drainage.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="cause-card">
                <div class="cause-icon">🌍</div>
                <div class="cause-title">Climate Intelligence</div>
                <p class="cause-text">Expand our global coverage for flood zones and air quality mapping.</p>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container pb-5">

    @if (session('error'))
        <div class="alert-don alert-error mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-4 align-items-start">

        <!-- DONATION FORM -->
        <div class="col-lg-7">
            <div class="don-card">
                <h2>Choose Your Contribution</h2>
                <p class="sub">Every amount helps — 100% goes toward environmental monitoring and protection.</p>

                <!-- Amount selector -->
                <div class="section-lbl">Select Amount ({{ $currency }})</div>
                <div class="amount-grid" id="amountGrid">
                    <button class="amount-btn" data-amount="5">$5</button>
                    <button class="amount-btn selected" data-amount="10">$10</button>
                    <button class="amount-btn" data-amount="25">$25</button>
                    <button class="amount-btn" data-amount="50">$50</button>
                    <button class="amount-btn" data-amount="100">$100</button>
                    <button class="amount-btn" data-amount="custom">Custom</button>
                </div>

                <div id="customAmountWrap" style="display:none; margin-bottom:1rem;">
                    <label class="section-lbl">Enter Amount ({{ $currency }})</label>
                    <input type="number" id="customAmount" class="form-control-don"
                           placeholder="e.g. 200" min="1" step="0.01">
                </div>

                <hr style="border-color:rgba(26,92,42,.08); margin: 1.5rem 0;">

                <!-- Donor info -->
                <div class="row g-3 mb-2">
                    <div class="col-sm-6">
                        <label class="section-lbl">Your Name <span style="color:#9ca3af;">(optional)</span></label>
                        <input type="text" id="donorName" class="form-control-don" placeholder="Anonymous">
                    </div>
                    <div class="col-sm-6">
                        <label class="section-lbl">Email Address <span style="color:#e74c3c;">*</span></label>
                        <input type="email" id="donorEmail" class="form-control-don" placeholder="you@example.com">
                        <div id="emailError" style="display:none; color:#b91c1c; font-size:.78rem; margin-top:.3rem;">
                            Please enter a valid email address.
                        </div>
                    </div>
                </div>

                <!-- Payment gateways -->
                <div class="section-lbl mt-3">Pay Securely With</div>
                <div class="gateway-grid">

                    <!-- PAYSTACK -->
                    <button class="gateway-btn btn-paystack" onclick="payWithPaystack()">
                        <span class="gateway-logo">💳</span>
                        <span>
                            <span style="font-size:.7rem; opacity:.85; display:block; line-height:1;">Donate via</span>
                            <span class="gateway-text">Paystack</span>
                        </span>
                        <span class="gateway-tag">Cards · Mobile Money</span>
                    </button>

                    <!-- FLUTTERWAVE -->
                    <button class="gateway-btn btn-flutterwave" onclick="payWithFlutterwave()">
                        <span class="gateway-logo">🌊</span>
                        <span>
                            <span style="font-size:.7rem; opacity:.85; display:block; line-height:1;">Donate via</span>
                            <span class="gateway-text">Flutterwave</span>
                        </span>
                        <span class="gateway-tag">Cards · Bank Transfer</span>
                    </button>

                    <!-- STRIPE -->
                    <form id="stripeForm" action="{{ route('donation.stripe') }}" method="POST" style="margin:0;">
                        @csrf
                        <input type="hidden" name="email"  id="stripeEmail">
                        <input type="hidden" name="amount" id="stripeAmount">
                        <input type="hidden" name="name"   id="stripeName">
                        <button type="button" class="gateway-btn btn-stripe" onclick="payWithStripe()">
                            <span class="gateway-logo">💜</span>
                            <span>
                                <span style="font-size:.7rem; opacity:.85; display:block; line-height:1;">Donate via</span>
                                <span class="gateway-text">Stripe</span>
                            </span>
                            <span class="gateway-tag">Cards · Apple/Google Pay</span>
                        </button>
                    </form>

                </div><!-- /gateway-grid -->
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-5">
            <div class="sidebar-card">
                <h4>🌿 Where Your Money Goes</h4>

                <div class="impact-item">
                    <div class="impact-icon">🌲</div>
                    <div class="impact-text">
                        <strong>$5 — Forest Data Point</strong>
                        Adds one satellite-verified forest monitoring point to our database.
                    </div>
                </div>
                <div class="impact-item">
                    <div class="impact-icon">💧</div>
                    <div class="impact-text">
                        <strong>$10 — River Quality Sample</strong>
                        Funds one river cleanliness data collection and analysis cycle.
                    </div>
                </div>
                <div class="impact-item">
                    <div class="impact-icon">🦢</div>
                    <div class="impact-text">
                        <strong>$25 — Wetland Boundary Scan</strong>
                        Maps and validates one Ramsar wetland boundary for encroachment alerts.
                    </div>
                </div>
                <div class="impact-item">
                    <div class="impact-icon">🌍</div>
                    <div class="impact-text">
                        <strong>$100 — District Coverage</strong>
                        Fully activates environmental monitoring for an entire district.
                    </div>
                </div>

                <div class="trust-badges">
                    <div class="trust-badge"><i class="bi bi-shield-check-fill"></i> 100% of donations fund environmental work</div>
                    <div class="trust-badge"><i class="bi bi-lock-fill"></i> All payments are encrypted and secure</div>
                    <div class="trust-badge"><i class="bi bi-receipt"></i> Instant email receipt on completion</div>
                    <div class="trust-badge"><i class="bi bi-globe"></i> Supporting communities from district to globe</div>
                </div>

                <div class="secure-strip">
                    <i class="bi bi-shield-fill-check"></i>
                    Secured by Paystack, Flutterwave &amp; Stripe — PCI DSS compliant gateways.
                </div>
            </div>

            <!-- Social share nudge -->
            <div style="background:#fff; border-radius:14px; padding:1.25rem; margin-top:1rem; border:1.5px solid rgba(26,92,42,.07); text-align:center;">
                <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:.9rem; color:var(--primary); margin-bottom:.4rem;">
                    🌱 Spread the Word
                </div>
                <p style="font-size:.8rem; color:#6b7280; margin-bottom:.75rem; line-height:1.5;">
                    Can't donate? Share Olives BI and help us reach more environmental champions.
                </p>
                <a href="https://twitter.com/intent/tweet?text=Support+environmental+protection+with+Olives+BI+🌍"
                   target="_blank"
                   style="display:inline-flex; align-items:center; gap:.4rem; background:#1da1f2; color:#fff; border-radius:8px; padding:.4rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none;">
                    <i class="bi bi-twitter-x"></i> Share on X
                </a>
            </div>
        </div>

    </div>
</div>

<!-- FOOTER -->
<footer class="don-footer">
    <span>🌿 Olives BI</span> — Environmental Protection Intelligence &nbsp;·&nbsp;
    © {{ date('Y') }} All rights reserved.
</footer>

<!-- Bootstrap JS (for mobile nav toggle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Payment SDKs -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>

<script>
    const PAYSTACK_KEY    = '{{ $paystackPublicKey }}';
    const FLUTTERWAVE_KEY = '{{ $flutterwavePublicKey }}';
    const CURRENCY        = '{{ $currency }}';

    let selectedAmount = 10;

    // ── Amount selector
    document.getElementById('amountGrid').addEventListener('click', e => {
        const btn = e.target.closest('.amount-btn');
        if (!btn) return;

        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');

        const wrap = document.getElementById('customAmountWrap');
        if (btn.dataset.amount === 'custom') {
            wrap.style.display = 'block';
            selectedAmount = null;
        } else {
            wrap.style.display = 'none';
            selectedAmount = parseFloat(btn.dataset.amount);
        }
    });

    document.getElementById('customAmount').addEventListener('input', e => {
        selectedAmount = parseFloat(e.target.value) || null;
    });

    // ── Validation helper
    function validateForm() {
        const email = document.getElementById('donorEmail').value.trim();
        const emailErr = document.getElementById('emailError');
        const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRe.test(email)) {
            emailErr.style.display = 'block';
            document.getElementById('donorEmail').focus();
            return false;
        }
        emailErr.style.display = 'none';

        if (!selectedAmount || selectedAmount < 1) {
            alert('Please enter an amount of at least 1 ' + CURRENCY + '.');
            return false;
        }
        return true;
    }

    function getDonorInfo() {
        return {
            email:  document.getElementById('donorEmail').value.trim(),
            name:   document.getElementById('donorName').value.trim() || 'Anonymous',
            amount: selectedAmount,
        };
    }

    // ── PAYSTACK
    function payWithPaystack() {
        if (!validateForm()) return;
        const { email, name, amount } = getDonorInfo();

        if (!PAYSTACK_KEY) {
            alert('Paystack is not configured. Please contact the administrator.');
            return;
        }

        const handler = PaystackPop.setup({
            key:       PAYSTACK_KEY,
            email:     email,
            amount:    Math.round(amount * 100),   // smallest unit
            currency:  CURRENCY,
            ref:       'olivesbi-ps-' + Date.now(),
            metadata:  { custom_fields: [{ display_name: 'Donor Name', variable_name: 'donor_name', value: name }] },
            callback: function(response) {
                window.location.href = '{{ route("donation.verify.paystack") }}?reference=' + response.reference;
            },
            onClose: function() {}
        });
        handler.openIframe();
    }

    // ── FLUTTERWAVE
    function payWithFlutterwave() {
        if (!validateForm()) return;
        const { email, name, amount } = getDonorInfo();

        if (!FLUTTERWAVE_KEY) {
            alert('Flutterwave is not configured. Please contact the administrator.');
            return;
        }

        const txRef = 'olivesbi-fw-' + Date.now();

        FlutterwaveCheckout({
            public_key:  FLUTTERWAVE_KEY,
            tx_ref:      txRef,
            amount:      amount,
            currency:    CURRENCY,
            customer: { email, name },
            customizations: {
                title:       'Olives BI Environmental Donation',
                description: 'Protecting forests, wetlands, rivers and land.',
                logo:        '{{ asset("logos/logo.png") }}',
            },
            callback: function(data) {
                window.location.href = '{{ route("donation.verify.flutterwave") }}'
                    + '?status='         + data.status
                    + '&tx_ref='         + encodeURIComponent(data.tx_ref)
                    + '&transaction_id=' + data.transaction_id;
            },
            onclose: function() {}
        });
    }

    // ── STRIPE (server-side session)
    function payWithStripe() {
        if (!validateForm()) return;
        const { email, name, amount } = getDonorInfo();

        if (!{{ $stripePublicKey ? 'true' : 'false' }}) {
            alert('Stripe is not configured. Please contact the administrator.');
            return;
        }

        document.getElementById('stripeEmail').value  = email;
        document.getElementById('stripeAmount').value = amount;
        document.getElementById('stripeName').value   = name;
        document.getElementById('stripeForm').submit();
    }
</script>

</body>
</html>

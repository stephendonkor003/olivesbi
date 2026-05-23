<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.public-seo', [
        'seoTitle' => 'IGF & Land Revenue Intelligence for Local Governments | Olives BI',
        'seoDescription' => 'See how Olives BI supports internally generated funds with land revenue intelligence for building permits, business licences, property rates, market tolls, land registration, and GIS-backed compliance.',
        'seoKeywords' => 'IGF software Ghana, local government revenue platform, land revenue intelligence, property rates software, building permit revenue, business licence management, district assembly revenue, GIS revenue system',
        'seoUrl' => route('igf.index'),
        'seoMainEntity' => [
            '@type' => 'Service',
            'name' => 'Olives BI IGF and Land Revenue Intelligence',
            'serviceType' => 'Local government revenue intelligence and GIS platform',
            'provider' => [
                '@type' => 'Organization',
                'name' => 'Olives BI',
            ],
            'areaServed' => 'Ghana',
        ],
    ])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:       #1a5c2a;
            --primary-light: #2d9e4b;
            --accent:        #4cc76a;
            --accent-soft:   #a8e6bc;
            --light-green:   #e8f5e9;
            --dark-bg:       #071a0d;
            --muted:         #6b7280;
            --gold:          #d4a017;
            --gold-light:    #fef3c7;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: #f0f7f1; color: #1a2e1e; }

        /* ── NAVBAR ── */
        .igf-nav {
            position: sticky; top: 0; z-index: 1000;
            background: rgba(7,26,13,.97); backdrop-filter: blur(10px);
            padding: .65rem 0; box-shadow: 0 2px 20px rgba(0,0,0,.25);
        }
        .igf-nav .brand { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.3rem; color:#fff; text-decoration:none; display:flex; align-items:center; gap:.5rem; }
        .igf-nav .brand .leaf { width:32px; height:32px; background:linear-gradient(135deg,var(--accent),var(--primary-light)); border-radius:50% 10px 50% 10px; display:flex; align-items:center; justify-content:center; font-size:1rem; }
        .igf-nav .brand em { color:var(--accent); font-style:normal; }
        .igf-nav-links { display:flex; align-items:center; gap:.2rem; }
        .igf-nav-links a { color:rgba(255,255,255,.75); font-size:.87rem; font-weight:500; padding:.35rem .85rem; border-radius:6px; text-decoration:none; transition:all .2s; }
        .igf-nav-links a:hover { color:var(--accent); background:rgba(255,255,255,.07); }
        .igf-nav-links a.active { color:var(--accent); font-weight:700; }
        .btn-nav-signin { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:rgba(255,255,255,.85); border-radius:8px; padding:.35rem 1rem; font-size:.83rem; font-weight:500; text-decoration:none; transition:all .2s; }
        .btn-nav-signin:hover { background:rgba(255,255,255,.18); color:#fff; }

        /* ── Mobile hamburger button ── */
        .nav-mob-toggle {
            display: none;
            align-items: center; justify-content: center;
            width: 40px; height: 40px;
            background: rgba(255,255,255,.08);
            border: 1.5px solid rgba(255,255,255,.2);
            border-radius: 10px; cursor: pointer;
            color: #fff; font-size: 1.2rem;
            transition: background .2s, border-color .2s;
            flex-shrink: 0;
        }
        .nav-mob-toggle:hover { background: rgba(255,255,255,.15); border-color: var(--accent); }
        .nav-mob-toggle.open  { background: rgba(76,199,106,.15); border-color: var(--accent); color: var(--accent); }

        /* ── Mobile drawer ── */
        .nav-mob-menu {
            max-height: 0; overflow: hidden;
            transition: max-height .38s cubic-bezier(.4,0,.2,1), opacity .28s ease;
            opacity: 0;
            background: rgba(5,20,10,.99);
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .nav-mob-menu.open { max-height: 500px; opacity: 1; }

        .mob-menu-inner {
            padding: .5rem .25rem 1rem;
        }
        .mob-nav-item {
            display: flex; align-items: center; gap: .85rem;
            padding: .72rem 1rem; border-radius: 12px; margin-bottom: .2rem;
            color: rgba(255,255,255,.78); font-size: .92rem; font-weight: 500;
            text-decoration: none; transition: background .18s, color .18s;
        }
        .mob-nav-item:hover { background: rgba(255,255,255,.07); color: #fff; }
        .mob-nav-item.active { background: rgba(76,199,106,.12); color: var(--accent); font-weight: 700; }
        .mob-nav-item .mni-icon {
            width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
            background: rgba(255,255,255,.07);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .mob-nav-item.active .mni-icon { background: rgba(76,199,106,.18); }
        .mob-nav-sep {
            height: 1px; background: rgba(255,255,255,.07);
            margin: .6rem 1rem;
        }
        .mob-nav-cta {
            margin: .75rem 1rem 0;
            display: flex; gap: .6rem;
        }
        .mob-nav-cta a {
            flex: 1; text-align: center; border-radius: 10px;
            padding: .65rem .5rem; font-size: .85rem; font-weight: 700;
            text-decoration: none; transition: all .2s;
        }
        .mob-cta-signin { background: rgba(255,255,255,.08); border: 1.5px solid rgba(255,255,255,.18); color: rgba(255,255,255,.85); }
        .mob-cta-signin:hover { background: rgba(255,255,255,.15); color: #fff; }
        .mob-cta-start  { background: linear-gradient(135deg,var(--primary-light),var(--accent)); color: #fff; border: none; }
        .mob-cta-start:hover { box-shadow: 0 4px 14px rgba(76,199,106,.35); }

        @media (max-width: 768px) {
            .igf-nav-links { display: none !important; }
            .btn-nav-signin { display: none !important; }
            .nav-mob-toggle { display: inline-flex; }
        }

        /* ── HERO ── */
        .igf-hero {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #0f3d1a 60%, #1a5c2a 100%);
            position: relative; overflow: hidden; padding: 4.5rem 0 3.5rem;
        }
        .igf-hero::before {
            content: ''; position: absolute; inset: 0;
            background: url('https://images.unsplash.com/photo-1486325212027-8081e485255e?auto=format&fit=crop&w=1600&q=30') center/cover;
            opacity: .1;
        }
        .igf-hero-inner { position: relative; z-index: 2; }
        .igf-badge { display:inline-flex; align-items:center; gap:.4rem; background:rgba(212,160,23,.15); border:1px solid rgba(212,160,23,.4); color:#fde68a; border-radius:50px; padding:.3rem .9rem; font-size:.75rem; font-weight:700; letter-spacing:.5px; text-transform:uppercase; margin-bottom:.9rem; }
        .igf-hero h1 { font-family:'Poppins',sans-serif; font-weight:900; font-size:clamp(2rem,5vw,3.2rem); color:#fff; line-height:1.1; margin-bottom:.75rem; }
        .igf-hero h1 span { color:var(--accent); }
        .igf-hero p { color:rgba(255,255,255,.7); font-size:1.05rem; max-width:580px; line-height:1.75; margin-bottom:1.75rem; }
        .hero-stats { display:flex; flex-wrap:wrap; gap:1.5rem; }
        .hero-stat { text-align:center; }
        .hero-stat .num { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.8rem; color:var(--accent); line-height:1; }
        .hero-stat .lbl { font-size:.75rem; color:rgba(255,255,255,.6); margin-top:.15rem; }

        /* ── SECTION HEADERS ── */
        .sec-label { display:inline-flex; align-items:center; gap:.4rem; font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.65px; color:var(--primary); background:var(--light-green); border-radius:50px; padding:.25rem .85rem; margin-bottom:.6rem; }
        .sec-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:clamp(1.5rem,3vw,2.1rem); color:#1a2e1e; margin-bottom:.5rem; }
        .sec-title span { color:var(--primary); }
        .sec-sub { color:var(--muted); font-size:.96rem; max-width:600px; line-height:1.7; }

        /* ── IGF STREAM CARDS ── */
        .stream-card {
            background: #fff; border-radius: 18px;
            border: 1.5px solid rgba(26,92,42,.08);
            padding: 1.75rem; height: 100%;
            transition: transform .3s, box-shadow .3s;
            position: relative; overflow: hidden;
        }
        .stream-card:hover { transform: translateY(-5px); box-shadow: 0 12px 40px rgba(26,92,42,.13); }
        .stream-card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,var(--primary),var(--accent)); }
        .stream-card.gold::before { background:linear-gradient(90deg,#b45309,var(--gold)); }
        .stream-card.blue::before { background:linear-gradient(90deg,#1565c0,#42a5f5); }
        .stream-card.purple::before { background:linear-gradient(90deg,#6d28d9,#a78bfa); }
        .stream-card.red::before  { background:linear-gradient(90deg,#b91c1c,#f87171); }
        .stream-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:1rem; }
        .stream-icon.green  { background:var(--light-green); }
        .stream-icon.gold   { background:#fef3c7; }
        .stream-icon.blue   { background:#e3f2fd; }
        .stream-icon.purple { background:#f3e8ff; }
        .stream-icon.red    { background:#fce4ec; }
        .stream-title { font-family:'Poppins',sans-serif; font-weight:700; font-size:1.05rem; color:#1a2e1e; margin-bottom:.4rem; }
        .stream-desc { font-size:.85rem; color:var(--muted); line-height:1.65; margin-bottom:.9rem; }
        .stream-items { list-style:none; margin:0; padding:0; }
        .stream-items li { font-size:.82rem; color:#374151; padding:.3rem 0; border-top:1px solid rgba(26,92,42,.06); display:flex; align-items:center; gap:.45rem; }
        .stream-items li:first-child { border-top:0; }
        .stream-items li::before { content:'✓'; color:var(--primary-light); font-weight:800; flex-shrink:0; }

        /* ── HOW IT WORKS ── */
        .how-step {
            display:flex; gap:1.25rem; align-items:flex-start;
            padding:1.5rem; background:#fff; border-radius:16px;
            border:1.5px solid rgba(26,92,42,.08);
            margin-bottom:1rem;
        }
        .how-step:last-child { margin-bottom:0; }
        .step-num { width:44px; height:44px; background:linear-gradient(135deg,var(--primary),var(--primary-light)); border-radius:12px; display:flex; align-items:center; justify-content:center; font-family:'Poppins',sans-serif; font-weight:800; font-size:1.1rem; color:#fff; flex-shrink:0; }
        .step-body h4 { font-family:'Poppins',sans-serif; font-weight:700; font-size:.95rem; color:#1a2e1e; margin-bottom:.3rem; }
        .step-body p  { font-size:.83rem; color:var(--muted); line-height:1.6; margin:0; }

        /* ── IMPACT NUMBERS ── */
        .impact-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
        .impact-num { background:linear-gradient(135deg,var(--primary),var(--primary-light)); border-radius:16px; padding:1.75rem 1rem; text-align:center; color:#fff; }
        .impact-num .big { font-family:'Poppins',sans-serif; font-weight:800; font-size:2.2rem; line-height:1; }
        .impact-num .desc { font-size:.78rem; opacity:.8; margin-top:.35rem; line-height:1.4; }

        /* ── RESEARCH SECTION ── */
        .research-card { background:var(--light-green); border-radius:16px; padding:2rem; border:1.5px solid rgba(26,92,42,.1); }
        .research-item { display:flex; gap:.9rem; align-items:flex-start; margin-bottom:.85rem; }
        .research-item:last-child { margin-bottom:0; }
        .ri-icon { width:38px; height:38px; background:#fff; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; box-shadow:0 1px 6px rgba(26,92,42,.1); }
        .ri-text strong { display:block; font-size:.85rem; font-weight:700; color:var(--primary); }
        .ri-text span   { font-size:.8rem; color:var(--muted); line-height:1.5; }

        /* ── CTA ── */
        .igf-cta { background:linear-gradient(135deg,var(--dark-bg),#0f3d1a); border-radius:24px; padding:3rem 2rem; text-align:center; color:#fff; }
        .igf-cta h2 { font-family:'Poppins',sans-serif; font-weight:800; font-size:clamp(1.5rem,3vw,2.2rem); margin-bottom:.75rem; }
        .igf-cta h2 span { color:var(--accent); }
        .igf-cta p { color:rgba(255,255,255,.7); font-size:.95rem; max-width:520px; margin:0 auto 1.75rem; line-height:1.7; }
        .btn-cta-primary { background:linear-gradient(135deg,var(--primary-light),var(--accent)); color:#fff; border:none; border-radius:10px; padding:.8rem 2rem; font-weight:700; font-size:.95rem; text-decoration:none; display:inline-flex; align-items:center; gap:.5rem; transition:all .25s; }
        .btn-cta-primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(76,199,106,.4); color:#fff; }
        .btn-cta-ghost { background:transparent; border:1.5px solid rgba(255,255,255,.3); color:rgba(255,255,255,.85); border-radius:10px; padding:.8rem 1.75rem; font-weight:600; font-size:.95rem; text-decoration:none; display:inline-flex; align-items:center; gap:.5rem; transition:all .25s; }
        .btn-cta-ghost:hover { border-color:var(--accent); color:var(--accent); }

        /* ── REGISTER SECTION ── */
        .reg-section { background: linear-gradient(180deg, #f0f7f1 0%, #e8f5e9 100%); padding: 5rem 0; }
        .reg-section .sec-title { font-size: clamp(1.4rem, 2.8vw, 2rem); }

        /* Benefits list */
        .reg-benefit { display:flex; align-items:flex-start; gap:.9rem; margin-bottom:1.1rem; }
        .rb-icon { width:42px; height:42px; background:#fff; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; box-shadow:0 2px 10px rgba(26,92,42,.1); }
        .rb-text strong { display:block; font-size:.88rem; font-weight:700; color:var(--primary); }
        .rb-text span   { font-size:.82rem; color:var(--muted); line-height:1.5; }

        /* Form card */
        .reg-card {
            background: #fff; border-radius: 20px; padding: 2.25rem;
            box-shadow: 0 8px 48px rgba(26,92,42,.13);
            border: 1.5px solid rgba(26,92,42,.08);
            position: sticky; top: 90px;
        }
        .reg-card-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.2rem; color:#1a2e1e; margin-bottom:.25rem; }
        .reg-card-sub   { font-size:.83rem; color:var(--muted); margin-bottom:1.5rem; line-height:1.5; }

        /* Form elements */
        .rf-group { margin-bottom:1rem; }
        .rf-label {
            display:block; font-size:.72rem; font-weight:700;
            text-transform:uppercase; letter-spacing:.5px;
            color:var(--primary); margin-bottom:.35rem;
        }
        .rf-label .req { color:#e74c3c; margin-left:.15rem; }
        .rf-input, .rf-select {
            width:100%; border:1.5px solid rgba(26,92,42,.18); border-radius:10px;
            padding:.65rem .9rem; font-size:.88rem; color:#1a2e1e;
            outline:none; transition:border .2s, box-shadow .2s;
            font-family:'Inter',sans-serif; background:#fff;
        }
        .rf-input:focus, .rf-select:focus {
            border-color:var(--primary-light);
            box-shadow:0 0 0 3px rgba(45,158,75,.12);
        }
        .rf-input.error { border-color:#e74c3c; }
        .rf-error { font-size:.74rem; color:#e74c3c; margin-top:.28rem; display:none; }
        .rf-error.show { display:block; }
        .rf-hint { font-size:.73rem; color:var(--muted); margin-top:.28rem; }

        /* Password strength */
        .pw-strength { display:flex; gap:.25rem; margin-top:.4rem; }
        .pw-bar { flex:1; height:4px; border-radius:2px; background:rgba(26,92,42,.1); transition:background .3s; }
        .pw-bar.weak { background:#e74c3c; }
        .pw-bar.fair { background:#f59e0b; }
        .pw-bar.good { background:var(--primary-light); }
        .pw-bar.strong { background:var(--primary); }

        /* Submit button */
        .btn-register {
            width:100%; padding:.85rem; border:none; border-radius:11px;
            background:linear-gradient(135deg,var(--primary),var(--primary-light));
            color:#fff; font-family:'Poppins',sans-serif; font-weight:700;
            font-size:.95rem; cursor:pointer; display:flex; align-items:center;
            justify-content:center; gap:.5rem; transition:all .25s;
            margin-top:.5rem;
        }
        .btn-register:hover { box-shadow:0 6px 22px rgba(26,92,42,.35); transform:translateY(-1px); }
        .btn-register:disabled { opacity:.65; cursor:not-allowed; transform:none; }

        /* Divider */
        .rf-divider { display:flex; align-items:center; gap:.75rem; margin:.85rem 0; }
        .rf-divider::before, .rf-divider::after { content:''; flex:1; height:1px; background:rgba(26,92,42,.1); }
        .rf-divider span { font-size:.72rem; color:var(--muted); white-space:nowrap; }

        /* Success state */
        .reg-success {
            display:none; text-align:center; padding:1rem 0;
        }
        .reg-success.show { display:block; }
        .reg-form-wrap.hide { display:none; }
        .success-circle {
            width:72px; height:72px; border-radius:50%;
            background:linear-gradient(135deg,var(--primary),var(--accent));
            display:flex; align-items:center; justify-content:center;
            font-size:2rem; margin:0 auto 1rem;
            animation:successPop .5s cubic-bezier(.34,1.56,.64,1);
        }
        @keyframes successPop { from { transform:scale(0); opacity:0; } to { transform:scale(1); opacity:1; } }
        .success-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.2rem; color:var(--primary); margin-bottom:.4rem; }
        .success-email { font-size:.83rem; color:var(--muted); line-height:1.6; }
        .success-email strong { color:var(--primary); }

        /* Step tabs */
        .reg-steps { display:flex; gap:0; margin-bottom:1.5rem; border-bottom:2px solid rgba(26,92,42,.08); }
        .reg-step-btn {
            flex:1; padding:.5rem .25rem; text-align:center;
            font-size:.72rem; font-weight:700; color:var(--muted);
            border-bottom:2.5px solid transparent; margin-bottom:-2px;
            cursor:default; display:flex; flex-direction:column; align-items:center; gap:.2rem;
        }
        .reg-step-btn .sn { width:22px; height:22px; border-radius:50%; background:rgba(26,92,42,.1); color:var(--muted); font-size:.7rem; display:flex; align-items:center; justify-content:center; font-weight:800; }
        .reg-step-btn.active { color:var(--primary); border-bottom-color:var(--primary-light); }
        .reg-step-btn.active .sn { background:var(--primary); color:#fff; }
        .reg-step-btn.done .sn { background:var(--accent); color:#fff; }
        .reg-step-page { display:none; }
        .reg-step-page.active { display:block; }
        .btn-step { height:42px; border:none; border-radius:9px; padding:0 1.2rem; font-weight:700; font-size:.85rem; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; }
        .btn-step-next { background:linear-gradient(135deg,var(--primary),var(--primary-light)); color:#fff; }
        .btn-step-next:hover { box-shadow:0 4px 14px rgba(26,92,42,.3); }
        .btn-step-back { background:var(--light-green); color:var(--primary); }

        /* ── FOOTER ── */
        .igf-footer { background:var(--dark-bg); padding:1.5rem 0; text-align:center; color:rgba(255,255,255,.35); font-size:.8rem; margin-top:4rem; }
        .igf-footer span { color:var(--accent); }

        @media (max-width:768px) {
            .impact-grid { grid-template-columns:repeat(2,1fr); }
            #health-risks-grid { grid-template-columns:repeat(2,1fr); }
        }
        @media (max-width:480px) {
            .impact-grid { grid-template-columns:1fr 1fr; }
            .hero-stats { gap:1rem; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="igf-nav">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <a href="{{ route('auth.LandingPage') }}" class="brand">
            <div class="leaf">🌿</div>
            Olives <em>BI</em>
        </a>
        <div class="igf-nav-links">
            <a href="{{ route('auth.LandingPage') }}">Home</a>
            <a href="{{ route('auth.LandingPage') }}#features">Features</a>
            <a href="{{ route('locality.check') }}">Check Locality</a>
            <a href="{{ route('igf.index') }}" class="active">Business We Support</a>
            <a href="{{ route('donation.index') }}" style="color:var(--accent); font-weight:700;">
                <i class="bi bi-heart-fill" style="font-size:.7rem;"></i> Support
            </a>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('auth.index') }}" class="btn-nav-signin">Sign In</a>
            <button class="nav-mob-toggle" id="navToggle" type="button" aria-label="Open menu">
                <i class="bi bi-list" id="navToggleIcon"></i>
            </button>
        </div>
    </div>

    <!-- Mobile drawer -->
    <div class="nav-mob-menu" id="navMobMenu">
        <div class="container">
            <div class="mob-menu-inner">
                <a href="{{ route('auth.LandingPage') }}" class="mob-nav-item">
                    <div class="mni-icon"><i class="bi bi-house-fill"></i></div>
                    Home
                </a>
                <a href="{{ route('auth.LandingPage') }}#features" class="mob-nav-item">
                    <div class="mni-icon"><i class="bi bi-stars"></i></div>
                    Features
                </a>
                <a href="{{ route('locality.check') }}" class="mob-nav-item">
                    <div class="mni-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    Check Locality
                </a>
                <a href="{{ route('igf.index') }}" class="mob-nav-item active">
                    <div class="mni-icon"><i class="bi bi-building-fill-gear"></i></div>
                    Business We Support
                </a>
                <a href="{{ route('donation.index') }}" class="mob-nav-item">
                    <div class="mni-icon"><i class="bi bi-heart-fill" style="color:#f87171;"></i></div>
                    Support the Mission
                </a>

                <div class="mob-nav-sep"></div>

                <div class="mob-nav-cta">
                    <a href="{{ route('auth.index') }}" class="mob-cta-signin">Sign In</a>
                    <a href="#register" class="mob-cta-start">Register Assembly</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- ── HERO ── -->
<section class="igf-hero">
    <div class="container igf-hero-inner">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="igf-badge"><i class="bi bi-building-fill-gear"></i> IGF &amp; Land Revenue Intelligence</div>
                <h1>Turning <span>Land Data</span> into Government Revenue</h1>
                <p>Olives BI empowers Assemblies, Municipalities, and District governments to unlock Internally Generated Funds through geospatial intelligence — from land registration and building permits to market tolls, fines, and business licencing.</p>
                <div class="hero-stats">
                    <div class="hero-stat"><div class="num">8+</div><div class="lbl">IGF Revenue Streams</div></div>
                    <div class="hero-stat"><div class="num">100%</div><div class="lbl">Land-Data Driven</div></div>
                    <div class="hero-stat"><div class="num">GIS</div><div class="lbl">Powered Compliance</div></div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem; max-width:280px;">
                    <div style="background:rgba(255,255,255,.08); border-radius:14px; padding:1.1rem; text-align:center; border:1px solid rgba(255,255,255,.12);">
                        <div style="font-size:1.8rem;">🏗️</div>
                        <div style="font-size:.72rem; color:rgba(255,255,255,.7); margin-top:.4rem;">Building Permits</div>
                    </div>
                    <div style="background:rgba(255,255,255,.08); border-radius:14px; padding:1.1rem; text-align:center; border:1px solid rgba(255,255,255,.12);">
                        <div style="font-size:1.8rem;">📋</div>
                        <div style="font-size:.72rem; color:rgba(255,255,255,.7); margin-top:.4rem;">Land Registration</div>
                    </div>
                    <div style="background:rgba(255,255,255,.08); border-radius:14px; padding:1.1rem; text-align:center; border:1px solid rgba(255,255,255,.12);">
                        <div style="font-size:1.8rem;">🏪</div>
                        <div style="font-size:.72rem; color:rgba(255,255,255,.7); margin-top:.4rem;">Business Permits</div>
                    </div>
                    <div style="background:rgba(255,255,255,.08); border-radius:14px; padding:1.1rem; text-align:center; border:1px solid rgba(255,255,255,.12);">
                        <div style="font-size:1.8rem;">⚖️</div>
                        <div style="font-size:.72rem; color:rgba(255,255,255,.7); margin-top:.4rem;">Fines &amp; Levies</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── IGF REVENUE STREAMS ── -->
<section style="padding:5rem 0 3rem;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="sec-label"><i class="bi bi-coin"></i> Revenue Streams</div>
            <h2 class="sec-title">How Land Data Generates <span>IGF Revenue</span></h2>
            <p class="sec-sub mx-auto">Every parcel, building, business, and market stall represents a revenue opportunity. Olives BI maps, tracks, and enforces compliance across all IGF categories using geospatial data.</p>
        </div>

        <div class="row g-4">

            <!-- Building Permits -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card">
                    <div class="stream-icon green">🏗️</div>
                    <div class="stream-title">Building Permits &amp; Development Control</div>
                    <p class="stream-desc">Detect unauthorised structures, enforce setbacks, and ensure all construction activities pay the correct development levy before breaking ground.</p>
                    <ul class="stream-items">
                        <li>Satellite-detect new construction without permits</li>
                        <li>Map permit applications against land parcels</li>
                        <li>Track levy payments by zone and land use type</li>
                        <li>Flag structures in protected or restricted zones</li>
                    </ul>
                </div>
            </div>

            <!-- Land Registration -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card gold">
                    <div class="stream-icon gold">📋</div>
                    <div class="stream-title">Land Registration &amp; Titling</div>
                    <p class="stream-desc">A complete parcel registry linked to owners, valuations, and transaction history. Close the gap between registered land and actual land use to boost stamp duty and registration fees.</p>
                    <ul class="stream-items">
                        <li>GIS-linked parcel boundaries and ownership records</li>
                        <li>Track unregistered parcels and prompt formalization</li>
                        <li>Automated stamp duty and registration fee invoicing</li>
                        <li>Historical transaction overlay for valuation accuracy</li>
                    </ul>
                </div>
            </div>

            <!-- Business Permits -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card blue">
                    <div class="stream-icon blue">🏪</div>
                    <div class="stream-title">Business Operating Permits</div>
                    <p class="stream-desc">Know every business operating in your jurisdiction. Olives BI cross-references OSM business data and satellite imagery to identify unlicensed operators.</p>
                    <ul class="stream-items">
                        <li>Map all operating businesses per district</li>
                        <li>Identify businesses without valid licences</li>
                        <li>Track renewal dates and send automated reminders</li>
                        <li>Category-based fee schedule enforcement</li>
                    </ul>
                </div>
            </div>

            <!-- Market Tolls -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card gold">
                    <div class="stream-icon gold">🛒</div>
                    <div class="stream-title">Market Tolls &amp; Stall Management</div>
                    <p class="stream-desc">Digitise market toll collection. Map every stall, kiosk, and temporary vendor footprint to ensure fair, complete toll capture across all market days.</p>
                    <ul class="stream-items">
                        <li>GPS-mapped stall locations and dimensions</li>
                        <li>Daily, weekly, and monthly toll schedules</li>
                        <li>Mobile-friendly toll payment confirmation</li>
                        <li>Identify ghost stalls and uncollected tolls</li>
                    </ul>
                </div>
            </div>

            <!-- Property Rates -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card purple">
                    <div class="stream-icon purple">🏘️</div>
                    <div class="stream-title">Property Rates &amp; Valuation</div>
                    <p class="stream-desc">Accurate property valuation drives accurate property rates. Olives BI links parcel data, building footprints, and land use classifications to produce defensible valuations.</p>
                    <ul class="stream-items">
                        <li>Automated mass appraisal via land use zones</li>
                        <li>Flag properties with outdated valuations</li>
                        <li>Track arrears and enforcement actions by parcel</li>
                        <li>Compare rates across comparable properties</li>
                    </ul>
                </div>
            </div>

            <!-- Fines & Levies -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card red">
                    <div class="stream-icon red">⚖️</div>
                    <div class="stream-title">Fines, Levies &amp; Environmental Fees</div>
                    <p class="stream-desc">Environmental compliance enforcement is a legitimate revenue source. Issue fines for illegal dumping, encroachment on reserves, and non-compliant waste management — backed by geo-evidence.</p>
                    <ul class="stream-items">
                        <li>GPS-tagged enforcement actions and evidence</li>
                        <li>Encroachment on Ramsar / forest reserves</li>
                        <li>Illegal dumping fines with location timestamps</li>
                        <li>Repeat offender tracking by parcel and owner</li>
                    </ul>
                </div>
            </div>

            <!-- Road and Infra Levies -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card">
                    <div class="stream-icon green">🛣️</div>
                    <div class="stream-title">Road &amp; Infrastructure Development Levies</div>
                    <p class="stream-desc">Properties and businesses that benefit from public infrastructure pay their fair share. Map road frontages, utility connections, and development impact zones to apportion levies fairly.</p>
                    <ul class="stream-items">
                        <li>Road frontage calculation per parcel</li>
                        <li>Infrastructure benefit zone delineation</li>
                        <li>Developer impact fee assessment</li>
                        <li>Track levy collection vs infrastructure investment</li>
                    </ul>
                </div>
            </div>

            <!-- Natural Resource Royalties -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card gold">
                    <div class="stream-icon gold">🌳</div>
                    <div class="stream-title">Natural Resource &amp; Land-Use Royalties</div>
                    <p class="stream-desc">Forest concessions, quarry licences, sand winning permits, and water abstraction rights all generate royalties. Olives BI maps active and lapsed concessions to prevent revenue leakage.</p>
                    <ul class="stream-items">
                        <li>Active concession boundary monitoring</li>
                        <li>Sand winning and quarry licence tracking</li>
                        <li>Water abstraction permit compliance</li>
                        <li>Alert on unlicensed resource extraction</li>
                    </ul>
                </div>
            </div>

            <!-- Research & Data Licensing -->
            <div class="col-md-6 col-lg-4">
                <div class="stream-card blue">
                    <div class="stream-icon blue">🔬</div>
                    <div class="stream-title">Research Data Licensing &amp; GIS Services</div>
                    <p class="stream-desc">Governments hold valuable geospatial datasets. Olives BI helps package and license land, environmental, and demographic data to researchers, developers, and NGOs — turning public data into a sustainable revenue stream.</p>
                    <ul class="stream-items">
                        <li>Parcel and boundary data licensing to universities</li>
                        <li>Environmental baseline data for EIA consultants</li>
                        <li>Developer access APIs for planning portals</li>
                        <li>NGO and INGO spatial data subscriptions</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── IMPACT NUMBERS ── -->
<section style="background:var(--light-green); padding:4rem 0;">
    <div class="container">
        <div class="impact-grid">
            <div class="impact-num">
                <div class="big">30–60%</div>
                <div class="desc">Typical IGF increase when land data is formalised</div>
            </div>
            <div class="impact-num">
                <div class="big">8+</div>
                <div class="desc">Distinct revenue streams activatable from one platform</div>
            </div>
            <div class="impact-num">
                <div class="big">3×</div>
                <div class="desc">More properties discovered vs manual field surveys</div>
            </div>
            <div class="impact-num">
                <div class="big">Real-time</div>
                <div class="desc">Compliance alerts when new structures appear on satellite</div>
            </div>
        </div>
    </div>
</section>

<!-- ── HOW IT WORKS ── -->
<section style="padding:5rem 0;">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <div class="sec-label"><i class="bi bi-diagram-3"></i> The Process</div>
                <h2 class="sec-title">How Olives BI Powers <span>Assembly IGF</span></h2>
                <p class="sec-sub">A four-step cycle that turns raw land intelligence into verified revenue — with full auditability at every step.</p>

                <div class="mt-4">
                    <div class="how-step">
                        <div class="step-num">1</div>
                        <div class="step-body">
                            <h4>Map &amp; Discover</h4>
                            <p>Satellite imagery, OSM data, and field surveys build a comprehensive picture of every property, business, and land use within the jurisdiction — including those invisible to manual census.</p>
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="step-num">2</div>
                        <div class="step-body">
                            <h4>Verify &amp; Classify</h4>
                            <p>Each discovered asset is cross-referenced against the current permit and registration database. Gaps — unpermitted buildings, unlicensed businesses, lapsed concessions — are flagged automatically.</p>
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="step-num">3</div>
                        <div class="step-body">
                            <h4>Invoice &amp; Collect</h4>
                            <p>Compliance notices, permit applications, and fee invoices are issued to identified owners or operators. The platform tracks payment status and escalates outstanding accounts.</p>
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="step-num">4</div>
                        <div class="step-body">
                            <h4>Monitor &amp; Report</h4>
                            <p>Live dashboards show revenue collected by stream, zone, and officer. Anomalies — new constructions, changed land use, new market entrants — trigger alerts for immediate follow-up.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="sec-label"><i class="bi bi-journal-bookmark"></i> For Researchers &amp; Policymakers</div>
                <h2 class="sec-title">Land Data as a <span>Public Good</span></h2>
                <p class="sec-sub mb-4">Beyond revenue, Olives BI's land intelligence platform supports policy research, urban planning, environmental impact studies, and development finance — giving researchers and government analysts the spatial context they need.</p>

                <div class="research-card">
                    <div class="research-item">
                        <div class="ri-icon">🏙️</div>
                        <div class="ri-text">
                            <strong>Urban Growth Analysis</strong>
                            <span>Track informal settlement expansion, peri-urban land conversion, and zoning compliance over time to inform master planning decisions.</span>
                        </div>
                    </div>
                    <div class="research-item">
                        <div class="ri-icon">🌾</div>
                        <div class="ri-text">
                            <strong>Agricultural Land Conversion Monitoring</strong>
                            <span>Alert when farmland is converted to built-up use without proper change-of-use authorisation — protecting food security zones.</span>
                        </div>
                    </div>
                    <div class="research-item">
                        <div class="ri-icon">💧</div>
                        <div class="ri-text">
                            <strong>Watershed &amp; Floodplain Encroachment</strong>
                            <span>Identify buildings and infrastructure placed within flood buffer zones and wetland setbacks, providing data for environmental litigation and insurance risk assessments.</span>
                        </div>
                    </div>
                    <div class="research-item">
                        <div class="ri-icon">📊</div>
                        <div class="ri-text">
                            <strong>Revenue Benchmarking &amp; Fiscal Studies</strong>
                            <span>Compare IGF performance across districts of similar size and economic profile. Pinpoint underperforming revenue streams and model the impact of rate adjustments.</span>
                        </div>
                    </div>
                    <div class="research-item">
                        <div class="ri-icon">🌍</div>
                        <div class="ri-text">
                            <strong>SDG &amp; Climate Finance Reporting</strong>
                            <span>Generate auditable spatial reports for SDG 11 (Sustainable Cities), SDG 15 (Life on Land), and climate adaptation financing — backed by verifiable satellite and sensor data.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── REGISTER SECTION ── -->
<section class="reg-section" id="register">
    <div class="container">
        <div class="row g-5 align-items-start">

            <!-- Left: Why register -->
            <div class="col-lg-5">
                <div class="sec-label"><i class="bi bi-person-badge-fill"></i> Assembly &amp; Municipality Portal</div>
                <h2 class="sec-title">Create Your <span>Official Account</span></h2>
                <p class="sec-sub mb-4">Register your Assembly or Municipality to access the full Olives BI IGF dashboard — land data, compliance alerts, revenue tracking and more.</p>

                <div class="reg-benefit">
                    <div class="rb-icon">🗺️</div>
                    <div class="rb-text">
                        <strong>Full Land &amp; Parcel Dashboard</strong>
                        <span>See every registered and unregistered parcel, building, and business in your jurisdiction on a live GIS map.</span>
                    </div>
                </div>
                <div class="reg-benefit">
                    <div class="rb-icon">💰</div>
                    <div class="rb-text">
                        <strong>IGF Revenue Tracker</strong>
                        <span>Monitor collection performance across all 8+ revenue streams in real-time, with monthly trend reports.</span>
                    </div>
                </div>
                <div class="reg-benefit">
                    <div class="rb-icon">🔔</div>
                    <div class="rb-text">
                        <strong>Compliance Alerts</strong>
                        <span>Get instant notifications when new unauthorised structures appear, permits lapse, or environmental violations are detected.</span>
                    </div>
                </div>
                <div class="reg-benefit">
                    <div class="rb-icon">📊</div>
                    <div class="rb-text">
                        <strong>Monthly IGF Reports</strong>
                        <span>Auto-generated PDF reports per revenue category, ready for council presentations and auditor submissions.</span>
                    </div>
                </div>
                <div class="reg-benefit">
                    <div class="rb-icon">🔒</div>
                    <div class="rb-text">
                        <strong>Secure &amp; Role-Based Access</strong>
                        <span>Add your revenue officers, planners, and administrators with individual logins and permission levels.</span>
                    </div>
                </div>

                <div style="background:#fff; border-radius:14px; padding:1.1rem 1.25rem; border:1.5px solid rgba(26,92,42,.1); margin-top:1.75rem;">
                    <div style="font-size:.75rem; font-weight:700; color:var(--primary); margin-bottom:.5rem;">Already have an account?</div>
                    <a href="{{ route('auth.index') }}" style="display:inline-flex; align-items:center; gap:.4rem; font-size:.85rem; font-weight:700; color:var(--primary); text-decoration:none;">
                        <i class="bi bi-box-arrow-in-right"></i> Sign in to your Assembly portal
                    </a>
                </div>
            </div>

            <!-- Right: Registration form -->
            <div class="col-lg-7">
                <div class="reg-card">

                    <div class="reg-card-title">Register Your Assembly / Municipality</div>
                    <p class="reg-card-sub">All fields marked <span style="color:#e74c3c;">*</span> are required. A confirmation email will be sent to your official address.</p>

                    <!-- Step indicators -->
                    <div class="reg-steps">
                        <div class="reg-step-btn active" id="step-ind-1">
                            <div class="sn">1</div>
                            <span>Organisation</span>
                        </div>
                        <div class="reg-step-btn" id="step-ind-2">
                            <div class="sn">2</div>
                            <span>Contact</span>
                        </div>
                        <div class="reg-step-btn" id="step-ind-3">
                            <div class="sn">3</div>
                            <span>Account</span>
                        </div>
                    </div>

                    <!-- Form wrapper (hidden when success shown) -->
                    <div id="reg-form-wrap">

                        <!-- STEP 1: Organisation details -->
                        <div class="reg-step-page active" id="step-1">
                            <div class="rf-group">
                                <label class="rf-label" for="reg-assembly-name">Assembly / Municipality Name <span class="req">*</span></label>
                                <input class="rf-input" id="reg-assembly-name" type="text" placeholder="e.g. Accra Metropolitan Assembly">
                                <div class="rf-error" id="err-assembly-name">Please enter the official assembly or municipality name.</div>
                            </div>

                            <div class="rf-group">
                                <label class="rf-label" for="reg-type">Assembly Type <span class="req">*</span></label>
                                <select class="rf-select" id="reg-type">
                                    <option value="">— Select type —</option>
                                    <option>Metropolitan Assembly</option>
                                    <option>Municipal Assembly</option>
                                    <option>District Assembly</option>
                                    <option>Urban Council</option>
                                    <option>Town / Area Council</option>
                                    <option>Regional Coordinating Council</option>
                                </select>
                                <div class="rf-error" id="err-type">Please select the assembly type.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="rf-group mb-0">
                                        <label class="rf-label" for="reg-region">Region <span class="req">*</span></label>
                                        <input class="rf-input" id="reg-region" type="text" placeholder="e.g. Greater Accra">
                                        <div class="rf-error" id="err-region">Region is required.</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="rf-group mb-0">
                                        <label class="rf-label" for="reg-district">District <span class="req">*</span></label>
                                        <input class="rf-input" id="reg-district" type="text" placeholder="e.g. Accra Central">
                                        <div class="rf-error" id="err-district">District is required.</div>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top:1.25rem; display:flex; justify-content:flex-end;">
                                <button class="btn-step btn-step-next" id="btn-next-1" type="button">
                                    Next: Contact Details <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 2: Contact person -->
                        <div class="reg-step-page" id="step-2">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="rf-group mb-0">
                                        <label class="rf-label" for="reg-firstname">First Name <span class="req">*</span></label>
                                        <input class="rf-input" id="reg-firstname" type="text" placeholder="First name">
                                        <div class="rf-error" id="err-firstname">First name is required.</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="rf-group mb-0">
                                        <label class="rf-label" for="reg-lastname">Last Name <span class="req">*</span></label>
                                        <input class="rf-input" id="reg-lastname" type="text" placeholder="Last name">
                                        <div class="rf-error" id="err-lastname">Last name is required.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="rf-group" style="margin-top:1rem;">
                                <label class="rf-label" for="reg-position">Position / Role <span class="req">*</span></label>
                                <select class="rf-select" id="reg-position">
                                    <option value="">— Select role —</option>
                                    <option>District Chief Executive (DCE)</option>
                                    <option>Municipal Chief Executive (MCE)</option>
                                    <option>Coordinating Director</option>
                                    <option>Revenue Officer</option>
                                    <option>District Planning Officer</option>
                                    <option>Finance Officer</option>
                                    <option>Environmental Officer</option>
                                    <option>IT / GIS Officer</option>
                                    <option>Other</option>
                                </select>
                                <div class="rf-error" id="err-position">Please select your position.</div>
                            </div>

                            <div class="rf-group">
                                <label class="rf-label" for="reg-phone">Official Phone Number <span class="req">*</span></label>
                                <input class="rf-input" id="reg-phone" type="tel" placeholder="e.g. +233 20 000 0000">
                                <div class="rf-error" id="err-phone">A valid phone number is required.</div>
                            </div>

                            <div style="margin-top:1.25rem; display:flex; justify-content:space-between; gap:.75rem;">
                                <button class="btn-step btn-step-back" id="btn-back-2" type="button">
                                    <i class="bi bi-arrow-left"></i> Back
                                </button>
                                <button class="btn-step btn-step-next" id="btn-next-2" type="button">
                                    Next: Account Setup <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 3: Account credentials -->
                        <div class="reg-step-page" id="step-3">
                            <div class="rf-group">
                                <label class="rf-label" for="reg-email">Official Email Address <span class="req">*</span></label>
                                <input class="rf-input" id="reg-email" type="email" placeholder="yourname@assemblyname.gov.gh">
                                <div class="rf-hint">Use your official government email. A verification link will be sent here.</div>
                                <div class="rf-error" id="err-email">Please enter a valid official email address.</div>
                            </div>

                            <div class="rf-group">
                                <label class="rf-label" for="reg-password">Password <span class="req">*</span></label>
                                <div style="position:relative;">
                                    <input class="rf-input" id="reg-password" type="password" placeholder="Create a strong password" style="padding-right:2.8rem;" oninput="checkPwStrength(this.value)">
                                    <button type="button" onclick="togglePw('reg-password', this)" style="position:absolute; right:.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer; padding:0; font-size:1rem; line-height:1;"><i class="bi bi-eye"></i></button>
                                </div>
                                <div class="pw-strength mt-1">
                                    <div class="pw-bar" id="pw-bar-1"></div>
                                    <div class="pw-bar" id="pw-bar-2"></div>
                                    <div class="pw-bar" id="pw-bar-3"></div>
                                    <div class="pw-bar" id="pw-bar-4"></div>
                                </div>
                                <div class="rf-hint" id="pw-strength-lbl">Min 8 characters, include uppercase, numbers, and symbols.</div>
                                <div class="rf-error" id="err-password">Password must be at least 8 characters.</div>
                            </div>

                            <div class="rf-group">
                                <label class="rf-label" for="reg-confirm">Confirm Password <span class="req">*</span></label>
                                <div style="position:relative;">
                                    <input class="rf-input" id="reg-confirm" type="password" placeholder="Re-enter your password" style="padding-right:2.8rem;">
                                    <button type="button" onclick="togglePw('reg-confirm', this)" style="position:absolute; right:.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer; padding:0; font-size:1rem; line-height:1;"><i class="bi bi-eye"></i></button>
                                </div>
                                <div class="rf-error" id="err-confirm">Passwords do not match.</div>
                            </div>

                            <div style="display:flex; align-items:flex-start; gap:.6rem; margin-bottom:1.25rem; padding:.75rem; background:var(--light-green); border-radius:9px; font-size:.8rem; color:#374151; line-height:1.5;">
                                <input type="checkbox" id="reg-terms" style="margin-top:.15rem; flex-shrink:0; accent-color:var(--primary);">
                                <label for="reg-terms">I confirm that I am an authorised representative of the Assembly/Municipality and agree to the <a href="#" style="color:var(--primary); font-weight:600;">Terms of Use</a> and <a href="#" style="color:var(--primary); font-weight:600;">Data Policy</a>.</label>
                            </div>
                            <div class="rf-error" id="err-terms" style="margin-top:-.75rem; margin-bottom:.75rem;">You must agree to the terms to proceed.</div>

                            <div style="display:flex; justify-content:space-between; gap:.75rem;">
                                <button class="btn-step btn-step-back" id="btn-back-3" type="button">
                                    <i class="bi bi-arrow-left"></i> Back
                                </button>
                                <button class="btn-register" id="btn-submit" type="button">
                                    <i class="bi bi-send-fill"></i> Create Account &amp; Send Verification Email
                                </button>
                            </div>
                        </div>

                    </div><!-- /reg-form-wrap -->

                    <!-- Success state -->
                    <div class="reg-success" id="reg-success">
                        <div class="success-circle">✓</div>
                        <div class="success-title">Account Request Submitted!</div>
                        <p class="success-email" id="success-msg">
                            A verification email has been sent to <strong id="success-email-display">your email</strong>.<br>
                            Click the link in the email to activate your Assembly portal account.
                        </p>
                        <div style="margin-top:1.25rem; padding:1rem; background:var(--light-green); border-radius:10px; font-size:.8rem; color:#374151; line-height:1.6;">
                            <strong style="color:var(--primary);">What happens next?</strong><br>
                            1. Check your inbox (and spam folder) for the verification email.<br>
                            2. Click the activation link to confirm your email address.<br>
                            3. Our team will review and activate your account within 24 hours.<br>
                            4. You will receive a second email with your portal login instructions.
                        </div>
                        <a href="{{ route('auth.index') }}" style="display:inline-flex; align-items:center; gap:.5rem; margin-top:1.25rem; font-size:.85rem; font-weight:700; color:var(--primary); text-decoration:none;">
                            <i class="bi bi-box-arrow-in-right"></i> Go to Sign In
                        </a>
                    </div>

                </div><!-- /reg-card -->
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section style="padding:0 0 5rem;">
    <div class="container">
        <div class="igf-cta">
            <h2>Ready to Unlock Your Assembly's <span>Full Revenue Potential?</span></h2>
            <p>Get a free land-data readiness assessment for your district or municipality. Our team will map the IGF gap and show you exactly what Olives BI can unlock.</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('locality.check') }}" class="btn-cta-primary">
                    <i class="bi bi-geo-alt-fill"></i> Check Your Locality Now
                </a>
                <a href="{{ route('donation.index') }}" class="btn-cta-ghost">
                    <i class="bi bi-heart"></i> Support the Mission
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer class="igf-footer">
    <span>🌿 Olives BI</span> — Environmental &amp; Land Revenue Intelligence &nbsp;·&nbsp; © {{ date('Y') }}
    &nbsp;·&nbsp; <a href="{{ route('auth.LandingPage') }}" style="color:var(--accent); text-decoration:none;">Home</a>
    &nbsp;·&nbsp; <a href="{{ route('locality.check') }}" style="color:var(--accent); text-decoration:none;">Check Locality</a>
    &nbsp;·&nbsp; <a href="{{ route('donation.index') }}" style="color:var(--accent); text-decoration:none;">Support Us</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    /* ── Mobile nav ── */
    const toggle     = document.getElementById('navToggle');
    const menu       = document.getElementById('navMobMenu');
    const toggleIcon = document.getElementById('navToggleIcon');
    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('open');
            toggle.classList.toggle('open', isOpen);
            if (toggleIcon) toggleIcon.className = isOpen ? 'bi bi-x-lg' : 'bi bi-list';
            toggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
        });
        /* Close drawer when a link inside is clicked */
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.remove('open');
                toggle.classList.remove('open');
                if (toggleIcon) toggleIcon.className = 'bi bi-list';
            });
        });
        /* Close on outside click */
        document.addEventListener('click', e => {
            if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('open');
                toggle.classList.remove('open');
                if (toggleIcon) toggleIcon.className = 'bi bi-list';
            }
        });
    }

    /* ── Password strength ── */
    function checkPwStrength(val) {
        const bars  = [1,2,3,4].map(i => document.getElementById('pw-bar-' + i));
        const lbl   = document.getElementById('pw-strength-lbl');
        let score   = 0;
        if (val.length >= 8)                      score++;
        if (/[A-Z]/.test(val))                    score++;
        if (/[0-9]/.test(val))                    score++;
        if (/[^A-Za-z0-9]/.test(val))             score++;
        const levels = ['weak','fair','good','strong'];
        const labels = ['Too short','Fair — add numbers or symbols','Good — add a symbol','Strong password'];
        bars.forEach((b, i) => {
            b.className = 'pw-bar' + (i < score ? ' ' + levels[score - 1] : '');
        });
        if (lbl) lbl.textContent = val.length === 0 ? 'Min 8 characters, include uppercase, numbers, and symbols.' : labels[score - 1] || 'Too short';
    }

    /* ── Toggle password visibility ── */
    function togglePw(inputId, btn) {
        const inp = document.getElementById(inputId);
        if (!inp) return;
        const isText = inp.type === 'text';
        inp.type = isText ? 'password' : 'text';
        btn.innerHTML = isText ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    }

    /* ── Step navigation helpers ── */
    function showStep(n) {
        [1,2,3].forEach(i => {
            document.getElementById('step-' + i).classList.toggle('active', i === n);
            const ind = document.getElementById('step-ind-' + i);
            ind.classList.remove('active','done');
            if (i === n)     ind.classList.add('active');
            else if (i < n)  ind.classList.add('done');
        });
    }

    function setError(id, show) {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('show', show);
    }

    function clearErrors(...ids) {
        ids.forEach(id => setError(id, false));
    }

    function val(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim() : '';
    }

    /* ── Step 1 → Step 2 ── */
    document.getElementById('btn-next-1').addEventListener('click', () => {
        const asmName = val('reg-assembly-name');
        const asmType = val('reg-type');
        const region  = val('reg-region');
        const district= val('reg-district');

        let ok = true;
        clearErrors('err-assembly-name','err-type','err-region','err-district');
        if (!asmName)   { setError('err-assembly-name', true); ok = false; }
        if (!asmType)   { setError('err-type', true);          ok = false; }
        if (!region)    { setError('err-region', true);        ok = false; }
        if (!district)  { setError('err-district', true);      ok = false; }

        if (ok) showStep(2);
    });

    /* ── Step 2 → Step 3 ── */
    document.getElementById('btn-next-2').addEventListener('click', () => {
        const fn   = val('reg-firstname');
        const ln   = val('reg-lastname');
        const pos  = val('reg-position');
        const ph   = val('reg-phone');

        let ok = true;
        clearErrors('err-firstname','err-lastname','err-position','err-phone');
        if (!fn)  { setError('err-firstname', true); ok = false; }
        if (!ln)  { setError('err-lastname', true);  ok = false; }
        if (!pos) { setError('err-position', true);  ok = false; }
        if (!ph || ph.replace(/[\s\-\+\(\)]/g,'').length < 7) { setError('err-phone', true); ok = false; }

        if (ok) showStep(3);
    });

    /* ── Back buttons ── */
    document.getElementById('btn-back-2').addEventListener('click', () => showStep(1));
    document.getElementById('btn-back-3').addEventListener('click', () => showStep(2));

    /* ── Submit ── */
    document.getElementById('btn-submit').addEventListener('click', () => {
        const email   = val('reg-email');
        const pw      = val('reg-password');
        const confirm = val('reg-confirm');
        const terms   = document.getElementById('reg-terms');

        let ok = true;
        clearErrors('err-email','err-password','err-confirm','err-terms');

        const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRe.test(email)) { setError('err-email', true);   ok = false; }
        if (pw.length < 8)                  { setError('err-password', true); ok = false; }
        if (pw !== confirm)                 { setError('err-confirm', true);  ok = false; }
        if (!terms || !terms.checked)       { setError('err-terms', true);    ok = false; }

        if (!ok) return;

        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Submitting…';

        setTimeout(() => {
            document.getElementById('reg-form-wrap').classList.add('hide');
            const successEl = document.getElementById('reg-success');
            successEl.classList.add('show');
            const emailDisplay = document.getElementById('success-email-display');
            if (emailDisplay) emailDisplay.textContent = email;
        }, 900);
    });
</script>
</body>
</html>

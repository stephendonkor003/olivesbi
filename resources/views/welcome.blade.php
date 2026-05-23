<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.public-seo', [
        'seoTitle' => 'Olives BI - Environmental Protection Intelligence, GIS & Revenue Data Platform',
        'seoDescription' => 'Olives BI is an environmental intelligence platform for protecting forests, wetlands, rivers, land, air quality, and local revenue systems with GIS, open data, and decision dashboards.',
        'seoKeywords' => 'Olives BI, environmental protection intelligence, GIS platform, land data, forest reserve protection, Ramsar wetlands, air quality, waste management, local government revenue, IGF Ghana, district assembly software',
        'seoUrl' => route('auth.LandingPage'),
        'seoSchema' => [
            '@type' => 'WebPage',
            'primaryImageOfPage' => asset('assets/images/locality/ramsar-wetland.jpg'),
        ],
    ])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:       #1a5c2a;
            --primary-mid:   #236b33;
            --primary-light: #2d9e4b;
            --accent:        #4cc76a;
            --accent-soft:   #a8e6bc;
            --earth:         #7a5230;
            --water:         #1565c0;
            --water-light:   #42a5f5;
            --sky:           #0288d1;
            --light-green:   #e8f5e9;
            --dark-bg:       #071a0d;
            --card-bg:       #ffffff;
            --text-muted:    #6b7280;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f7f1;
            color: #1a2e1e;
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        #mainNav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: 1rem 0;
            transition: background .35s ease, padding .35s ease, box-shadow .35s ease;
        }
        #mainNav.scrolled {
            background: rgba(7, 26, 13, .96);
            backdrop-filter: blur(10px);
            padding: .6rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,.3);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            color: #fff !important;
            text-decoration: none;
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
            font-weight: 500;
            font-size: .9rem;
            transition: color .2s;
            padding: .4rem .85rem !important;
            border-radius: 6px;
        }
        .nav-link-custom:hover { color: var(--accent) !important; background: rgba(255,255,255,.06); }

        .btn-nav-login {
            background: transparent;
            border: 1.5px solid rgba(255,255,255,.4);
            color: #fff;
            border-radius: 8px;
            padding: .4rem 1.2rem;
            font-weight: 500;
            font-size: .88rem;
            transition: all .25s;
        }
        .btn-nav-login:hover { border-color: var(--accent); color: var(--accent); }

        .btn-nav-cta {
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: .4rem 1.4rem;
            font-weight: 600;
            font-size: .88rem;
            transition: all .25s;
            box-shadow: 0 2px 10px rgba(76,199,106,.35);
        }
        .btn-nav-cta:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(76,199,106,.45); color: #fff; }

        .navbar-toggler { border: 1.5px solid rgba(255,255,255,.4); }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,0.85%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ── HERO SLIDER ── */
        #hero {
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        .hero-slider { position: absolute; inset: 0; z-index: 0; }

        .hero-slide {
            position: absolute; inset: 0;
            opacity: 0;
            transition: opacity 1.5s ease;
        }
        .hero-slide.active { opacity: 1; }

        .slide-bg {
            position: absolute; inset: 0;
            background-size: cover;
            background-position: center;
            transform: scale(1.07);
            transition: transform 10s ease;
        }
        .hero-slide.active .slide-bg { transform: scale(1); }

        .slide-video {
            position: absolute; inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
        }

        .slide-type-badge {
            position: absolute;
            top: 5.5rem; right: 1.5rem;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(6px);
            color: rgba(255,255,255,.75);
            font-size: .7rem;
            font-weight: 700;
            padding: .28rem .7rem;
            border-radius: 5px;
            letter-spacing: .6px;
            text-transform: uppercase;
            z-index: 2;
        }

        .hero-overlay {
            position: absolute; inset: 0;
            z-index: 1;
            background: linear-gradient(
                135deg,
                rgba(7,26,13,.86) 0%,
                rgba(26,92,42,.6) 50%,
                rgba(7,26,13,.82) 100%
            );
        }
        .hero-content { position: relative; z-index: 2; }

        /* Controls */
        .slider-controls {
            position: absolute;
            bottom: 1.75rem;
            left: 0; right: 0;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .85rem;
            padding: 0 1rem;
        }
        .slide-label {
            color: rgba(255,255,255,.78);
            font-size: .78rem;
            font-weight: 600;
            background: rgba(0,0,0,.35);
            border: 1px solid rgba(255,255,255,.14);
            padding: .28rem .9rem;
            border-radius: 50px;
            backdrop-filter: blur(6px);
            white-space: nowrap;
            letter-spacing: .3px;
            transition: opacity .5s;
        }
        .slide-dots { display: flex; gap: .45rem; align-items: center; }
        .slide-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,.32);
            cursor: pointer; border: none; padding: 0;
            transition: all .35s;
            flex-shrink: 0;
        }
        .slide-dot.active {
            background: var(--accent);
            width: 24px;
            border-radius: 4px;
        }
        .slide-btn {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
            border: 1.5px solid rgba(255,255,255,.22);
            color: #fff; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            transition: all .25s;
            backdrop-filter: blur(4px);
            flex-shrink: 0;
        }
        .slide-btn:hover { background: rgba(255,255,255,.2); border-color: var(--accent); color: var(--accent); }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(76,199,106,.15);
            border: 1px solid rgba(76,199,106,.4);
            color: var(--accent-soft);
            border-radius: 50px;
            padding: .35rem 1rem;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: clamp(2.2rem, 5vw, 4rem);
            line-height: 1.15;
            color: #fff;
            margin-bottom: 1.25rem;
        }
        .hero-title .highlight {
            background: linear-gradient(90deg, var(--accent), #a8e6bc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-subtitle {
            font-size: 1.1rem;
            color: rgba(255,255,255,.78);
            max-width: 560px;
            line-height: 1.7;
            margin-bottom: 2.2rem;
        }
        .btn-hero-primary {
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            border: none;
            color: #fff;
            padding: .85rem 2rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            transition: all .3s;
            box-shadow: 0 4px 20px rgba(76,199,106,.4);
        }
        .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(76,199,106,.5); color: #fff; }
        .btn-hero-secondary {
            background: rgba(255,255,255,.1);
            border: 1.5px solid rgba(255,255,255,.35);
            color: #fff;
            padding: .85rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all .3s;
            backdrop-filter: blur(6px);
        }
        .btn-hero-secondary:hover { background: rgba(255,255,255,.18); border-color: rgba(255,255,255,.6); color: #fff; }

        .hero-scroll-indicator {
            position: absolute;
            bottom: 2rem; left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .3rem;
            color: rgba(255,255,255,.5);
            font-size: .75rem;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%       { transform: translateX(-50%) translateY(8px); }
        }

        /* ── STATS BAR ── */
        .stats-bar {
            background: var(--dark-bg);
            padding: 2rem 0;
        }
        .stat-item { text-align: center; padding: .5rem 1rem; }
        .stat-number {
            font-family: 'Poppins', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--accent);
            line-height: 1;
        }
        .stat-label {
            font-size: .82rem;
            color: rgba(255,255,255,.55);
            margin-top: .3rem;
            font-weight: 500;
            letter-spacing: .3px;
        }
        .stat-divider {
            width: 1px;
            background: rgba(255,255,255,.1);
            height: 50px;
            align-self: center;
        }

        /* ── SECTIONS ── */
        .section-label {
            display: inline-block;
            background: var(--light-green);
            color: var(--primary);
            border-radius: 50px;
            padding: .3rem 1rem;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: .75rem;
        }
        .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: clamp(1.7rem, 3vw, 2.5rem);
            color: var(--dark-bg);
            line-height: 1.2;
        }
        .section-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 580px;
            margin: .75rem auto 0;
            line-height: 1.7;
        }

        /* ── FEATURE CARDS ── */
        .features-section { padding: 5rem 0; background: #f5fbf6; }
        .feature-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(26,92,42,.07);
            transition: transform .3s, box-shadow .3s;
            height: 100%;
            border: 1px solid rgba(26,92,42,.06);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 36px rgba(26,92,42,.14);
        }
        .feature-img {
            width: 100%; height: 180px;
            object-fit: cover;
            display: block;
        }
        .feature-body { padding: 1.4rem; }
        .feature-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            flex-shrink: 0;
        }
        .feature-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--dark-bg);
            margin-bottom: .45rem;
        }
        .feature-text {
            font-size: .85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Feature icon colour variants */
        .icon-forest   { background: #e8f5e9; color: #1b5e20; }
        .icon-water    { background: #e3f2fd; color: #0d47a1; }
        .icon-earth    { background: #fff3e0; color: #e65100; }
        .icon-air      { background: #e0f7fa; color: #006064; }
        .icon-waste    { background: #f3e5f5; color: #4a148c; }
        .icon-flood    { background: #e8eaf6; color: #1a237e; }
        .icon-ramsar   { background: #e0f2f1; color: #004d40; }
        .icon-build    { background: #fce4ec; color: #880e4f; }

        /* ── COVERAGE SCALE ── */
        .coverage-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary) 100%);
            position: relative;
            overflow: hidden;
        }
        .coverage-section::before {
            content: '';
            position: absolute; inset: 0;
            background: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1920&q=60') center / cover;
            opacity: .12;
        }
        .coverage-content { position: relative; z-index: 2; }

        .scale-item {
            text-align: center;
            padding: 1.5rem 1rem;
        }
        .scale-circle {
            width: 90px; height: 90px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            border: 2px solid rgba(76,199,106,.35);
            background: rgba(76,199,106,.1);
            transition: all .3s;
        }
        .scale-item:hover .scale-circle {
            background: rgba(76,199,106,.25);
            border-color: var(--accent);
            transform: scale(1.08);
        }
        .scale-label {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: .95rem;
            color: #fff;
            margin-bottom: .25rem;
        }
        .scale-sub { font-size: .78rem; color: rgba(255,255,255,.5); }
        .scale-arrow {
            color: rgba(76,199,106,.5);
            font-size: 1.5rem;
            align-self: center;
            padding-bottom: 2rem;
        }

        /* ── HOW IT WORKS ── */
        .howitworks-section { padding: 5rem 0; background: #fff; }
        .step-card {
            position: relative;
            padding: 2rem;
            border-radius: 16px;
            background: var(--light-green);
            border: 1.5px solid rgba(26,92,42,.08);
            text-align: center;
            transition: all .3s;
        }
        .step-card:hover { background: #d4edda; transform: translateY(-4px); }
        .step-number {
            font-family: 'Poppins', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            color: rgba(26,92,42,.1);
            line-height: 1;
            margin-bottom: .5rem;
        }
        .step-icon {
            font-size: 2.2rem;
            margin-bottom: 1rem;
        }
        .step-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--primary);
            margin-bottom: .5rem;
        }
        .step-text { font-size: .88rem; color: #4b6e52; line-height: 1.6; }

        .step-connector {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 1rem;
        }

        /* ── MISSION CALLOUT ── */
        .mission-section {
            padding: 5rem 0;
            background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1920&q=70') center / cover;
            position: relative;
        }
        .mission-overlay {
            position: absolute; inset: 0;
            background: rgba(7,26,13,.82);
        }
        .mission-content { position: relative; z-index: 2; text-align: center; }
        .mission-quote {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.5rem, 3vw, 2.3rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.4;
            max-width: 800px;
            margin: 0 auto 1.5rem;
        }
        .mission-quote .em { color: var(--accent); font-style: italic; }
        .mission-sub {
            color: rgba(255,255,255,.65);
            font-size: 1rem;
            max-width: 560px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ── FOCUS AREAS IMAGE STRIP ── */
        .strip-section { padding: 4rem 0; background: #f0f7f1; }
        .strip-img {
            width: 100%; height: 220px;
            object-fit: cover;
            border-radius: 12px;
            transition: transform .35s;
        }
        .strip-item { position: relative; overflow: hidden; border-radius: 12px; }
        .strip-item:hover .strip-img { transform: scale(1.05); }
        .strip-caption {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: linear-gradient(transparent, rgba(7,26,13,.85));
            color: #fff;
            padding: 1.5rem .9rem .75rem;
            font-weight: 600;
            font-size: .85rem;
        }

        /* ── CTA SECTION ── */
        .cta-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--primary) 0%, #0d3a1a 100%);
        }
        .btn-cta-white {
            background: #fff;
            color: var(--primary);
            border: none;
            padding: .9rem 2.5rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            transition: all .3s;
        }
        .btn-cta-white:hover { background: var(--light-green); color: var(--primary); transform: translateY(-2px); }
        .btn-cta-outline {
            background: transparent;
            border: 2px solid rgba(255,255,255,.4);
            color: #fff;
            padding: .9rem 2.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all .3s;
        }
        .btn-cta-outline:hover { border-color: var(--accent); color: var(--accent); }

        /* ── FOOTER ── */
        footer {
            background: var(--dark-bg);
            color: rgba(255,255,255,.6);
            padding: 3rem 0 1.5rem;
        }
        .footer-logo {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            color: #fff;
        }
        .footer-logo span { color: var(--accent); }
        .footer-tagline { font-size: .83rem; color: rgba(255,255,255,.4); margin-top: .3rem; }
        .footer-heading {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: rgba(255,255,255,.8);
            font-size: .9rem;
            margin-bottom: 1rem;
            letter-spacing: .3px;
        }
        .footer-link {
            display: block;
            color: rgba(255,255,255,.5);
            text-decoration: none;
            font-size: .85rem;
            margin-bottom: .45rem;
            transition: color .2s;
        }
        .footer-link:hover { color: var(--accent); }
        .footer-divider { border-color: rgba(255,255,255,.08); margin: 2rem 0 1rem; }
        .footer-bottom { font-size: .8rem; color: rgba(255,255,255,.3); }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .stat-divider { display: none; }
            .scale-arrow { display: none; }
            .hero-title { font-size: 2rem; }
        }

        /* ── UTILITY ── */
        .green-dot {
            display: inline-block;
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            margin-right: .5rem;
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(.8); }
        }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════════
     NAVIGATION
═══════════════════════════════════════════ -->
<nav id="mainNav" class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">
            <div class="brand-leaf">🌿</div>
            Olives <span class="brand-bi ms-1">BI</span>
        </a>

        <button class="navbar-toggler border-0 ms-auto me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#coverage">Coverage</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#how-it-works">How It Works</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#mission">Mission</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('locality.check') }}">Check Locality</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('igf.index') }}" style="font-weight:600;">Business We Support</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#contact">Contact</a></li>
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


<!-- ═══════════════════════════════════════════
     HERO SLIDER
═══════════════════════════════════════════ -->
<section id="hero">

    <!-- ── 8 SLIDES: 4 videos + 4 images ── -->
    <div class="hero-slider">

        <!-- SLIDE 1 · VIDEO · Forest Reserves -->
        <div class="hero-slide active" data-label="🌲 Forest Reserves">
            <span class="slide-type-badge">▶ Video</span>
            <video class="slide-video" autoplay muted loop playsinline preload="auto"
                poster="https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1920&q=50">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-forest-stream-in-the-sunlight-529-large.mp4" type="video/mp4">
            </video>
        </div>

        <!-- SLIDE 2 · IMAGE · Land Data Intelligence -->
        <div class="hero-slide" data-label="🗺️ Land Data Intelligence">
            <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?auto=format&fit=crop&w=1920&q=80')"></div>
        </div>

        <!-- SLIDE 3 · VIDEO · Protected Zones -->
        <div class="hero-slide" data-label="🌿 Protected Zones">
            <span class="slide-type-badge">▶ Video</span>
            <video class="slide-video" autoplay muted loop playsinline preload="auto"
                poster="https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1920&q=50">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-drone-flying-over-forest-and-road-1083-large.mp4" type="video/mp4">
            </video>
        </div>

        <!-- SLIDE 4 · IMAGE · Afforestation -->
        <div class="hero-slide" data-label="🏞️ Afforestation Tracking">
            <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1920&q=80')"></div>
        </div>

        <!-- SLIDE 5 · VIDEO · River Cleanliness -->
        <div class="hero-slide" data-label="💧 River Cleanliness">
            <span class="slide-type-badge">▶ Video</span>
            <video class="slide-video" autoplay muted loop playsinline preload="auto"
                poster="https://images.unsplash.com/photo-1502209524164-acea936639a2?auto=format&fit=crop&w=1920&q=50">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-river-running-through-a-forest-36680-large.mp4" type="video/mp4">
            </video>
        </div>

        <!-- SLIDE 6 · IMAGE · Flood Zone Mapping -->
        <div class="hero-slide" data-label="⛰️ Flood Zone Mapping">
            <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1920&q=80')"></div>
        </div>

        <!-- SLIDE 7 · VIDEO · Air Quality -->
        <div class="hero-slide" data-label="💨 Air Quality Zones">
            <span class="slide-type-badge">▶ Video</span>
            <video class="slide-video" autoplay muted loop playsinline preload="auto"
                poster="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=1920&q=50">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-green-trees-in-a-forest-7189-large.mp4" type="video/mp4">
            </video>
        </div>

        <!-- SLIDE 8 · IMAGE · Waste Management -->
        <div class="hero-slide" data-label="♻️ Waste Management">
            <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?auto=format&fit=crop&w=1920&q=80')"></div>
        </div>

    </div><!-- /.hero-slider -->

    <!-- Dark green overlay (sits above slides, below content) -->
    <div class="hero-overlay"></div>

    <!-- Main hero content (static across all slides) -->
    <div class="container hero-content py-5">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-7 py-5">
                <div class="hero-badge" data-aos="fade-down">
                    <span class="green-dot"></span>
                    Environmental Intelligence Platform
                </div>
                <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100">
                    Protecting Earth Through<br>
                    <span class="highlight">Intelligent Land Data</span>
                </h1>
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">
                    Olives BI guides governments, planners, and communities to safeguard
                    forests, wetlands, rivers, and land from unauthorized use —
                    from a single district to the entire globe.
                </p>
                <div class="d-flex flex-wrap gap-3" data-aos="fade-up" data-aos-delay="300">
                    <a href="#features" class="btn btn-hero-primary">
                        <i class="bi bi-shield-check me-2"></i>Explore Platform
                    </a>
                    <a href="#how-it-works" class="btn btn-hero-secondary">
                        <i class="bi bi-play-circle me-2"></i>How It Works
                    </a>
                </div>

                <div class="d-flex flex-wrap gap-4 mt-4 pt-2" data-aos="fade-up" data-aos-delay="400">
                    <div class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,.65); font-size:.85rem;">
                        <i class="bi bi-check-circle-fill" style="color:var(--accent)"></i>
                        Forest Reserves
                    </div>
                    <div class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,.65); font-size:.85rem;">
                        <i class="bi bi-check-circle-fill" style="color:var(--accent)"></i>
                        Ramsar Wetlands
                    </div>
                    <div class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,.65); font-size:.85rem;">
                        <i class="bi bi-check-circle-fill" style="color:var(--accent)"></i>
                        Flood Zones
                    </div>
                    <div class="d-flex align-items-center gap-2" style="color:rgba(255,255,255,.65); font-size:.85rem;">
                        <i class="bi bi-check-circle-fill" style="color:var(--accent)"></i>
                        Air Quality
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slider navigation controls -->
    <div class="slider-controls">
        <span class="slide-label" id="slideLabel">🌲 Forest Reserves</span>
        <button class="slide-btn" id="slidePrev" aria-label="Previous slide">
            <i class="bi bi-chevron-left"></i>
        </button>
        <div class="slide-dots" id="slideDots"></div>
        <button class="slide-btn" id="slideNext" aria-label="Next slide">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

</section>


<!-- ═══════════════════════════════════════════
     STATS BAR
═══════════════════════════════════════════ -->
<section id="stats" class="stats-bar">
    <div class="container">
        <div class="row justify-content-center text-center g-0">
            <div class="col-6 col-md-3 stat-item" data-aos="fade-up">
                <div class="stat-number">8+</div>
                <div class="stat-label">Environmental Domains</div>
            </div>
            <div class="d-none d-md-flex stat-divider"></div>
            <div class="col-6 col-md-3 stat-item" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-number">195</div>
                <div class="stat-label">Countries Supported</div>
            </div>
            <div class="d-none d-md-flex stat-divider"></div>
            <div class="col-6 col-md-3 stat-item" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-number">5</div>
                <div class="stat-label">Coverage Scales</div>
            </div>
            <div class="d-none d-md-flex stat-divider"></div>
            <div class="col-6 col-md-3 stat-item" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-number">Real-Time</div>
                <div class="stat-label">Land Intelligence</div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     FEATURE STRIP (photo bar)
═══════════════════════════════════════════ -->
<section class="strip-section">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-md-3" data-aos="zoom-in">
                <div class="strip-item">
                    <img src="https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=600&q=80"
                         alt="Forest Reserve" class="strip-img" loading="lazy">
                    <div class="strip-caption">🌲 Forest Reserves</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="80">
                <div class="strip-item">
                    <img src="https://images.unsplash.com/photo-1502209524164-acea936639a2?auto=format&fit=crop&w=600&q=80"
                         alt="River" class="strip-img" loading="lazy">
                    <div class="strip-caption">💧 River Cleanliness</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="160">
                <div class="strip-item">
                    <img src="{{ asset('assets/images/locality/ramsar-wetland.jpg') }}"
                         alt="Wetlands" class="strip-img" loading="lazy">
                    <div class="strip-caption">🦢 Ramsar Wetlands</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="240">
                <div class="strip-item">
                    <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=600&q=80"
                         alt="Air Pollution" class="strip-img" loading="lazy">
                    <div class="strip-caption">💨 Air Quality Zones</div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     FEATURES GRID
═══════════════════════════════════════════ -->
<section id="features" class="features-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label">Protection Domains</span>
            <h2 class="section-title">Comprehensive Environmental<br>Intelligence Coverage</h2>
            <p class="section-subtitle">
                From unauthorized structures to household waste ratios, Olives BI monitors
                every critical land and environmental domain.
            </p>
        </div>

        <div class="row g-4">

            <!-- Unauthorized Buildings -->
            <div class="col-sm-6 col-lg-3" data-aos="fade-up">
                <div class="feature-card">
                    <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=600&q=75"
                         alt="Construction" class="feature-img" loading="lazy">
                    <div class="feature-body">
                        <div class="feature-icon icon-build"><i class="bi bi-buildings"></i></div>
                        <div class="feature-title">Unauthorized Buildings</div>
                        <p class="feature-text">Detect and flag illegal construction setups on protected and restricted land parcels before environmental damage occurs.</p>
                    </div>
                </div>
            </div>

            <!-- Road Construction -->
            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="80">
                <div class="feature-card">
                    <img src="https://images.unsplash.com/photo-1545569341-9eb8b30979d9?auto=format&fit=crop&w=600&q=75"
                         alt="Road construction" class="feature-img" loading="lazy">
                    <div class="feature-body">
                        <div class="feature-icon icon-earth"><i class="bi bi-sign-intersection"></i></div>
                        <div class="feature-title">Road Construction Zones</div>
                        <p class="feature-text">Monitor road development activities in sensitive corridors, ensuring compliance with environmental impact assessments.</p>
                    </div>
                </div>
            </div>

            <!-- Ramsar Sites -->
            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="160">
                <div class="feature-card">
                    <img src="{{ asset('assets/images/locality/ramsar-wetland.jpg') }}"
                         alt="Ramsar wetland" class="feature-img" loading="lazy">
                    <div class="feature-body">
                        <div class="feature-icon icon-ramsar"><i class="bi bi-water"></i></div>
                        <div class="feature-title">Ramsar / Wetland Sites</div>
                        <p class="feature-text">Protect internationally designated wetlands from encroachment, drainage, and biodiversity loss using geo-referenced data.</p>
                    </div>
                </div>
            </div>

            <!-- Forest Reserves -->
            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="240">
                <div class="feature-card">
                    <img src="https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=600&q=75"
                         alt="Forest reserve" class="feature-img" loading="lazy">
                    <div class="feature-body">
                        <div class="feature-icon icon-forest"><i class="bi bi-tree"></i></div>
                        <div class="feature-title">Forest Reserves</div>
                        <p class="feature-text">Track afforestation progress and deforestation threats in gazetted forest reserves at national and sub-national levels.</p>
                    </div>
                </div>
            </div>

            <!-- Flood Prone -->
            <div class="col-sm-6 col-lg-3" data-aos="fade-up">
                <div class="feature-card">
                    <img src="https://images.unsplash.com/photo-1499336315816-097655dcfbda?auto=format&fit=crop&w=600&q=75"
                         alt="Flood prone area" class="feature-img" loading="lazy">
                    <div class="feature-body">
                        <div class="feature-icon icon-flood"><i class="bi bi-tsunami"></i></div>
                        <div class="feature-title">Flood-Prone Areas</div>
                        <p class="feature-text">Identify and map flood-prone zones to prevent settlements in high-risk areas and plan effective drainage systems.</p>
                    </div>
                </div>
            </div>

            <!-- Air Pollution -->
            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="80">
                <div class="feature-card">
                    <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=600&q=75"
                         alt="Air pollution" class="feature-img" loading="lazy">
                    <div class="feature-body">
                        <div class="feature-icon icon-air"><i class="bi bi-wind"></i></div>
                        <div class="feature-title">Air Pollution Zones</div>
                        <p class="feature-text">Visualize air quality index data across districts, highlight pollution hotspots, and guide urban planning decisions.</p>
                    </div>
                </div>
            </div>

            <!-- River Cleanliness -->
            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="160">
                <div class="feature-card">
                    <img src="https://images.unsplash.com/photo-1502209524164-acea936639a2?auto=format&fit=crop&w=600&q=75"
                         alt="River water quality" class="feature-img" loading="lazy">
                    <div class="feature-body">
                        <div class="feature-icon icon-water"><i class="bi bi-droplet-half"></i></div>
                        <div class="feature-title">River Cleanliness Index</div>
                        <p class="feature-text">Measure and report on river water quality levels — detecting pollution, industrial discharge, and safe drinking water zones.</p>
                    </div>
                </div>
            </div>

            <!-- Waste Management -->
            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="240">
                <div class="feature-card">
                    <img src="https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=600&q=75"
                         alt="Waste management" class="feature-img" loading="lazy">
                    <div class="feature-body">
                        <div class="feature-icon icon-waste"><i class="bi bi-recycle"></i></div>
                        <div class="feature-title">Waste Dumps & Household Waste</div>
                        <p class="feature-text">Track waste dump locations, household waste generation ratios, and recycling coverage by district, municipality, or country.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     COVERAGE SCALE
═══════════════════════════════════════════ -->
<section id="coverage" class="coverage-section">
    <div class="container coverage-content">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label" style="background:rgba(76,199,106,.15); color:var(--accent-soft);">Scalable Intelligence</span>
            <h2 class="section-title" style="color:#fff;">From Your District<br>to the Entire Globe</h2>
            <p class="section-subtitle" style="color:rgba(255,255,255,.6);">
                Olives BI seamlessly scales across every geographic level, giving decision-makers
                the data they need at exactly the right scope.
            </p>
        </div>

        <div class="row align-items-center justify-content-center g-2">

            <div class="col-6 col-sm-4 col-md-2" data-aos="zoom-in">
                <div class="scale-item">
                    <div class="scale-circle">🏘️</div>
                    <div class="scale-label">District</div>
                    <div class="scale-sub">Hyper-local data</div>
                </div>
            </div>
            <div class="d-none d-md-flex col-md-1 scale-arrow justify-content-center">
                <i class="bi bi-arrow-right"></i>
            </div>

            <div class="col-6 col-sm-4 col-md-2" data-aos="zoom-in" data-aos-delay="100">
                <div class="scale-item">
                    <div class="scale-circle">🏙️</div>
                    <div class="scale-label">Municipality</div>
                    <div class="scale-sub">City-level insight</div>
                </div>
            </div>
            <div class="d-none d-md-flex col-md-1 scale-arrow justify-content-center">
                <i class="bi bi-arrow-right"></i>
            </div>

            <div class="col-6 col-sm-4 col-md-2" data-aos="zoom-in" data-aos-delay="200">
                <div class="scale-item">
                    <div class="scale-circle">🗺️</div>
                    <div class="scale-label">Country</div>
                    <div class="scale-sub">National overview</div>
                </div>
            </div>
            <div class="d-none d-md-flex col-md-1 scale-arrow justify-content-center">
                <i class="bi bi-arrow-right"></i>
            </div>

            <div class="col-6 col-sm-4 col-md-2" data-aos="zoom-in" data-aos-delay="300">
                <div class="scale-item">
                    <div class="scale-circle">🌍</div>
                    <div class="scale-label">Continent</div>
                    <div class="scale-sub">Regional patterns</div>
                </div>
            </div>
            <div class="d-none d-md-flex col-md-1 scale-arrow justify-content-center">
                <i class="bi bi-arrow-right"></i>
            </div>

            <div class="col-6 col-sm-4 col-md-2" data-aos="zoom-in" data-aos-delay="400">
                <div class="scale-item">
                    <div class="scale-circle">🌐</div>
                    <div class="scale-label">Global</div>
                    <div class="scale-sub">Planetary scope</div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════ -->
<section id="how-it-works" class="howitworks-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label">Simple & Powerful</span>
            <h2 class="section-title">How Olives BI Works</h2>
            <p class="section-subtitle">Three steps to turn land data into meaningful environmental protection.</p>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-md-4" data-aos="fade-up">
                <div class="step-card h-100">
                    <div class="step-number">01</div>
                    <div class="step-icon">📡</div>
                    <div class="step-title">Collect Land Intelligence</div>
                    <p class="step-text">
                        Ingest geo-spatial data from satellite imagery, government registries,
                        sensor networks, and field reports across all monitored zones and boundaries.
                    </p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="120">
                <div class="step-card h-100" style="background:#d4edda;">
                    <div class="step-number">02</div>
                    <div class="step-icon">🔍</div>
                    <div class="step-title">Analyse & Detect Risks</div>
                    <p class="step-text">
                        Smart algorithms cross-reference land-use data against protected boundaries,
                        environmental thresholds, and compliance rules to surface violations and risks.
                    </p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="240">
                <div class="step-card h-100">
                    <div class="step-number">03</div>
                    <div class="step-icon">📊</div>
                    <div class="step-title">Guide Decision Makers</div>
                    <p class="step-text">
                        Deliver interactive dashboards, alerts, and reports to planners, regulators,
                        and communities — turning insights into timely, evidence-based action.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     MISSION CALLOUT
═══════════════════════════════════════════ -->
<section id="mission" class="mission-section">
    <div class="mission-overlay"></div>
    <div class="container mission-content py-4">
        <div data-aos="fade-up">
            <blockquote class="mission-quote">
                "The Earth does not belong to us —<br>
                <span class="em">we belong to the Earth.</span><br>
                Olives BI exists to prove it."
            </blockquote>
            <p class="mission-sub">
                Our platform was designed with one principle at its core: every data point
                must ultimately serve the protection of the natural world — its forests,
                rivers, wetlands, air, and soil — for generations to come.
            </p>
            <div class="mt-4">
                <a href="#features" class="btn btn-hero-primary">
                    <i class="bi bi-leaf me-2"></i>See What We Protect
                </a>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     AFFORESTATION HIGHLIGHT
═══════════════════════════════════════════ -->
<section style="padding:5rem 0; background:#fff;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?auto=format&fit=crop&w=900&q=80"
                     alt="Afforestation" style="width:100%; border-radius:20px; box-shadow:0 8px 40px rgba(26,92,42,.18);" loading="lazy">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <span class="section-label">Afforestation & Land Restoration</span>
                <h2 class="section-title mt-2">Tracking the Growth<br>of Green Cover</h2>
                <p style="color:var(--text-muted); line-height:1.75; margin-top:1rem;">
                    Olives BI doesn't just monitor threats — it tracks positive change too.
                    Our afforestation module maps tree planting campaigns, monitors reforestation
                    success rates, and measures canopy growth against national and international targets.
                </p>
                <ul style="list-style:none; padding:0; margin-top:1.5rem;">
                    <li class="d-flex align-items-start gap-2 mb-2" style="font-size:.9rem; color:#2d5a3b;">
                        <i class="bi bi-check-circle-fill mt-1" style="color:var(--primary-light); flex-shrink:0;"></i>
                        Carbon sequestration tracking per district
                    </li>
                    <li class="d-flex align-items-start gap-2 mb-2" style="font-size:.9rem; color:#2d5a3b;">
                        <i class="bi bi-check-circle-fill mt-1" style="color:var(--primary-light); flex-shrink:0;"></i>
                        Species diversity monitoring in restored zones
                    </li>
                    <li class="d-flex align-items-start gap-2 mb-2" style="font-size:.9rem; color:#2d5a3b;">
                        <i class="bi bi-check-circle-fill mt-1" style="color:var(--primary-light); flex-shrink:0;"></i>
                        Land degradation reversal metrics
                    </li>
                    <li class="d-flex align-items-start gap-2" style="font-size:.9rem; color:#2d5a3b;">
                        <i class="bi bi-check-circle-fill mt-1" style="color:var(--primary-light); flex-shrink:0;"></i>
                        National reforestation goal compliance reports
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     CTA
═══════════════════════════════════════════ -->
<section id="contact" class="cta-section">
    <div class="container text-center" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <span class="section-label" style="background:rgba(76,199,106,.15); color:var(--accent-soft);">Join the Movement</span>
                <h2 class="section-title mt-2 mb-3" style="color:#fff;">
                    Start Protecting Your<br>Environment Today
                </h2>
                <p style="color:rgba(255,255,255,.65); font-size:1rem; line-height:1.7; margin-bottom:2rem;">
                    Whether you're a district planner, national environmental agency, or global
                    conservation body — Olives BI gives you the intelligence to act before damage is done.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('auth.register') }}" class="btn btn-cta-white">
                        <i class="bi bi-person-plus me-2"></i>Create Free Account
                    </a>
                    <a href="{{ route('auth.index') }}" class="btn btn-cta-outline">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════ -->
<footer>
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="footer-logo">🌿 Olives <span>BI</span></div>
                <div class="footer-tagline">Environmental Protection Intelligence</div>
                <p style="color:rgba(255,255,255,.4); font-size:.82rem; margin-top:.75rem; line-height:1.65;">
                    Designed with the environment at its core — guiding land-use decisions
                    to protect the planet for future generations.
                </p>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="footer-heading">Platform</div>
                <a href="#features" class="footer-link">Features</a>
                <a href="#coverage" class="footer-link">Coverage</a>
                <a href="#how-it-works" class="footer-link">How It Works</a>
                <a href="#mission" class="footer-link">Mission</a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="footer-heading">Domains</div>
                <a href="#features" class="footer-link">Forest Reserves</a>
                <a href="#features" class="footer-link">Ramsar Wetlands</a>
                <a href="#features" class="footer-link">Flood Zones</a>
                <a href="#features" class="footer-link">Air Quality</a>
                <a href="#features" class="footer-link">Waste Management</a>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="footer-heading">Get Involved</div>
                <a href="{{ route('auth.register') }}" class="footer-link">Create Account</a>
                <a href="{{ route('auth.index') }}" class="footer-link">Sign In</a>
                <a href="#contact" class="footer-link">Contact Us</a>
            </div>
        </div>

        <hr class="footer-divider">
        <div class="d-flex flex-wrap justify-content-between align-items-center footer-bottom">
            <span>© {{ date('Y') }} Olives BI. Built with purpose — for the planet.</span>
            <span style="color:var(--accent); font-size:.78rem;">🌍 Protecting Earth Through Data</span>
        </div>
    </div>
</footer>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true, offset: 60 });

    // ── Navbar scroll effect
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 50);
    });

    // ── Hero Slider
    class HeroSlider {
        constructor() {
            this.slides   = Array.from(document.querySelectorAll('.hero-slide'));
            this.label    = document.getElementById('slideLabel');
            this.dotsWrap = document.getElementById('slideDots');
            this.current  = 0;
            this.total    = this.slides.length;
            this.timer    = null;
            this.DURATION = 7500;

            // Immediately pause every video except the first slide's —
            // they all have autoplay in HTML so the browser fires them;
            // we only want slide 0 playing on load.
            this.slides.forEach((slide, i) => {
                const vid = slide.querySelector('video');
                if (!vid) return;
                if (i === 0) {
                    vid.play().catch(() => {});   // ensure slide 1 plays
                } else {
                    vid.pause();
                    vid.currentTime = 0;
                }
            });

            this._buildDots();
            this._updateUI(0);
            this._startTimer();

            document.getElementById('slidePrev').addEventListener('click', () => this.prev());
            document.getElementById('slideNext').addEventListener('click', () => this.next());

            document.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft')  this.prev();
                if (e.key === 'ArrowRight') this.next();
            });

            let tx = 0;
            const hero = document.getElementById('hero');
            hero.addEventListener('touchstart', e => { tx = e.changedTouches[0].screenX; }, { passive: true });
            hero.addEventListener('touchend',   e => {
                const dx = e.changedTouches[0].screenX - tx;
                if (Math.abs(dx) > 50) dx > 0 ? this.prev() : this.next();
            }, { passive: true });
        }

        _buildDots() {
            this.slides.forEach((_, i) => {
                const d = document.createElement('button');
                d.className = 'slide-dot';
                d.setAttribute('aria-label', 'Slide ' + (i + 1));
                d.addEventListener('click', () => this._goTo(i));
                this.dotsWrap.appendChild(d);
            });
            this.dots = Array.from(this.dotsWrap.querySelectorAll('.slide-dot'));
        }

        _updateUI(index) {
            this.dots.forEach((d, i) => d.classList.toggle('active', i === index));
            this.label.textContent = this.slides[index].dataset.label || '';
        }

        _goTo(index) {
            clearTimeout(this.timer);

            // Hide & pause outgoing slide
            const old = this.slides[this.current];
            old.classList.remove('active');
            const oldVid = old.querySelector('video');
            if (oldVid) { oldVid.pause(); oldVid.currentTime = 0; }

            // Show & play incoming slide
            this.current = index;
            const cur = this.slides[index];
            cur.classList.add('active');
            const vid = cur.querySelector('video');
            if (vid) {
                vid.currentTime = 0;
                vid.play().catch(() => {
                    // autoplay blocked — retry on next user click
                    document.addEventListener('click', () => vid.play().catch(() => {}), { once: true });
                });
            }

            this._updateUI(index);
            this._startTimer();
        }

        _startTimer() {
            this.timer = setTimeout(() => this._goTo((this.current + 1) % this.total), this.DURATION);
        }

        next() { this._goTo((this.current + 1) % this.total); }
        prev() { this._goTo((this.current - 1 + this.total) % this.total); }
    }

    new HeroSlider();
</script>
</body>
</html>

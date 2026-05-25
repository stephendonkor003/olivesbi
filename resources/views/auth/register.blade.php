<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account — Olives BI</title>
    <meta name="description" content="Create your Olives BI account and access environmental and revenue intelligence.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">

    <style>
        :root {
            --primary:       #1a5c2a;
            --primary-light: #2d9e4b;
            --accent:        #4cc76a;
            --dark-bg:       #071a0d;
            --light-green:   #e8f5e9;
            --muted:         #6b7280;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; flex-direction: column; background: #f4faf5; color: #1a2e1e; }

        /* ── TOPBAR ── */
        .reg-topbar {
            background: rgba(7,26,13,.97); backdrop-filter: blur(10px);
            padding: .75rem 0; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 16px rgba(0,0,0,.2);
        }
        .reg-topbar .inner {
            max-width: 1200px; margin: 0 auto; padding: 0 1.25rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .reg-logo { display: flex; align-items: center; gap: .55rem; text-decoration: none; }
        .reg-logo .leaf {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            border-radius: 50% 10px 50% 10px;
            display: flex; align-items: center; justify-content: center; font-size: .95rem;
        }
        .reg-logo .wordmark { font-family: 'Poppins', sans-serif; font-weight: 900; font-size: 1.25rem; color: #fff; line-height: 1; }
        .reg-logo .wordmark em { color: var(--accent); font-style: normal; }
        .reg-topbar-right { display: flex; align-items: center; gap: .75rem; font-size: .85rem; color: rgba(255,255,255,.55); }
        .reg-topbar-right .lbl { display: none; }
        @media (min-width: 480px) { .reg-topbar-right .lbl { display: inline; } }
        .btn-signin-top {
            background: rgba(255,255,255,.1); border: 1.5px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.88); border-radius: 8px; padding: .32rem .9rem;
            font-weight: 600; text-decoration: none; font-size: .83rem; transition: all .2s;
        }
        .btn-signin-top:hover { background: rgba(255,255,255,.17); color: #fff; }

        /* ── BODY SPLIT ── */
        .reg-body { flex: 1; display: flex; min-height: 0; }

        /* ── LEFT PANEL ── */
        .reg-side {
            width: 340px; flex-shrink: 0;
            background: linear-gradient(170deg, #071a0d 0%, #0e3318 55%, #1a5c2a 100%);
            position: sticky; top: 57px; height: calc(100vh - 57px);
            overflow-y: auto; overflow-x: hidden;
            display: flex; flex-direction: column;
            padding: 2rem 1.75rem;
        }
        .reg-side::before {
            content: ''; position: absolute;
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(76,199,106,.09), transparent 65%);
            top: -100px; right: -160px; pointer-events: none;
        }
        .reg-side::after {
            content: ''; position: absolute;
            width: 280px; height: 280px; border-radius: 50%;
            background: radial-gradient(circle, rgba(45,158,75,.08), transparent 65%);
            bottom: -80px; left: -70px; pointer-events: none;
        }
        .side-z { position: relative; z-index: 2; flex: 1; display: flex; flex-direction: column; }

        .side-headline { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.35rem; color: #fff; line-height: 1.25; margin-bottom: .6rem; }
        .side-sub { color: rgba(255,255,255,.52); font-size: .83rem; line-height: 1.7; margin-bottom: 1.75rem; }

        .side-benefits { list-style: none; flex: 1; }
        .side-benefits li {
            display: flex; gap: .75rem; align-items: flex-start;
            padding: .7rem 0; border-top: 1px solid rgba(255,255,255,.07);
        }
        .side-benefits li:first-child { border-top: 0; }
        .sb-icon {
            width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
            background: rgba(76,199,106,.13);
            display: flex; align-items: center; justify-content: center;
            color: var(--accent); font-size: .9rem;
        }
        .sb-title { display: block; color: rgba(255,255,255,.88); font-size: .82rem; font-weight: 600; }
        .sb-desc  { color: rgba(255,255,255,.42); font-size: .73rem; line-height: 1.45; margin-top: .1rem; }

        .side-stats {
            display: grid; grid-template-columns: 1fr 1fr; gap: .55rem; margin-top: 1.5rem;
        }
        .ss-item {
            background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px; padding: .7rem;
        }
        .ss-num { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.1rem; color: var(--accent); line-height: 1; }
        .ss-lbl { font-size: .64rem; color: rgba(255,255,255,.4); margin-top: .18rem; }

        /* ── FORM MAIN ── */
        .reg-main {
            flex: 1; padding: 2rem 2rem; min-width: 0;
            display: flex; flex-direction: column; align-items: center;
        }
        .reg-form-wrap { width: 100%; max-width: 680px; }

        /* ── CARDS ── */
        .rf-card {
            background: #fff; border-radius: 20px;
            border: 1.5px solid rgba(26,92,42,.09);
            box-shadow: 0 4px 24px rgba(26,92,42,.08);
            margin-bottom: 1.25rem; overflow: hidden;
        }
        .rf-card-head {
            padding: 1.5rem 1.75rem 1.25rem;
            border-bottom: 1px solid rgba(26,92,42,.07);
            display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;
        }
        .rf-card-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.15rem; color: #1a2e1e; margin-bottom: .18rem; }
        .rf-card-sub { font-size: .83rem; color: var(--muted); line-height: 1.5; margin: 0; }
        .rf-card-body { padding: 1.5rem 1.75rem; }

        /* Mode toggle */
        .mode-toggle {
            display: inline-flex; gap: .25rem; padding: .2rem;
            background: var(--light-green); border: 1px solid rgba(26,92,42,.12);
            border-radius: 9px; flex-shrink: 0;
        }
        .mode-btn {
            border: 0; border-radius: 7px; background: transparent;
            color: var(--primary); font-weight: 700; font-size: .78rem;
            padding: .35rem .7rem; cursor: pointer; transition: all .2s;
        }
        .mode-btn.active { background: #fff; box-shadow: 0 2px 8px rgba(26,92,42,.12); }

        /* Alerts */
        .reg-alert {
            display: flex; align-items: flex-start; gap: .55rem;
            padding: .75rem 1rem; border-radius: 10px; font-size: .85rem;
            margin-bottom: 1rem; line-height: 1.5;
        }
        .reg-alert.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .reg-alert.danger  { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        /* Section titles inside cards */
        .sec-ttl {
            display: flex; align-items: center; gap: .5rem;
            font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .88rem;
            color: var(--primary); margin: 1.4rem 0 .85rem;
            padding-bottom: .4rem; border-bottom: 1.5px solid var(--light-green);
        }
        .sec-ttl:first-child { margin-top: 0; }

        /* Form fields */
        .form-label {
            font-size: .7rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: .5px;
            color: var(--primary); margin-bottom: .32rem;
        }
        .form-control, .form-select {
            border: 1.5px solid rgba(26,92,42,.18); border-radius: 10px;
            min-height: 44px; font-size: .9rem; color: #1a2e1e;
            transition: border .2s, box-shadow .2s; font-family: 'Inter', sans-serif;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(45,158,75,.12);
        }
        .form-control.is-invalid, .form-select.is-invalid { border-color: #e74c3c; }
        .invalid-feedback { font-size: .76rem; }

        /* Password */
        .pw-wrap { position: relative; }
        .pw-wrap .form-control { padding-right: 3rem; }
        .pw-eye {
            position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer;
            padding: 0; font-size: 1rem; line-height: 1; z-index: 5; transition: color .2s;
        }
        .pw-eye:hover { color: var(--primary); }
        .pw-strength { display: flex; gap: .25rem; margin-top: .4rem; }
        .pw-bar { flex: 1; height: 4px; border-radius: 2px; background: rgba(26,92,42,.1); transition: background .3s; }
        .pw-bar.weak   { background: #e74c3c; }
        .pw-bar.fair   { background: #f59e0b; }
        .pw-bar.good   { background: var(--primary-light); }
        .pw-bar.strong { background: var(--primary); }
        .pw-hint { font-size: .72rem; color: var(--muted); margin-top: .26rem; }

        /* Account type cards */
        .type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
        .type-card {
            border: 1.5px solid rgba(26,92,42,.18); border-radius: 12px;
            padding: 1rem 1rem .85rem; cursor: pointer;
            transition: border-color .2s, background .2s;
        }
        .type-card:has(input:checked) { border-color: var(--primary-light); background: var(--light-green); }
        .type-card input { accent-color: var(--primary); }
        .tc-head { display: flex; align-items: center; gap: .6rem; margin-bottom: .35rem; }
        .tc-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: var(--light-green);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0; transition: background .2s;
        }
        .type-card:has(input:checked) .tc-icon { background: rgba(26,92,42,.15); }
        .tc-name { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: .9rem; color: #1a2e1e; }
        .tc-desc { font-size: .77rem; color: var(--muted); line-height: 1.5; }

        .plan-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }
        .plan-card {
            border: 1.5px solid rgba(26,92,42,.18); border-radius: 12px;
            padding: .95rem; cursor: pointer; background: #fff;
            transition: border-color .2s, background .2s, transform .2s;
        }
        .plan-card:hover { transform: translateY(-1px); }
        .plan-card:has(input:checked) { border-color: var(--primary-light); background: var(--light-green); }
        .plan-card input { accent-color: var(--primary); }
        .pc-head { display: flex; align-items: center; gap: .55rem; margin-bottom: .35rem; }
        .pc-icon { width: 32px; height: 32px; border-radius: 9px; background: var(--light-green); display: flex; align-items: center; justify-content: center; color: var(--primary); }
        .plan-card:has(input:checked) .pc-icon { background: rgba(26,92,42,.15); }
        .pc-name { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .82rem; color: #1a2e1e; }
        .pc-desc { font-size: .73rem; color: var(--muted); line-height: 1.45; }

        /* Newsletter */
        .nl-box {
            background: #f3fbf5; border: 1.5px solid rgba(45,158,75,.2);
            border-radius: 12px; padding: 1.25rem;
        }
        .topic-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .45rem; margin-top: .85rem; }
        .topic-chip {
            display: flex; align-items: center; gap: .45rem;
            min-height: 40px; padding: .45rem .7rem;
            border-radius: 8px; background: #fff;
            border: 1px solid rgba(26,92,42,.12);
            font-size: .83rem; font-weight: 600; cursor: pointer;
            transition: border-color .2s, background .2s;
        }
        .topic-chip:has(input:checked) { border-color: var(--primary-light); background: var(--light-green); }
        .topic-chip input { accent-color: var(--primary); }

        /* Submit row */
        .reg-submit-row {
            display: flex; align-items: center; gap: 1.25rem;
            padding: 1.25rem 1.75rem; border-top: 1px solid rgba(26,92,42,.08);
        }
        .fine-print { font-size: .77rem; color: var(--muted); line-height: 1.55; flex: 1; margin: 0; }
        .btn-create {
            height: 50px; min-width: 180px; border: none; border-radius: 12px; flex-shrink: 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff; font-family: 'Poppins', sans-serif;
            font-weight: 700; font-size: .93rem; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            transition: all .25s;
        }
        .btn-create:hover { transform: translateY(-2px); box-shadow: 0 8px 26px rgba(26,92,42,.3); }

        .reg-signin-row { text-align: center; font-size: .87rem; color: var(--muted); margin-top: .75rem; margin-bottom: 2rem; }
        .reg-signin-row a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .reg-signin-row a:hover { text-decoration: underline; }

        /* Utilities */
        .is-hidden { display: none !important; }
        .advanced-fields { display: none; }
        body.show-advanced .advanced-fields { display: block; }
        .iti { width: 100%; }
        .iti .form-control { padding-left: 3.25rem; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .reg-body { flex-direction: column; }
            .reg-side {
                width: 100%; position: static; height: auto;
                flex-direction: row; padding: 1.25rem 1.25rem;
            }
            .side-headline { font-size: 1.1rem; margin-bottom: 0; }
            .side-sub, .side-benefits, .side-stats { display: none; }
            .reg-main { padding: 1.5rem 1rem; }
            .plan-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 600px) {
            .type-grid { grid-template-columns: 1fr; }
            .plan-grid { grid-template-columns: 1fr; }
            .topic-grid { grid-template-columns: 1fr; }
            .rf-card-body { padding: 1.1rem 1rem; }
            .rf-card-head { padding: 1.1rem 1rem; flex-direction: column; }
            .reg-submit-row { flex-direction: column; align-items: stretch; padding: 1rem; }
            .btn-create { min-width: 100%; }
            .reg-main { padding: 1rem .75rem; }
        }
    </style>
</head>
<body>

<!-- ── TOPBAR ── -->
<header class="reg-topbar">
    <div class="inner">
        <a href="{{ route('auth.LandingPage') }}" class="reg-logo">
            <div class="leaf">🌿</div>
            <div class="wordmark">Olives <em>BI</em></div>
        </a>
        <div class="reg-topbar-right">
            <span class="lbl">Already have an account?</span>
            <a href="{{ route('auth.index') }}" class="btn-signin-top"><i class="bi bi-box-arrow-in-right me-1"></i>Sign In</a>
        </div>
    </div>
</header>

<!-- ── BODY ── -->
<div class="reg-body">

    <!-- LEFT PANEL -->
    <aside class="reg-side">
        <div class="side-z">
            <div>
                <div class="side-headline">One account.<br>Full intelligence.</div>
                <p class="side-sub">Register to access real-time environmental health data, IGF revenue dashboards, and land intelligence for any locality in Ghana.</p>

                <ul class="side-benefits">
                    <li>
                        <div class="sb-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <span class="sb-title">Locality Health Scores</span>
                            <span class="sb-desc">Live AQI, waste risk, flood zones, and health ratings for searched areas.</span>
                        </div>
                    </li>
                    <li>
                        <div class="sb-icon"><i class="bi bi-building-fill-gear"></i></div>
                        <div>
                            <span class="sb-title">IGF Revenue Intelligence</span>
                            <span class="sb-desc">Track building permits, land rates, business licences, and compliance by district.</span>
                        </div>
                    </li>
                    <li>
                        <div class="sb-icon"><i class="bi bi-envelope-paper-fill"></i></div>
                        <div>
                            <span class="sb-title">Smart Alerts</span>
                            <span class="sb-desc">Subscribe to environmental and revenue updates for areas you care about.</span>
                        </div>
                    </li>
                    <li>
                        <div class="sb-icon"><i class="bi bi-shield-check-fill"></i></div>
                        <div>
                            <span class="sb-title">Secure &amp; Private</span>
                            <span class="sb-desc">Your data is encrypted. Sign in with your phone or email after registration.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="side-stats">
                <div class="ss-item"><div class="ss-num">Live</div><div class="ss-lbl">Environmental data</div></div>
                <div class="ss-item"><div class="ss-num">8+</div><div class="ss-lbl">IGF Revenue Streams</div></div>
                <div class="ss-item"><div class="ss-num">GIS</div><div class="ss-lbl">Powered intelligence</div></div>
                <div class="ss-item"><div class="ss-num">Free</div><div class="ss-lbl">To get started</div></div>
            </div>
        </div>
    </aside>

    <!-- FORM AREA -->
    <main class="reg-main">
        <div class="reg-form-wrap">

            @if (session()->has('status'))
                <div class="reg-alert success">
                    <i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:.1rem;"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="reg-alert danger">
                    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:.1rem;"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="reg-alert danger">
                    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:.1rem;"></i>
                    Please check the highlighted fields below and try again.
                </div>
            @endif

            <form action="{{ route('citizens.frontstore') }}" method="POST" id="register-form">
                @csrf

                <!-- CARD 1: Type + mode -->
                <div class="rf-card">
                    <div class="rf-card-head">
                        <div>
                            <div class="rf-card-title">Create your account</div>
                            <p class="rf-card-sub">Fill in the required fields. Switch to Advanced for additional details.</p>
                        </div>
                        <div class="mode-toggle" aria-label="Registration mode">
                            <button type="button" class="mode-btn active" data-mode="simple">Simple</button>
                            <button type="button" class="mode-btn" data-mode="advanced">Advanced</button>
                        </div>
                    </div>
                    <div class="rf-card-body">
                        <div class="sec-ttl"><i class="bi bi-layers"></i> Account Intent</div>
                        <div class="plan-grid">
                            <label class="plan-card">
                                <div class="pc-head">
                                    <input type="radio" name="account_plan" value="subscriber" @checked(old('account_plan') === 'subscriber')>
                                    <div class="pc-icon"><i class="bi bi-newspaper"></i></div>
                                    <div class="pc-name">News & Environment Subscriber</div>
                                </div>
                                <div class="pc-desc">For environmental news, updates, public notices, and research.</div>
                            </label>
                            <label class="plan-card">
                                <div class="pc-head">
                                    <input type="radio" name="account_plan" value="account" @checked(old('account_plan', 'account') === 'account')>
                                    <div class="pc-icon"><i class="bi bi-house-check"></i></div>
                                    <div class="pc-name">Olives Account</div>
                                </div>
                                <div class="pc-desc">For property and business holders checking safety, taxes, levies, permits, and fines.</div>
                            </label>
                            <label class="plan-card">
                                <div class="pc-head">
                                    <input type="radio" name="account_plan" value="pro" @checked(old('account_plan') === 'pro')>
                                    <div class="pc-icon"><i class="bi bi-bank"></i></div>
                                    <div class="pc-name">Olives Pro</div>
                                </div>
                                <div class="pc-desc">For assemblies, municipalities, and districts managing one jurisdiction.</div>
                            </label>
                            <label class="plan-card advanced-fields">
                                <div class="pc-head">
                                    <input type="radio" name="account_plan" value="sky" @checked(old('account_plan') === 'sky')>
                                    <div class="pc-icon"><i class="bi bi-globe2"></i></div>
                                    <div class="pc-name">Olives Sky</div>
                                </div>
                                <div class="pc-desc">For national users managing regions, municipalities, districts, and assemblies.</div>
                            </label>
                            <label class="plan-card advanced-fields">
                                <div class="pc-head">
                                    <input type="radio" name="account_plan" value="cloud" @checked(old('account_plan') === 'cloud')>
                                    <div class="pc-icon"><i class="bi bi-cloud-check"></i></div>
                                    <div class="pc-name">Olives Cloud</div>
                                </div>
                                <div class="pc-desc">For regional bodies such as ECOWAS overseeing multiple countries.</div>
                            </label>
                            <label class="plan-card advanced-fields">
                                <div class="pc-head">
                                    <input type="radio" name="account_plan" value="shell" @checked(old('account_plan') === 'shell')>
                                    <div class="pc-icon"><i class="bi bi-globe-europe-africa"></i></div>
                                    <div class="pc-name">Olives Shell</div>
                                </div>
                                <div class="pc-desc">For continental institutions such as the African Union or European Union.</div>
                            </label>
                        </div>
                        @error('account_plan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                        <div class="sec-ttl"><i class="bi bi-ui-checks-grid"></i> Registration Type</div>
                        <div class="type-grid">
                            <label class="type-card">
                                <div class="tc-head">
                                    <input type="radio" name="registration_type" value="individual" @checked(old('registration_type','individual')==='individual')>
                                    <div class="tc-icon">👤</div>
                                    <div class="tc-name">Individual</div>
                                </div>
                                <div class="tc-desc">For residents, rate payers, property owners, and general platform users.</div>
                            </label>
                            <label class="type-card">
                                <div class="tc-head">
                                    <input type="radio" name="registration_type" value="organization" @checked(old('registration_type')==='organization')>
                                    <div class="tc-icon">🏢</div>
                                    <div class="tc-name">Organisation</div>
                                </div>
                                <div class="tc-desc">For businesses, institutions, NGOs, and operational teams.</div>
                            </label>
                        </div>
                        @error('registration_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- CARD 2: Individual details -->
                <div class="rf-card" id="individual-fields">
                    <div class="rf-card-head">
                        <div>
                            <div class="rf-card-title">Personal Details</div>
                            <p class="rf-card-sub">Your name, phone number, and login credentials.</p>
                        </div>
                    </div>
                    <div class="rf-card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="first_name">First Name</label>
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                                       class="form-control @error('first_name') is-invalid @enderror" placeholder="First name">
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="last_name">Last Name</label>
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                                       class="form-control @error('last_name') is-invalid @enderror" placeholder="Last name">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="telephone_number">Phone Number</label>
                                <input id="telephone_number" type="tel" name="telephone_number"
                                       value="{{ old('telephone_number') }}"
                                       class="form-control @error('telephone_number') is-invalid @enderror"
                                       placeholder="Phone number">
                                @error('telephone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-sm-6">
                                <label class="form-label" for="password">Password</label>
                                <div class="pw-wrap">
                                    <input id="password" type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Create a password"
                                           oninput="checkRegPwStrength(this.value,'reg-pw-bar','reg-pw-hint')">
                                    <button type="button" class="pw-eye" onclick="toggleRegPw('password',this)"><i class="bi bi-eye"></i></button>
                                </div>
                                <div class="pw-strength">
                                    <div class="pw-bar" id="reg-pw-bar-1"></div>
                                    <div class="pw-bar" id="reg-pw-bar-2"></div>
                                    <div class="pw-bar" id="reg-pw-bar-3"></div>
                                    <div class="pw-bar" id="reg-pw-bar-4"></div>
                                </div>
                                <div class="pw-hint" id="reg-pw-hint">Min 8 characters.</div>
                                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="password_confirmation">Confirm Password</label>
                                <div class="pw-wrap">
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                           class="form-control" placeholder="Re-enter password">
                                    <button type="button" class="pw-eye" onclick="toggleRegPw('password_confirmation',this)"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced individual -->
                        <div class="advanced-fields">
                            <div class="sec-ttl"><i class="bi bi-sliders"></i> Additional Details <span style="font-size:.73rem;font-weight:400;color:var(--muted);">(optional)</span></div>
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <label class="form-label" for="gender">Gender</label>
                                    <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">Select</option>
                                        <option value="Male"   @selected(old('gender')==='Male')>Male</option>
                                        <option value="Female" @selected(old('gender')==='Female')>Female</option>
                                        <option value="Other"  @selected(old('gender')==='Other')>Other</option>
                                    </select>
                                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label" for="date_of_birth">Date of Birth</label>
                                    <input id="date_of_birth" type="date" name="date_of_birth"
                                           value="{{ old('date_of_birth') }}"
                                           class="form-control @error('date_of_birth') is-invalid @enderror">
                                    @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label" for="country_of_citizenship">Citizenship</label>
                                    <input id="country_of_citizenship" type="text" name="country_of_citizenship"
                                           value="{{ old('country_of_citizenship') }}"
                                           class="form-control @error('country_of_citizenship') is-invalid @enderror"
                                           placeholder="e.g. Ghanaian">
                                    @error('country_of_citizenship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="id_type">ID Type</label>
                                    <input id="id_type" type="text" name="id_type" value="{{ old('id_type') }}"
                                           class="form-control @error('id_type') is-invalid @enderror"
                                           placeholder="Ghana Card, Passport, Voter ID">
                                    @error('id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="id_number">ID Number</label>
                                    <input id="id_number" type="text" name="id_number" value="{{ old('id_number') }}"
                                           class="form-control @error('id_number') is-invalid @enderror">
                                    @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Organization details -->
                <div class="rf-card is-hidden" id="organization-fields">
                    <div class="rf-card-head">
                        <div>
                            <div class="rf-card-title">Organisation Details</div>
                            <p class="rf-card-sub">Organisation name, contact person, and login credentials.</p>
                        </div>
                    </div>
                    <div class="rf-card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="business_name">Organisation Name</label>
                                <input id="business_name" type="text" name="business_name"
                                       value="{{ old('business_name') }}"
                                       class="form-control @error('business_name') is-invalid @enderror"
                                       placeholder="Registered organisation name">
                                @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6 advanced-fields">
                                <label class="form-label" for="tin_number">TIN <span style="font-weight:400;color:var(--muted);">(optional)</span></label>
                                <input id="tin_number" type="text" name="tin_number" value="{{ old('tin_number') }}"
                                       class="form-control @error('tin_number') is-invalid @enderror">
                                @error('tin_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="org_first_name">Contact First Name</label>
                                <input id="org_first_name" type="text" name="org_first_name"
                                       value="{{ old('org_first_name') }}"
                                       class="form-control @error('org_first_name') is-invalid @enderror"
                                       placeholder="First name">
                                @error('org_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="org_last_name">Contact Last Name</label>
                                <input id="org_last_name" type="text" name="org_last_name"
                                       value="{{ old('org_last_name') }}"
                                       class="form-control @error('org_last_name') is-invalid @enderror"
                                       placeholder="Last name">
                                @error('org_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="org_telephone_number">Contact Phone</label>
                                <input id="org_telephone_number" type="tel" name="org_telephone_number"
                                       value="{{ old('org_telephone_number') }}"
                                       class="form-control @error('org_telephone_number') is-invalid @enderror"
                                       placeholder="Phone number">
                                @error('org_telephone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6 advanced-fields">
                                <label class="form-label" for="date_of_commencement">Start Date <span style="font-weight:400;color:var(--muted);">(optional)</span></label>
                                <input id="date_of_commencement" type="date" name="date_of_commencement"
                                       value="{{ old('date_of_commencement') }}"
                                       class="form-control @error('date_of_commencement') is-invalid @enderror">
                                @error('date_of_commencement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="org_password">Password</label>
                                <div class="pw-wrap">
                                    <input id="org_password" type="password" name="org_password"
                                           class="form-control @error('org_password') is-invalid @enderror"
                                           placeholder="Create a password"
                                           oninput="checkRegPwStrength(this.value,'org-pw-bar','org-pw-hint')">
                                    <button type="button" class="pw-eye" onclick="toggleRegPw('org_password',this)"><i class="bi bi-eye"></i></button>
                                </div>
                                <div class="pw-strength">
                                    <div class="pw-bar" id="org-pw-bar-1"></div>
                                    <div class="pw-bar" id="org-pw-bar-2"></div>
                                    <div class="pw-bar" id="org-pw-bar-3"></div>
                                    <div class="pw-bar" id="org-pw-bar-4"></div>
                                </div>
                                <div class="pw-hint" id="org-pw-hint">Min 8 characters.</div>
                                @error('org_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="org_password_confirmation">Confirm Password</label>
                                <div class="pw-wrap">
                                    <input id="org_password_confirmation" type="password" name="org_password_confirmation"
                                           class="form-control" placeholder="Re-enter password">
                                    <button type="button" class="pw-eye" onclick="toggleRegPw('org_password_confirmation',this)"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: Newsletter + submit -->
                <div class="rf-card">
                    <div class="rf-card-head">
                        <div>
                            <div class="rf-card-title">Updates &amp; Newsletter</div>
                            <p class="rf-card-sub">Optional — subscribe to intelligence alerts for the topics you care about.</p>
                        </div>
                    </div>
                    <div class="rf-card-body">
                        <div class="nl-box">
                            <div class="row g-3 align-items-start">
                                <div class="col-sm-7">
                                    <label class="form-label" for="email">Email Address</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="name@example.com">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-5 pt-sm-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                               id="newsletter_opt_in" name="newsletter_opt_in"
                                               @checked(old('newsletter_opt_in'))>
                                        <label class="form-check-label fw-bold" for="newsletter_opt_in">Subscribe me</label>
                                    </div>
                                    <div class="pw-hint mt-1">Email required when subscribing.</div>
                                </div>
                            </div>
                            <div class="topic-grid">
                                <label class="topic-chip">
                                    <input type="checkbox" name="newsletter_topics[]" value="environment"
                                           @checked(in_array('environment', old('newsletter_topics', [])))>
                                    🌿 Environmental alerts
                                </label>
                                <label class="topic-chip">
                                    <input type="checkbox" name="newsletter_topics[]" value="waste"
                                           @checked(in_array('waste', old('newsletter_topics', [])))>
                                    🗑️ Waste management
                                </label>
                                <label class="topic-chip">
                                    <input type="checkbox" name="newsletter_topics[]" value="revenue"
                                           @checked(in_array('revenue', old('newsletter_topics', [])))>
                                    💰 Revenue updates
                                </label>
                                <label class="topic-chip">
                                    <input type="checkbox" name="newsletter_topics[]" value="locality"
                                           @checked(in_array('locality', old('newsletter_topics', [])))>
                                    📍 Locality intelligence
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="reg-submit-row">
                        <p class="fine-print">By registering, you create an account on Olives BI. You can sign in immediately after registration.</p>
                        <button type="submit" class="btn-create">
                            <i class="bi bi-arrow-right-circle-fill"></i> Create Account
                        </button>
                    </div>
                </div>

            </form>

            <div class="reg-signin-row">
                Already have an account? <a href="{{ route('auth.index') }}">Sign in here</a>
            </div>

        </div>
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
<script>
    /* ── Account type ── */
    const radios = document.querySelectorAll('input[name="registration_type"]');
    const individualFields   = document.getElementById('individual-fields');
    const organizationFields = document.getElementById('organization-fields');

    function syncAccountType() {
        const selected = document.querySelector('input[name="registration_type"]:checked')?.value || 'individual';
        individualFields.classList.toggle('is-hidden', selected !== 'individual');
        organizationFields.classList.toggle('is-hidden', selected !== 'organization');
    }
    radios.forEach(r => r.addEventListener('change', syncAccountType));
    syncAccountType();

    /* ── Mode toggle ── */
    const modeButtons = document.querySelectorAll('[data-mode]');
    const shouldShowAdvanced = {{ (old('gender') || old('date_of_birth') || old('country_of_citizenship') || old('id_type') || old('id_number') || old('tin_number') || old('date_of_commencement') || in_array(old('account_plan'), ['sky', 'cloud', 'shell'], true)) ? 'true' : 'false' }};
    function setMode(mode) {
        document.body.classList.toggle('show-advanced', mode === 'advanced');
        modeButtons.forEach(b => b.classList.toggle('active', b.dataset.mode === mode));
    }
    modeButtons.forEach(b => b.addEventListener('click', () => setMode(b.dataset.mode)));
    setMode(shouldShowAdvanced ? 'advanced' : 'simple');

    /* ── Password visibility ── */
    function toggleRegPw(inputId, btn) {
        const inp = document.getElementById(inputId);
        if (!inp) return;
        const isText = inp.type === 'text';
        inp.type = isText ? 'password' : 'text';
        btn.innerHTML = isText ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    }

    /* ── Password strength ── */
    function checkRegPwStrength(val, barPrefix, hintId) {
        let score = 0;
        if (val.length >= 8)            score++;
        if (/[A-Z]/.test(val))          score++;
        if (/[0-9]/.test(val))          score++;
        if (/[^A-Za-z0-9]/.test(val))  score++;
        const levels = ['weak','fair','good','strong'];
        const labels = ['Too short','Fair — add numbers or symbols','Good — add a symbol','Strong password'];
        [1,2,3,4].forEach(i => {
            const bar = document.getElementById(barPrefix + '-' + i);
            if (bar) bar.className = 'pw-bar' + (i <= score ? ' ' + levels[score - 1] : '');
        });
        const hint = document.getElementById(hintId);
        if (hint) hint.textContent = val.length === 0 ? 'Min 8 characters.' : (labels[score - 1] || 'Too short');
    }

    /* ── intl-tel-input ── */
    const form = document.getElementById('register-form');
    const phoneInputs = [
        document.getElementById('telephone_number'),
        document.getElementById('org_telephone_number')
    ].filter(Boolean);
    const phonePickers = [];

    phoneInputs.forEach(input => {
        if (!window.intlTelInput) return;
        const picker = window.intlTelInput(input, {
            initialCountry: 'auto',
            nationalMode: false,
            separateDialCode: true,
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js',
            geoIpLookup: cb => {
                fetch('https://ipapi.co/json/')
                    .then(r => r.json())
                    .then(d => cb((d && d.country_code) ? d.country_code : 'GH'))
                    .catch(() => cb('GH'));
            },
        });
        phonePickers.push({ input, picker });
    });

    form.addEventListener('submit', e => {
        const selectedType = document.querySelector('input[name="registration_type"]:checked')?.value || 'individual';
        const activePhone  = selectedType === 'organization' ? 'org_telephone_number' : 'telephone_number';
        phonePickers.forEach(({ input, picker }) => {
            if (input.name !== activePhone || !input.value.trim()) return;
            if (picker.isValidNumber()) {
                input.value = picker.getNumber();
                input.classList.remove('is-invalid');
            } else {
                input.classList.add('is-invalid');
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>

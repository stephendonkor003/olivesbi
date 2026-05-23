<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Olives BI</title>
    <meta name="description" content="Sign in to your Olives BI environmental and revenue intelligence account.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:       #1a5c2a;
            --primary-light: #2d9e4b;
            --accent:        #4cc76a;
            --dark-bg:       #071a0d;
            --light-green:   #e8f5e9;
            --muted:         #6b7280;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; }
        body { display: flex; min-height: 100vh; background: #f4faf5; }

        /* ── SPLIT LAYOUT ── */
        .auth-panel {
            width: 420px; flex-shrink: 0;
            background: linear-gradient(160deg, #071a0d 0%, #0e3318 55%, #1a5c2a 100%);
            position: relative; overflow: hidden;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 2.5rem 2.25rem;
        }
        .auth-panel::before {
            content: '';
            position: absolute; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(76,199,106,.1), transparent 65%);
            top: -140px; right: -200px; border-radius: 50%; pointer-events: none;
        }
        .auth-panel::after {
            content: '';
            position: absolute; width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(45,158,75,.09), transparent 65%);
            bottom: -100px; left: -100px; border-radius: 50%; pointer-events: none;
        }
        .panel-z { position: relative; z-index: 2; }

        .panel-logo { display: flex; align-items: center; gap: .6rem; margin-bottom: 3rem; }
        .panel-logo .leaf {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            border-radius: 50% 12px 50% 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
            box-shadow: 0 0 16px rgba(76,199,106,.4);
        }
        .panel-logo .wordmark { font-family: 'Poppins', sans-serif; font-weight: 900; font-size: 1.4rem; color: #fff; line-height: 1; }
        .panel-logo .wordmark em { color: var(--accent); font-style: normal; }

        .panel-headline {
            font-family: 'Poppins', sans-serif; font-weight: 800;
            font-size: 1.75rem; color: #fff; line-height: 1.2; margin-bottom: .75rem;
        }
        .panel-sub { color: rgba(255,255,255,.58); font-size: .88rem; line-height: 1.75; margin-bottom: 2.25rem; }

        .panel-features { list-style: none; }
        .panel-features li {
            display: flex; gap: .8rem; align-items: flex-start;
            padding: .7rem 0; border-top: 1px solid rgba(255,255,255,.07);
        }
        .panel-features li:first-child { border-top: 0; }
        .pf-icon {
            width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
            background: rgba(76,199,106,.14);
            display: flex; align-items: center; justify-content: center;
            color: var(--accent); font-size: .95rem;
        }
        .pf-strong { display: block; color: rgba(255,255,255,.9); font-size: .84rem; font-weight: 600; }
        .pf-desc   { color: rgba(255,255,255,.48); font-size: .76rem; line-height: 1.45; margin-top: .1rem; }

        .panel-stats {
            display: grid; grid-template-columns: 1fr 1fr; gap: .6rem;
            margin-top: 2.5rem; position: relative; z-index: 2;
        }
        .ps-item {
            background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
            border-radius: 11px; padding: .85rem;
        }
        .ps-num { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.25rem; color: var(--accent); line-height: 1; }
        .ps-lbl { font-size: .68rem; color: rgba(255,255,255,.45); margin-top: .2rem; }

        /* ── FORM AREA ── */
        .auth-form-area {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 2.5rem 1.5rem; min-width: 0;
        }
        .auth-card {
            width: 100%; max-width: 440px;
            background: #fff; border-radius: 22px;
            box-shadow: 0 10px 50px rgba(26,92,42,.1);
            border: 1.5px solid rgba(26,92,42,.08);
            padding: 2.75rem 2.5rem;
        }
        .ac-greeting { font-size: 1.3rem; margin-bottom: .15rem; }
        .ac-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.55rem; color: #1a2e1e; margin-bottom: .35rem; }
        .ac-sub { font-size: .88rem; color: var(--muted); margin-bottom: 1.85rem; line-height: 1.55; }

        /* Alerts */
        .auth-alert {
            display: flex; align-items: flex-start; gap: .55rem;
            padding: .75rem 1rem; border-radius: 10px; font-size: .85rem;
            margin-bottom: 1.4rem; line-height: 1.5;
        }
        .auth-alert.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .auth-alert.danger  { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        /* Form fields */
        .af-group { margin-bottom: 1.15rem; }
        .af-label {
            display: block; font-size: .71rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: .5px;
            color: var(--primary); margin-bottom: .38rem;
        }
        .af-input {
            width: 100%; border: 1.5px solid rgba(26,92,42,.18); border-radius: 11px;
            padding: .72rem 1rem; font-size: .9rem; color: #1a2e1e;
            outline: none; transition: border .2s, box-shadow .2s;
            font-family: 'Inter', sans-serif; background: #fff;
        }
        .af-input:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(45,158,75,.12); }
        .af-input.is-invalid { border-color: #e74c3c; box-shadow: 0 0 0 3px rgba(231,76,60,.1); }
        .af-error { font-size: .75rem; color: #e74c3c; margin-top: .3rem; display: flex; align-items: center; gap: .3rem; }

        /* Password field */
        .pw-wrap { position: relative; }
        .pw-wrap .af-input { padding-right: 3.1rem; }
        .pw-eye {
            position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer;
            padding: 0; font-size: 1.05rem; line-height: 1; transition: color .2s;
        }
        .pw-eye:hover { color: var(--primary); }

        /* Row: remember + forgot */
        .af-meta { display: flex; justify-content: space-between; align-items: center; margin: .25rem 0 1.6rem; }
        .af-check-lbl { display: flex; align-items: center; gap: .45rem; font-size: .84rem; color: #374151; cursor: pointer; }
        .af-check-lbl input { accent-color: var(--primary); width: 15px; height: 15px; cursor: pointer; }
        .af-forgot { font-size: .84rem; color: var(--primary); text-decoration: none; font-weight: 600; }
        .af-forgot:hover { text-decoration: underline; }

        /* Submit */
        .btn-signin {
            width: 100%; height: 50px; border: none; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff; font-family: 'Poppins', sans-serif;
            font-weight: 700; font-size: .97rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: .55rem;
            transition: all .25s; letter-spacing: .01em;
        }
        .btn-signin:hover { transform: translateY(-2px); box-shadow: 0 8px 26px rgba(26,92,42,.32); }
        .btn-signin:active { transform: translateY(0); }

        /* Divider */
        .af-divider { display: flex; align-items: center; gap: .75rem; margin: 1.6rem 0; }
        .af-divider::before, .af-divider::after { content: ''; flex: 1; height: 1px; background: rgba(26,92,42,.1); }
        .af-divider span { font-size: .73rem; color: var(--muted); white-space: nowrap; }

        /* Switch link */
        .af-switch { text-align: center; font-size: .88rem; color: var(--muted); }
        .af-switch a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .af-switch a:hover { text-decoration: underline; }

        /* Footer links */
        .auth-page-footer { text-align: center; margin-top: 1.75rem; font-size: .78rem; color: var(--muted); }
        .auth-page-footer a { color: var(--muted); text-decoration: none; transition: color .2s; }
        .auth-page-footer a:hover { color: var(--primary); }
        .auth-page-footer .dot { margin: 0 .4rem; }

        /* ── MOBILE ── */
        @media (max-width: 820px) {
            body { flex-direction: column; }
            .auth-panel {
                width: 100%; flex-direction: row; align-items: center;
                gap: 1rem; padding: 1.1rem 1.25rem; justify-content: flex-start;
            }
            .panel-headline, .panel-sub, .panel-features, .panel-stats { display: none; }
            .panel-logo { margin-bottom: 0; }
            .panel-logo .leaf { width: 34px; height: 34px; font-size: .95rem; }
            .panel-logo .wordmark { font-size: 1.2rem; }
            .auth-form-area { padding: 1.75rem 1rem; justify-content: flex-start; }
            .auth-card { padding: 1.75rem 1.25rem; border-radius: 18px; }
            .ac-title { font-size: 1.35rem; }
        }

        @media (max-width: 480px) {
            .auth-card { padding: 1.5rem 1rem; }
            .af-meta { flex-direction: column; align-items: flex-start; gap: .75rem; }
        }
    </style>
</head>
<body>

    <!-- LEFT: Brand Panel -->
    <div class="auth-panel">
        <div class="panel-z">
            <div class="panel-logo">
                <div class="leaf">🌿</div>
                <div class="wordmark">Olives <em>BI</em></div>
            </div>

            <div class="panel-headline">Land Data.<br>Revenue Intelligence.<br>Greener Future.</div>
            <p class="panel-sub">Sign in to access real-time environmental risk assessments, land revenue dashboards, and geospatial intelligence for your locality.</p>

            <ul class="panel-features">
                <li>
                    <div class="pf-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <div>
                        <span class="pf-strong">Locality Intelligence</span>
                        <span class="pf-desc">Live AQI, waste risk, water bodies, and health scores for any searched area.</span>
                    </div>
                </li>
                <li>
                    <div class="pf-icon"><i class="bi bi-building-fill-gear"></i></div>
                    <div>
                        <span class="pf-strong">IGF Revenue Streams</span>
                        <span class="pf-desc">Building permits, land registration, business licences, and compliance tracking.</span>
                    </div>
                </li>
                <li>
                    <div class="pf-icon"><i class="bi bi-bar-chart-line-fill"></i></div>
                    <div>
                        <span class="pf-strong">Live Dashboards</span>
                        <span class="pf-desc">Environmental KPIs and revenue performance tracked in real-time.</span>
                    </div>
                </li>
            </ul>
        </div>

        <div class="panel-stats">
            <div class="ps-item"><div class="ps-num">8+</div><div class="ps-lbl">IGF Revenue Streams</div></div>
            <div class="ps-item"><div class="ps-num">Live</div><div class="ps-lbl">Environmental data</div></div>
            <div class="ps-item"><div class="ps-num">GIS</div><div class="ps-lbl">Powered intelligence</div></div>
            <div class="ps-item"><div class="ps-num">100%</div><div class="ps-lbl">Land-data driven</div></div>
        </div>
    </div>

    <!-- RIGHT: Form Area -->
    <div class="auth-form-area">
        <div class="auth-card">
            <div class="ac-greeting">👋</div>
            <div class="ac-title">Welcome back</div>
            <p class="ac-sub">Sign in to your Olives BI account to continue.</p>

            @if (session()->has('status'))
                <div class="auth-alert success">
                    <i class="bi bi-check-circle-fill" style="flex-shrink:0; margin-top:.1rem;"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('username') || $errors->has('password'))
                <div class="auth-alert danger">
                    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0; margin-top:.1rem;"></i>
                    Please check your credentials and try again.
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}">
                @csrf

                <div class="af-group">
                    <label class="af-label" for="username">Phone Number / Email</label>
                    <input class="af-input @error('username') is-invalid @enderror"
                           id="username" type="text" name="username"
                           placeholder="Enter your phone or email"
                           value="{{ old('username') }}" autocomplete="username" required>
                    @error('username')
                        <div class="af-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="af-group">
                    <label class="af-label" for="login_password">Password</label>
                    <div class="pw-wrap">
                        <input class="af-input @error('password') is-invalid @enderror"
                               id="login_password" type="password" name="password"
                               placeholder="Enter your password" required>
                        <button type="button" class="pw-eye" id="loginPwToggle" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="af-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="af-meta">
                    <label class="af-check-lbl">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('auth.sendOTP') }}" class="af-forgot">Forgot password?</a>
                </div>

                <button type="submit" class="btn-signin">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <div class="af-divider"><span>New to Olives BI?</span></div>

            <div class="af-switch">
                Don't have an account? <a href="{{ route('auth.register') }}">Get started — it's free</a>
            </div>
        </div>

        <div class="auth-page-footer">
            <a href="{{ route('auth.LandingPage') }}"><i class="bi bi-arrow-left"></i> Back to Home</a>
            <span class="dot">·</span>
            <a href="{{ route('locality.check') }}">Check Locality</a>
            <span class="dot">·</span>
            <a href="{{ route('igf.index') }}">Business We Support</a>
        </div>
    </div>

<script>
    const loginPwField  = document.getElementById('login_password');
    const loginPwToggle = document.getElementById('loginPwToggle');
    loginPwToggle.addEventListener('click', () => {
        const isText = loginPwField.type === 'text';
        loginPwField.type = isText ? 'password' : 'text';
        loginPwToggle.innerHTML = isText ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });
</script>
</body>
</html>

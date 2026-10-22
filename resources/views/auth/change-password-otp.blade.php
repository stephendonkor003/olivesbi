<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set or Reset Password — Olives BI</title>

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

        /* ── LEFT PANEL ── */
        .auth-panel {
            width: 420px; flex-shrink: 0;
            background: linear-gradient(160deg, #071a0d 0%, #0e3318 55%, #1a5c2a 100%);
            position: relative; overflow: hidden;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 2.5rem 2.25rem;
        }
        .auth-panel::before {
            content: ''; position: absolute; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(76,199,106,.1), transparent 65%);
            top: -140px; right: -200px; border-radius: 50%; pointer-events: none;
        }
        .auth-panel::after {
            content: ''; position: absolute; width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(45,158,75,.09), transparent 65%);
            bottom: -100px; left: -100px; border-radius: 50%; pointer-events: none;
        }
        .panel-z { position: relative; z-index: 2; }

        .panel-logo { display: flex; align-items: center; gap: .6rem; margin-bottom: 3rem; text-decoration: none; }
        .panel-logo .leaf {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            border-radius: 50% 12px 50% 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
            box-shadow: 0 0 16px rgba(76,199,106,.4);
        }
        .panel-logo .wordmark { font-family: 'Poppins', sans-serif; font-weight: 900; font-size: 1.4rem; color: #fff; line-height: 1; }
        .panel-logo .wordmark em { color: var(--accent); font-style: normal; }

        .panel-headline { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.65rem; color: #fff; line-height: 1.2; margin-bottom: .75rem; }
        .panel-sub { color: rgba(255,255,255,.55); font-size: .88rem; line-height: 1.75; margin-bottom: 2.25rem; }

        /* Reset steps on the panel */
        .reset-steps { display: flex; flex-direction: column; gap: 0; }
        .rs-item {
            display: flex; gap: .85rem; align-items: flex-start;
            padding: .75rem 0; border-top: 1px solid rgba(255,255,255,.07); position: relative;
        }
        .rs-item:first-child { border-top: 0; }
        .rs-num {
            width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .8rem;
            background: rgba(255,255,255,.1); color: rgba(255,255,255,.55);
        }
        .rs-num.active { background: var(--accent); color: #071a0d; }
        .rs-num.done { background: rgba(76,199,106,.2); color: var(--accent); }
        .rs-text strong { display: block; color: rgba(255,255,255,.88); font-size: .85rem; font-weight: 600; }
        .rs-text span { color: rgba(255,255,255,.45); font-size: .77rem; line-height: 1.45; margin-top: .1rem; }
        .rs-item.active .rs-text strong { color: var(--accent); }

        .panel-footer-note {
            margin-top: auto; padding-top: 2rem;
            color: rgba(255,255,255,.3); font-size: .75rem; line-height: 1.5;
        }
        .panel-footer-note a { color: rgba(255,255,255,.45); text-decoration: none; }
        .panel-footer-note a:hover { color: var(--accent); }

        /* ── FORM AREA ── */
        .auth-form-area {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 2.5rem 1.5rem; min-width: 0;
        }
        .auth-card {
            width: 100%; max-width: 420px;
            background: #fff; border-radius: 22px;
            box-shadow: 0 10px 50px rgba(26,92,42,.1);
            border: 1.5px solid rgba(26,92,42,.08);
            padding: 2.5rem 2.25rem;
        }

        .step-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            background: var(--light-green); color: var(--primary);
            border-radius: 50px; padding: .28rem .85rem;
            font-size: .71rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: .4px; margin-bottom: .9rem;
        }
        .ac-icon { font-size: 2rem; margin-bottom: .35rem; }
        .ac-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.5rem; color: #1a2e1e; margin-bottom: .35rem; }
        .ac-sub { font-size: .88rem; color: var(--muted); margin-bottom: 1.85rem; line-height: 1.55; }

        .auth-alert {
            display: flex; align-items: flex-start; gap: .55rem;
            padding: .75rem 1rem; border-radius: 10px; font-size: .85rem;
            margin-bottom: 1.4rem; line-height: 1.5;
        }
        .auth-alert.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .auth-alert.danger  { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

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
        .af-input.is-invalid { border-color: #e74c3c; }
        .af-error { font-size: .75rem; color: #e74c3c; margin-top: .3rem; }

        .btn-action {
            width: 100%; height: 50px; border: none; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff; font-family: 'Poppins', sans-serif;
            font-weight: 700; font-size: .97rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: .55rem;
            transition: all .25s; margin-top: 1.4rem;
        }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 8px 26px rgba(26,92,42,.32); }

        .af-divider { display: flex; align-items: center; gap: .75rem; margin: 1.5rem 0; }
        .af-divider::before, .af-divider::after { content: ''; flex: 1; height: 1px; background: rgba(26,92,42,.1); }
        .af-divider span { font-size: .73rem; color: var(--muted); }

        .af-switch { text-align: center; font-size: .87rem; color: var(--muted); }
        .af-switch a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .af-switch a:hover { text-decoration: underline; }

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
            .panel-headline, .panel-sub, .reset-steps, .panel-footer-note { display: none; }
            .panel-logo { margin-bottom: 0; }
            .panel-logo .leaf { width: 34px; height: 34px; font-size: .95rem; }
            .panel-logo .wordmark { font-size: 1.2rem; }
            .auth-form-area { padding: 1.75rem 1rem; justify-content: flex-start; }
            .auth-card { padding: 1.75rem 1.25rem; border-radius: 18px; }
        }
        @media (max-width: 480px) {
            .auth-card { padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body>

    <!-- LEFT PANEL -->
    <div class="auth-panel">
        <div class="panel-z">
            <a href="{{ route('auth.LandingPage') }}" class="panel-logo">
                <div class="leaf">🌿</div>
                <div class="wordmark">Olives <em>BI</em></div>
            </a>

            <div class="panel-headline">Set or reset your<br>password securely.</div>
            <p class="panel-sub">We'll send a one-time code to your registered phone number. Follow the three steps to set a new password.</p>

            <div class="reset-steps">
                <div class="rs-item active">
                    <div class="rs-num active">1</div>
                    <div class="rs-text">
                        <strong>Enter Your Phone Number</strong>
                        <span>We'll send an OTP to your registered number.</span>
                    </div>
                </div>
                <div class="rs-item">
                    <div class="rs-num">2</div>
                    <div class="rs-text">
                        <strong>Verify Your OTP</strong>
                        <span>Enter the code and your new password.</span>
                    </div>
                </div>
                <div class="rs-item">
                    <div class="rs-num">3</div>
                    <div class="rs-text">
                        <strong>Sign In</strong>
                        <span>Use your new password to access your account.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-footer-note">
            <a href="{{ route('auth.LandingPage') }}">← Back to Home</a>
            &nbsp;·&nbsp; © {{ date('Y') }} Olives BI
        </div>
    </div>

    <!-- FORM AREA -->
    <div class="auth-form-area">
        <div class="auth-card">
            <div class="step-badge"><i class="bi bi-phone"></i> Step 1 of 2</div>
            <div class="ac-icon">🔑</div>
            <div class="ac-title">Set or reset your password</div>
            <p class="ac-sub">New account or forgotten password? Enter your registered phone number to request a one-time code and choose your password.</p>

            @if (session()->has('status'))
                <div class="auth-alert success">
                    <i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:.1rem;"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="auth-alert danger">
                    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:.1rem;"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('auth.sendUserOTP') }}" method="POST">
                @csrf

                <div class="af-group">
                    <label class="af-label" for="phone">Phone Number</label>
                    <input class="af-input @error('phone') is-invalid @enderror"
                           id="phone" type="text" name="phone"
                           placeholder="e.g. +233 20 000 0000"
                           value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="af-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-action">
                    <i class="bi bi-send-fill"></i> Send OTP Code
                </button>
            </form>

            <div class="af-divider"><span>Remembered it?</span></div>

            <div class="af-switch">
                <a href="{{ route('auth.index') }}"><i class="bi bi-arrow-left"></i> Back to Sign In</a>
            </div>
        </div>

        <div class="auth-page-footer">
            <a href="{{ route('auth.LandingPage') }}">Home</a>
            <span class="dot">·</span>
            <a href="{{ route('auth.register') }}">Create Account</a>
            <span class="dot">·</span>
            <a href="{{ route('locality.check') }}">Check Locality</a>
        </div>
    </div>

</body>
</html>

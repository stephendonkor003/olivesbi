<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set New Password — Olives BI</title>

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

        .pw-tips { list-style: none; }
        .pw-tips li {
            display: flex; gap: .75rem; align-items: flex-start;
            padding: .6rem 0; border-top: 1px solid rgba(255,255,255,.07);
        }
        .pw-tips li:first-child { border-top: 0; }
        .tip-icon {
            width: 26px; height: 26px; border-radius: 7px; flex-shrink: 0;
            background: rgba(76,199,106,.14);
            display: flex; align-items: center; justify-content: center;
            color: var(--accent); font-size: .8rem;
        }
        .tip-text { color: rgba(255,255,255,.55); font-size: .81rem; line-height: 1.5; }

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

        .pw-wrap { position: relative; }
        .pw-wrap .af-input { padding-right: 3.1rem; }
        .pw-eye {
            position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer;
            padding: 0; font-size: 1rem; line-height: 1; transition: color .2s;
        }
        .pw-eye:hover { color: var(--primary); }

        .pw-strength { display: flex; gap: .25rem; margin-top: .4rem; }
        .pw-bar { flex: 1; height: 4px; border-radius: 2px; background: rgba(26,92,42,.1); transition: background .3s; }
        .pw-bar.weak   { background: #e74c3c; }
        .pw-bar.fair   { background: #f59e0b; }
        .pw-bar.good   { background: var(--primary-light); }
        .pw-bar.strong { background: var(--primary); }
        .pw-hint { font-size: .72rem; color: var(--muted); margin-top: .26rem; }

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

        @media (max-width: 820px) {
            body { flex-direction: column; }
            .auth-panel {
                width: 100%; flex-direction: row; align-items: center;
                gap: 1rem; padding: 1.1rem 1.25rem; justify-content: flex-start;
            }
            .panel-headline, .panel-sub, .pw-tips, .panel-footer-note { display: none; }
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

            <div class="panel-headline">Create a strong<br>new password.</div>
            <p class="panel-sub">Choose a password that is hard to guess but easy for you to remember. Follow the tips below.</p>

            <ul class="pw-tips">
                <li>
                    <div class="tip-icon"><i class="bi bi-rulers"></i></div>
                    <span class="tip-text">At least 8 characters long.</span>
                </li>
                <li>
                    <div class="tip-icon"><i class="bi bi-alphabet-uppercase"></i></div>
                    <span class="tip-text">Mix uppercase and lowercase letters.</span>
                </li>
                <li>
                    <div class="tip-icon"><i class="bi bi-123"></i></div>
                    <span class="tip-text">Include at least one number.</span>
                </li>
                <li>
                    <div class="tip-icon"><i class="bi bi-asterisk"></i></div>
                    <span class="tip-text">Add a special character like @, #, or !.</span>
                </li>
                <li>
                    <div class="tip-icon"><i class="bi bi-x-circle"></i></div>
                    <span class="tip-text">Avoid using your name, phone number, or "password".</span>
                </li>
            </ul>
        </div>

        <div class="panel-footer-note">
            <a href="{{ route('auth.LandingPage') }}">← Back to Home</a>
            &nbsp;·&nbsp; © {{ date('Y') }} Olives BI
        </div>
    </div>

    <!-- FORM AREA -->
    <div class="auth-form-area">
        <div class="auth-card">
            <div class="step-badge"><i class="bi bi-lock-fill"></i> Set New Password</div>
            <div class="ac-icon">🔒</div>
            <div class="ac-title">Create new password</div>
            <p class="ac-sub">Enter your new password below. Make sure it's strong and different from your previous one.</p>

            @if (session()->has('status'))
                <div class="auth-alert success">
                    <i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:.1rem;"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('auth.changePassword') }}" method="POST">
                @csrf

                <div class="af-group">
                    <label class="af-label" for="cp_password">New Password</label>
                    <div class="pw-wrap">
                        <input class="af-input @error('password') is-invalid @enderror"
                               id="cp_password" type="password" name="password"
                               placeholder="Enter your new password"
                               oninput="checkCpPwStrength(this.value)">
                        <button type="button" class="pw-eye" onclick="toggleCpPw('cp_password',this)"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="pw-strength">
                        <div class="pw-bar" id="cp-bar-1"></div>
                        <div class="pw-bar" id="cp-bar-2"></div>
                        <div class="pw-bar" id="cp-bar-3"></div>
                        <div class="pw-bar" id="cp-bar-4"></div>
                    </div>
                    <div class="pw-hint" id="cp-hint">Min 8 characters.</div>
                    @error('password')
                        <div class="af-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="af-group">
                    <label class="af-label" for="cp_confirm">Confirm New Password</label>
                    <div class="pw-wrap">
                        <input class="af-input @error('password_confirmation') is-invalid @enderror"
                               id="cp_confirm" type="password" name="password_confirmation"
                               placeholder="Re-enter new password">
                        <button type="button" class="pw-eye" onclick="toggleCpPw('cp_confirm',this)"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('password_confirmation')
                        <div class="af-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-action">
                    <i class="bi bi-check-circle-fill"></i> Save New Password
                </button>
            </form>

            <div class="af-divider"><span>or</span></div>

            <div class="af-switch">
                <a href="{{ route('auth.index') }}"><i class="bi bi-arrow-left"></i> Back to Sign In</a>
                &nbsp;&nbsp;·&nbsp;&nbsp;
                <a href="{{ route('auth.register') }}">Create Account</a>
            </div>
        </div>

        <div class="auth-page-footer">
            <a href="{{ route('auth.LandingPage') }}">Home</a>
            <span class="dot">·</span>
            <a href="{{ route('locality.check') }}">Check Locality</a>
        </div>
    </div>

<script>
    function toggleCpPw(id, btn) {
        const inp = document.getElementById(id);
        if (!inp) return;
        const isText = inp.type === 'text';
        inp.type = isText ? 'password' : 'text';
        btn.innerHTML = isText ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    }

    function checkCpPwStrength(val) {
        let score = 0;
        if (val.length >= 8)           score++;
        if (/[A-Z]/.test(val))         score++;
        if (/[0-9]/.test(val))         score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = ['weak','fair','good','strong'];
        const labels = ['Too short','Fair — add numbers or symbols','Good — add a symbol','Strong password'];
        [1,2,3,4].forEach(i => {
            const b = document.getElementById('cp-bar-' + i);
            if (b) b.className = 'pw-bar' + (i <= score ? ' ' + levels[score - 1] : '');
        });
        const hint = document.getElementById('cp-hint');
        if (hint) hint.textContent = val.length === 0 ? 'Min 8 characters.' : (labels[score - 1] || 'Too short');
    }
</script>
</body>
</html>

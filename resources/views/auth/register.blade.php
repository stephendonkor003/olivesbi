<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.public-seo', [
        'seoTitle' => 'Create an Olives BI Account - Environmental Intelligence Signup',
        'seoDescription' => 'Register for Olives BI to access environmental intelligence, locality checks, revenue data tools, and newsletter updates for land, waste, air quality, and public service insights.',
        'seoKeywords' => 'Olives BI registration, create Olives BI account, environmental intelligence signup, land data platform account, waste management updates, air quality updates, local revenue platform',
        'seoUrl' => route('auth.register'),
        'seoRobots' => 'index, follow, max-image-preview:large',
        'seoSchema' => [
            'potentialAction' => [
                '@type' => 'RegisterAction',
                'target' => route('auth.register'),
            ],
        ],
        'seoMainEntity' => [
            '@type' => 'SoftwareApplication',
            'name' => 'Olives BI',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
            ],
        ],
    ])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">

    <style>
        :root {
            --primary:       #1a5c2a;
            --primary-mid:   #236b33;
            --primary-light: #2d9e4b;
            --accent:        #4cc76a;
            --accent-soft:   #a8e6bc;
            --light-green:   #e8f5e9;
            --dark-bg:       #071a0d;
            --card-bg:       #ffffff;
            --text-muted:    #6b7280;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: #1a2e1e;
            background: #f0f7f1;
            overflow-x: hidden;
        }

        #mainNav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: .65rem 0;
            background: rgba(7, 26, 13, .96);
            backdrop-filter: blur(10px);
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

        .page-hero {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(110deg, rgba(7,26,13,.94), rgba(26,92,42,.82)),
                url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1800&q=70') center/cover;
            padding: 8.4rem 0 5.2rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .25rem .75rem;
            border-radius: 50px;
            background: rgba(76,199,106,.16);
            border: 1px solid rgba(76,199,106,.35);
            color: #cdf5d8;
            font-size: .74rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        h1 {
            margin: .8rem 0 .75rem;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: clamp(2rem, 4vw, 3.8rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: 0;
        }

        .hero-copy {
            max-width: 690px;
            color: rgba(255,255,255,.78);
            font-size: 1rem;
            line-height: 1.7;
        }

        .shell {
            margin-top: -3.6rem;
            position: relative;
            z-index: 3;
            padding-bottom: 3.5rem;
        }

        .form-surface {
            background: #fff;
            border: 1.5px solid rgba(26,92,42,.1);
            border-radius: 16px;
            box-shadow: 0 16px 45px rgba(26,92,42,.13);
            overflow: hidden;
        }

        .side-panel {
            height: 100%;
            background:
                linear-gradient(180deg, rgba(7,26,13,.94), rgba(13,44,22,.98)),
                url('https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?auto=format&fit=crop&w=900&q=65') center/cover;
            color: rgba(255,255,255,.78);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .side-panel h2 {
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            margin-bottom: .75rem;
        }

        .panel-stat {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
            margin-top: 1.2rem;
        }

        .panel-stat div {
            border: 1px solid rgba(255,255,255,.13);
            border-radius: 10px;
            padding: .75rem;
            background: rgba(255,255,255,.07);
        }

        .panel-stat strong {
            display: block;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 1.15rem;
            line-height: 1;
        }

        .panel-stat span {
            display: block;
            margin-top: .25rem;
            font-size: .72rem;
            color: rgba(255,255,255,.62);
        }

        .step-list {
            list-style: none;
            margin: 1.2rem 0 0;
            padding: 0;
        }

        .step-list li {
            display: flex;
            gap: .75rem;
            padding: .8rem 0;
            border-top: 1px solid rgba(255,255,255,.1);
            font-size: .9rem;
        }

        .step-list i { color: var(--accent); font-size: 1.05rem; }

        .form-wrap { padding: 2rem; }

        .form-heading {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
            padding-bottom: 1.1rem;
            margin-bottom: 1.2rem;
            border-bottom: 1px solid rgba(26,92,42,.1);
        }

        .form-heading h2 {
            margin: 0;
            color: #1a2e1e;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.35rem;
        }

        .form-heading p {
            margin: .25rem 0 0;
            color: var(--text-muted);
            font-size: .88rem;
        }

        .form-step {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            color: var(--primary);
            background: var(--light-green);
            border-radius: 50px;
            padding: .3rem .75rem;
            font-size: .75rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .mode-toggle {
            display: inline-flex;
            gap: .3rem;
            padding: .25rem;
            border-radius: 10px;
            background: var(--light-green);
            border: 1px solid rgba(26,92,42,.12);
        }

        .mode-btn {
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: var(--primary);
            font-weight: 800;
            font-size: .8rem;
            padding: .45rem .75rem;
        }

        .mode-btn.active {
            background: #fff;
            box-shadow: 0 2px 8px rgba(26,92,42,.12);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: var(--primary);
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            margin: 1.35rem 0 .8rem;
        }

        .section-title:first-child { margin-top: 0; }

        .form-label {
            font-size: .78rem;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
        }

        .form-control,
        .form-select {
            border: 1.5px solid rgba(26,92,42,.18);
            border-radius: 8px;
            min-height: 44px;
            font-size: .92rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(45,158,75,.13);
        }

        .iti { width: 100%; }
        .iti .form-control { padding-left: 3.25rem; }

        .choice-card {
            border: 1.5px solid rgba(26,92,42,.18);
            border-radius: 10px;
            padding: .85rem;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            height: 100%;
        }

        .choice-card:has(input:checked) {
            border-color: var(--primary-light);
            background: var(--light-green);
        }

        .choice-card strong {
            display: block;
            color: #17251b;
            font-size: .92rem;
        }

        .choice-card span {
            display: block;
            color: var(--text-muted);
            font-size: .78rem;
            margin-top: .15rem;
        }

        .newsletter-box {
            border: 1.5px solid rgba(45,158,75,.22);
            border-radius: 10px;
            background: #f3fbf5;
            padding: 1rem;
        }

        .topic-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: .5rem;
            margin-top: .75rem;
        }

        .topic-chip {
            display: flex;
            align-items: center;
            gap: .45rem;
            min-height: 40px;
            padding: .45rem .6rem;
            border-radius: 7px;
            background: #fff;
            border: 1px solid rgba(26,92,42,.12);
            font-size: .84rem;
            font-weight: 600;
        }

        .btn-start {
            border: 0;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            color: #fff;
            min-height: 48px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
        }

        .btn-start:hover {
            color: #fff;
            box-shadow: 0 8px 22px rgba(26,92,42,.28);
        }

        .fine-print {
            color: var(--text-muted);
            font-size: .78rem;
            line-height: 1.5;
        }

        footer {
            background: var(--dark-bg);
            padding: 3rem 0 1.5rem;
            color: rgba(255,255,255,.6);
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
            font-size: .85rem;
            color: #fff;
            margin-bottom: .8rem;
        }
        .footer-link {
            display: block;
            color: rgba(255,255,255,.45);
            text-decoration: none;
            font-size: .82rem;
            margin-bottom: .45rem;
            transition: color .2s;
        }
        .footer-link:hover { color: var(--accent); }
        .footer-divider { border-color: rgba(255,255,255,.08); margin: 2rem 0 1rem; }
        .footer-bottom { font-size: .8rem; color: rgba(255,255,255,.3); }

        .is-hidden { display: none; }
        .advanced-fields { display: none; }
        body.show-advanced .advanced-fields { display: block; }

        @media (max-width: 991px) {
            .page-hero { padding: 7.4rem 0 4.2rem; }
            .side-panel { border-radius: 0; }
            .form-heading { flex-direction: column; }
        }

        @media (max-width: 575px) {
            .topic-grid { grid-template-columns: 1fr; }
            .form-wrap { padding: 1rem; }
        }
    </style>
</head>
<body>
<nav id="mainNav" class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('auth.LandingPage') }}">
            <div class="brand-leaf"><i class="bi bi-leaf-fill"></i></div>
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
                <a href="{{ route('auth.index') }}" class="btn btn-nav-login">Sign In</a>
                <a href="{{ route('auth.register') }}" class="btn btn-nav-cta">Get Started</a>
            </div>
        </div>
    </div>
</nav>

<header class="page-hero">
    <div class="container">
        <span class="eyebrow"><i class="bi bi-person-check-fill"></i> Get Started</span>
        <h1>Create your Olives BI account</h1>
        <p class="hero-copy">
            Register as a general user, get access to the platform, and subscribe to revenue, environmental, waste management, and locality intelligence updates in one flow.
        </p>
    </div>
</header>

<main class="container shell">
    <form action="{{ route('citizens.frontstore') }}" method="POST" class="form-surface" id="register-form">
        @csrf

        <div class="row g-0">
            <div class="col-lg-4">
                <aside class="side-panel">
                    <div>
                        <h2>One account, one profile</h2>
                        <p class="mb-0">Your account lets you sign in with your phone number and password after registration.</p>
                        <ul class="step-list">
                            <li><i class="bi bi-person-vcard"></i><span>Choose individual or organization registration.</span></li>
                            <li><i class="bi bi-shield-lock"></i><span>Set your secure login password.</span></li>
                            <li><i class="bi bi-envelope-paper"></i><span>Opt into updates using the same contact details.</span></li>
                        </ul>
                    </div>
                    <div class="panel-stat">
                        <div><strong>Live</strong><span>Environmental updates</span></div>
                        <div><strong>BI</strong><span>Revenue intelligence</span></div>
                    </div>
                </aside>
            </div>

            <div class="col-lg-8">
                <div class="form-wrap">
                    <div class="form-heading">
                        <div>
                            <h2>Account registration</h2>
                            <p>Start with the simple required fields. Open advanced details only when you need them.</p>
                        </div>
                        <div class="d-flex flex-column align-items-lg-end gap-2">
                            <span class="form-step"><i class="bi bi-lock-fill"></i> Secure signup</span>
                            <div class="mode-toggle" aria-label="Registration mode">
                                <button type="button" class="mode-btn active" data-mode="simple">Simple</button>
                                <button type="button" class="mode-btn" data-mode="advanced">Advanced</button>
                            </div>
                        </div>
                    </div>

                    @if (session()->has('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Please check the highlighted fields and try again.
                        </div>
                    @endif

                    <div class="section-title"><i class="bi bi-ui-checks-grid"></i> Account Type</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="choice-card">
                                <input type="radio" name="registration_type" value="individual" class="form-check-input me-2" @checked(old('registration_type', 'individual') === 'individual')>
                                <strong>Individual user</strong>
                                <span>For residents, rate payers, property owners, and general platform users.</span>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="choice-card">
                                <input type="radio" name="registration_type" value="organization" class="form-check-input me-2" @checked(old('registration_type') === 'organization')>
                                <strong>Organization</strong>
                                <span>For businesses, institutions, NGOs, and operational teams.</span>
                            </label>
                        </div>
                    </div>
                    @error('registration_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                    <section id="individual-fields">
                        <div class="section-title"><i class="bi bi-person-lines-fill"></i> Personal Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="first_name">First Name</label>
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror">
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="last_name">Last Name</label>
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="telephone_number">Phone Number</label>
                                <input id="telephone_number" type="tel" name="telephone_number" value="{{ old('telephone_number') }}" class="form-control @error('telephone_number') is-invalid @enderror" placeholder="Phone number">
                                @error('telephone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="password">Password</label>
                                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="password_confirmation">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="advanced-fields">
                            <div class="section-title"><i class="bi bi-sliders"></i> Advanced Personal Details</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="gender">Gender</label>
                                    <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">Select</option>
                                        <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                                        <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                                        <option value="Other" @selected(old('gender') === 'Other')>Other</option>
                                    </select>
                                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="date_of_birth">Date of Birth</label>
                                    <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-control @error('date_of_birth') is-invalid @enderror">
                                    @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="country_of_citizenship">Citizenship</label>
                                    <input id="country_of_citizenship" type="text" name="country_of_citizenship" value="{{ old('country_of_citizenship') }}" class="form-control @error('country_of_citizenship') is-invalid @enderror">
                                    @error('country_of_citizenship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="id_type">ID Type</label>
                                    <input id="id_type" type="text" name="id_type" value="{{ old('id_type') }}" class="form-control @error('id_type') is-invalid @enderror" placeholder="Ghana Card, passport, voter ID">
                                    @error('id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="id_number">ID Number</label>
                                    <input id="id_number" type="text" name="id_number" value="{{ old('id_number') }}" class="form-control @error('id_number') is-invalid @enderror">
                                    @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="organization-fields" class="is-hidden">
                        <div class="section-title"><i class="bi bi-buildings"></i> Organization Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="business_name">Organization Name</label>
                                <input id="business_name" type="text" name="business_name" value="{{ old('business_name') }}" class="form-control @error('business_name') is-invalid @enderror">
                                @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 advanced-fields">
                                <label class="form-label" for="tin_number">TIN</label>
                                <input id="tin_number" type="text" name="tin_number" value="{{ old('tin_number') }}" class="form-control @error('tin_number') is-invalid @enderror">
                                @error('tin_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="org_first_name">Contact First Name</label>
                                <input id="org_first_name" type="text" name="org_first_name" value="{{ old('org_first_name') }}" class="form-control @error('org_first_name') is-invalid @enderror">
                                @error('org_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="org_last_name">Contact Last Name</label>
                                <input id="org_last_name" type="text" name="org_last_name" value="{{ old('org_last_name') }}" class="form-control @error('org_last_name') is-invalid @enderror">
                                @error('org_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="org_telephone_number">Contact Phone</label>
                                <input id="org_telephone_number" type="tel" name="org_telephone_number" value="{{ old('org_telephone_number') }}" class="form-control @error('org_telephone_number') is-invalid @enderror" placeholder="Phone number">
                                @error('org_telephone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 advanced-fields">
                                <label class="form-label" for="date_of_commencement">Start Date</label>
                                <input id="date_of_commencement" type="date" name="date_of_commencement" value="{{ old('date_of_commencement') }}" class="form-control @error('date_of_commencement') is-invalid @enderror">
                                @error('date_of_commencement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="org_password">Password</label>
                                <input id="org_password" type="password" name="org_password" class="form-control @error('org_password') is-invalid @enderror">
                                @error('org_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="org_password_confirmation">Confirm Password</label>
                                <input id="org_password_confirmation" type="password" name="org_password_confirmation" class="form-control">
                            </div>
                        </div>
                    </section>

                    <div class="section-title"><i class="bi bi-envelope-at"></i> Updates & Newsletter</div>
                    <div class="newsletter-box">
                        <div class="row g-3 align-items-start">
                            <div class="col-md-7">
                                <label class="form-label" for="email">Email Address</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-5 pt-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="newsletter_opt_in" name="newsletter_opt_in" @checked(old('newsletter_opt_in'))>
                                    <label class="form-check-label fw-bold" for="newsletter_opt_in">Subscribe me to updates</label>
                                </div>
                                <div class="fine-print mt-1">Email is required when newsletter subscription is selected.</div>
                            </div>
                        </div>

                        <div class="topic-grid">
                            <label class="topic-chip"><input type="checkbox" name="newsletter_topics[]" value="environment" @checked(in_array('environment', old('newsletter_topics', [])))> Environmental alerts</label>
                            <label class="topic-chip"><input type="checkbox" name="newsletter_topics[]" value="waste" @checked(in_array('waste', old('newsletter_topics', [])))> Waste management</label>
                            <label class="topic-chip"><input type="checkbox" name="newsletter_topics[]" value="revenue" @checked(in_array('revenue', old('newsletter_topics', [])))> Revenue updates</label>
                            <label class="topic-chip"><input type="checkbox" name="newsletter_topics[]" value="locality" @checked(in_array('locality', old('newsletter_topics', [])))> Locality intelligence</label>
                        </div>
                    </div>

                    <div class="row g-3 mt-3 align-items-center">
                        <div class="col-md-7">
                            <p class="fine-print mb-0">By registering, you create a customer account on Olives BI. You can sign in immediately after successful registration.</p>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-start">
                                <i class="bi bi-arrow-right-circle"></i>
                                Create Account
                            </button>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <span class="fine-print">Already have an account?</span>
                        <a href="{{ route('auth.index') }}" style="color:var(--primary); font-weight:800; text-decoration:none;">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<footer>
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="footer-logo"><i class="bi bi-leaf-fill me-1"></i>Olives <span>BI</span></div>
                <div class="footer-tagline">Environmental Protection Intelligence</div>
                <p style="color:rgba(255,255,255,.4); font-size:.82rem; margin-top:.75rem; line-height:1.65;">
                    Designed with the environment at its core, guiding land-use and revenue decisions with better data.
                </p>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="footer-heading">Platform</div>
                <a href="{{ route('auth.LandingPage') }}#features" class="footer-link">Features</a>
                <a href="{{ route('auth.LandingPage') }}#coverage" class="footer-link">Coverage</a>
                <a href="{{ route('auth.LandingPage') }}#how-it-works" class="footer-link">How It Works</a>
                <a href="{{ route('auth.LandingPage') }}#mission" class="footer-link">Mission</a>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="footer-heading">Domains</div>
                <a href="{{ route('auth.LandingPage') }}#features" class="footer-link">Forest Reserves</a>
                <a href="{{ route('auth.LandingPage') }}#features" class="footer-link">Ramsar Wetlands</a>
                <a href="{{ route('locality.check') }}" class="footer-link">Air Quality</a>
                <a href="{{ route('locality.check') }}" class="footer-link">Waste Management</a>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="footer-heading">Get Involved</div>
                <a href="{{ route('auth.register') }}" class="footer-link">Create Account</a>
                <a href="{{ route('auth.index') }}" class="footer-link">Sign In</a>
                <a href="{{ route('auth.LandingPage') }}#contact" class="footer-link">Contact Us</a>
            </div>
        </div>

        <hr class="footer-divider">
        <div class="d-flex flex-wrap justify-content-between align-items-center footer-bottom">
            <span>© {{ date('Y') }} Olives BI. Built with purpose for the planet.</span>
            <span style="color:var(--accent); font-size:.78rem;">Protecting Earth Through Data</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
<script>
    const radios = document.querySelectorAll('input[name="registration_type"]');
    const individualFields = document.getElementById('individual-fields');
    const organizationFields = document.getElementById('organization-fields');
    const form = document.getElementById('register-form');
    const modeButtons = document.querySelectorAll('[data-mode]');
    const phoneInputs = [
        document.getElementById('telephone_number'),
        document.getElementById('org_telephone_number')
    ].filter(Boolean);
    const phonePickers = [];

    const shouldShowAdvanced = @json(
        old('gender') ||
        old('date_of_birth') ||
        old('country_of_citizenship') ||
        old('id_type') ||
        old('id_number') ||
        old('tin_number') ||
        old('date_of_commencement')
    );

    function syncAccountType() {
        const selected = document.querySelector('input[name="registration_type"]:checked')?.value || 'individual';
        individualFields.classList.toggle('is-hidden', selected !== 'individual');
        organizationFields.classList.toggle('is-hidden', selected !== 'organization');
    }

    function setMode(mode) {
        document.body.classList.toggle('show-advanced', mode === 'advanced');
        modeButtons.forEach(button => button.classList.toggle('active', button.dataset.mode === mode));
    }

    phoneInputs.forEach(input => {
        if (!window.intlTelInput) return;

        const picker = window.intlTelInput(input, {
            initialCountry: 'auto',
            nationalMode: false,
            separateDialCode: true,
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js',
            geoIpLookup: callback => {
                fetch('https://ipapi.co/json/')
                    .then(response => response.json())
                    .then(data => callback((data && data.country_code) ? data.country_code : 'GH'))
                    .catch(() => callback('GH'));
            },
        });

        phonePickers.push({ input, picker });
    });

    form.addEventListener('submit', event => {
        const selectedType = document.querySelector('input[name="registration_type"]:checked')?.value || 'individual';
        const activePhoneName = selectedType === 'organization' ? 'org_telephone_number' : 'telephone_number';

        phonePickers.forEach(({ input, picker }) => {
            if (input.name !== activePhoneName || !input.value.trim()) return;

            if (picker.isValidNumber()) {
                input.value = picker.getNumber();
                input.classList.remove('is-invalid');
                return;
            }

            input.classList.add('is-invalid');
            event.preventDefault();
        });
    });

    radios.forEach(radio => radio.addEventListener('change', syncAccountType));
    modeButtons.forEach(button => button.addEventListener('click', () => setMode(button.dataset.mode)));
    syncAccountType();
    setMode(shouldShowAdvanced ? 'advanced' : 'simple');
</script>
</body>
</html>

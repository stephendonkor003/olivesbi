@php
    use App\Support\OlivesAccountTier;

    $authUser   = auth()->user();
    $tierSlug   = \App\Support\OlivesAccountTier::fromUser($authUser);
    $userName   = optional($authUser)->name ?? 'User';
    $userHandle = optional($authUser)->email ?: (optional($authUser)->phone ?? 'Olives BI user');
    $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName() ?? '';

    $tierConfig = [
        'subscriber' => [
            'accent' => '#4cc76a',
            'name'   => 'Subscriber',
            'nav'    => [
                ['icon' => 'bi-speedometer2',   'label' => 'Dashboard',        'route' => 'dashboard.entry'],
                ['icon' => 'bi-newspaper',       'label' => 'Environment News', 'route' => 'locality.check'],
                ['icon' => 'bi-bell',            'label' => 'My Alerts',        'route' => 'dashboard.myprofile'],
                ['icon' => 'bi-geo-alt',         'label' => 'Check Locality',   'route' => 'locality.check'],
                ['icon' => 'bi-envelope-paper',  'label' => 'Newsletter',       'route' => 'dashboard.myprofile'],
                ['icon' => 'bi-person-circle',   'label' => 'My Profile',       'route' => 'dashboard.myprofile'],
                ['icon' => 'bi-box-arrow-left',  'label' => 'Sign Out',         'route' => 'auth.logout'],
            ],
        ],
        'account' => [
            'accent' => '#2d9e4b',
            'name'   => 'Olives Account',
            'nav'    => [
                ['icon' => 'bi-speedometer2',   'label' => 'Dashboard',        'route' => 'dashboard.entry'],
                ['icon' => 'bi-buildings',       'label' => 'My Properties',   'route' => 'dashboard.olives-account.properties'],
                ['icon' => 'bi-briefcase',       'label' => 'My Business',     'route' => 'dashboard.olives-account.businesses'],
                ['icon' => 'bi-receipt',         'label' => 'My Bills',        'route' => 'dashboard.olives-account.bills'],
                ['icon' => 'bi-clock-history',   'label' => 'Payment History', 'route' => 'dashboard.olives-account.payments'],
                [
                    'icon' => 'bi-file-earmark-bar-graph',
                    'label' => 'Reports',
                    'route' => 'dashboard.olives-account.reports',
                    'children' => [
                        ['icon' => 'bi-grid-1x2', 'label' => 'Overview', 'route' => 'dashboard.olives-account.reports'],
                        ['icon' => 'bi-receipt-cutoff', 'label' => 'Bill Report', 'route' => 'dashboard.olives-account.reports.bills'],
                        ['icon' => 'bi-credit-card-2-front', 'label' => 'Payment Report', 'route' => 'dashboard.olives-account.reports.payments'],
                        ['icon' => 'bi-recycle', 'label' => 'Waste Report', 'route' => 'dashboard.olives-account.reports.waste'],
                        ['icon' => 'bi-shield-check', 'label' => 'Audit Trail', 'route' => 'dashboard.olives-account.reports.audit'],
                    ],
                ],
                ['icon' => 'bi-geo-alt',         'label' => 'Check Locality',  'route' => 'dashboard.olives-account.locality'],
                ['icon' => 'bi-person-circle',   'label' => 'My Profile',      'route' => 'dashboard.olives-account.profile'],
                ['icon' => 'bi-box-arrow-left',  'label' => 'Sign Out',        'route' => 'auth.logout'],
            ],
        ],
        'pro' => [
            'accent' => '#d4a017',
            'name'   => 'Olives Pro',
            'nav'    => [
                ['icon' => 'bi-speedometer2',   'label' => 'Dashboard',       'route' => 'dashboard.entry'],
                ['icon' => 'bi-buildings',       'label' => 'Properties',      'route' => 'properties.index'],
                ['icon' => 'bi-briefcase',       'label' => 'Businesses',      'route' => 'businesses.index'],
                ['icon' => 'bi-receipt',         'label' => 'Bills & Arrears', 'route' => 'bills.index'],
                ['icon' => 'bi-cash-coin',       'label' => 'Payments',        'route' => 'payments.index'],
                ['icon' => 'bi-bank',            'label' => 'Laws & Fees',     'route' => 'revenue-streams.index'],
                ['icon' => 'bi-people',          'label' => 'Citizens',        'route' => 'citizens.index'],
                ['icon' => 'bi-person-gear',     'label' => 'Agents & Users',  'route' => 'users.index'],
                ['icon' => 'bi-geo-alt',         'label' => 'Check Locality',  'route' => 'locality.check'],
                ['icon' => 'bi-person-circle',   'label' => 'My Profile',      'route' => 'dashboard.myprofile'],
                ['icon' => 'bi-box-arrow-left',  'label' => 'Sign Out',        'route' => 'auth.logout'],
            ],
        ],
        'sky' => [
            'accent' => '#1565c0',
            'name'   => 'Olives Sky',
            'nav'    => [
                ['icon' => 'bi-speedometer2',               'label' => 'Dashboard',          'route' => 'dashboard.entry'],
                ['icon' => 'bi-globe2',                     'label' => 'National Overview',  'route' => 'dashboard.overview'],
                ['icon' => 'bi-buildings',                  'label' => 'Assemblies',         'route' => 'mmdas.index'],
                ['icon' => 'bi-bar-chart-line',             'label' => 'National Revenue',   'route' => 'dashboard.operational'],
                ['icon' => 'bi-person-gear',                'label' => 'User Management',    'route' => 'users.index'],
                ['icon' => 'bi-geo-alt',                    'label' => 'Check Locality',     'route' => 'locality.check'],
                ['icon' => 'bi-person-circle',              'label' => 'My Profile',         'route' => 'dashboard.myprofile'],
                ['icon' => 'bi-box-arrow-left',             'label' => 'Sign Out',           'route' => 'auth.logout'],
            ],
        ],
        'cloud' => [
            'accent' => '#7c3aed',
            'name'   => 'Olives Cloud',
            'nav'    => [
                ['icon' => 'bi-speedometer2',   'label' => 'Dashboard',          'route' => 'dashboard.entry'],
                ['icon' => 'bi-cloud-check',    'label' => 'Regional Bloc View', 'route' => 'dashboard.overview'],
                ['icon' => 'bi-flag',           'label' => 'Member Countries',   'route' => 'dashboard.overview'],
                ['icon' => 'bi-person-gear',    'label' => 'User Management',    'route' => 'users.index'],
                ['icon' => 'bi-geo-alt',        'label' => 'Check Locality',     'route' => 'locality.check'],
                ['icon' => 'bi-person-circle',  'label' => 'My Profile',         'route' => 'dashboard.myprofile'],
                ['icon' => 'bi-box-arrow-left', 'label' => 'Sign Out',           'route' => 'auth.logout'],
            ],
        ],
        'shell' => [
            'accent' => '#0f766e',
            'name'   => 'Olives Shell',
            'nav'    => [
                ['icon' => 'bi-speedometer2',       'label' => 'Dashboard',           'route' => 'dashboard.entry'],
                ['icon' => 'bi-globe-europe-africa', 'label' => 'Continental Command', 'route' => 'dashboard.overview'],
                ['icon' => 'bi-person-gear',        'label' => 'User Management',     'route' => 'users.index'],
                ['icon' => 'bi-geo-alt',            'label' => 'Check Locality',      'route' => 'locality.check'],
                ['icon' => 'bi-person-circle',      'label' => 'My Profile',          'route' => 'dashboard.myprofile'],
                ['icon' => 'bi-box-arrow-left',     'label' => 'Sign Out',            'route' => 'auth.logout'],
            ],
        ],
    ];

    $tc         = $tierConfig[$tierSlug] ?? $tierConfig['account'];
    $accent     = $tc['accent'];
    $tierName   = $tc['name'];
    $sidebarNav = $tc['nav'];
    $profileRoute = $tierSlug === 'account' ? route('dashboard.olives-account.profile') : route('dashboard.myprofile');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page-title', $tierName) — Olives BI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@700;800;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @yield('page-head-extra')
    <style>
        :root {
            --primary:   #1a5c2a;
            --pl:        #2d9e4b;
            --accent:    {{ $accent }};
            --dark:      #071a0d;
            --soft:      #eef8f1;
            --muted:     #6b7280;
            --line:      rgba(26,92,42,.12);
            --sidebar-w: 272px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-size: 16px; }
        body { font-family: 'Inter', sans-serif; background: #f1f7f3; color: #17251b; min-height: 100vh; display: flex; }

        /* ── SIDEBAR ── */
        .tier-sidebar {
            width: var(--sidebar-w); flex-shrink: 0;
            background: var(--dark);
            display: flex; flex-direction: column;
            position: sticky; top: 0; height: 100vh; overflow-y: auto;
            padding: 1.4rem 1.1rem;
            border-right: 1px solid rgba(255,255,255,.05);
            scrollbar-width: none;
        }
        .tier-sidebar::-webkit-scrollbar { display: none; }

        .sb-brand { display: flex; align-items: center; gap: .5rem; text-decoration: none; margin-bottom: 1.5rem; }
        .sb-brand-leaf {
            width: 36px; height: 36px; flex-shrink: 0;
            background: linear-gradient(135deg, var(--accent), #4cc76a);
            border-radius: 50% 10px 50% 10px;
            display: grid; place-items: center;
            font-size: 1rem; box-shadow: 0 0 14px rgba(76,199,106,.35);
        }
        .sb-brand-word { font-family: 'Poppins', sans-serif; font-weight: 900; font-size: 1.2rem; color: #fff; line-height: 1; }
        .sb-brand-word em { color: var(--accent); font-style: normal; }

        .sb-tier-pill {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);
            border-radius: 999px; padding: .28rem .7rem; margin-bottom: 1.4rem;
            font-size: .68rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: .4px; color: var(--accent);
        }

        .sb-section-label {
            font-size: .62rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: .6px; color: rgba(255,255,255,.28);
            padding: 0 .4rem; margin-bottom: .4rem; margin-top: .9rem;
        }
        .sb-section-label:first-of-type { margin-top: 0; }

        .sb-nav { display: flex; flex-direction: column; gap: .2rem; }
        .sb-link {
            display: flex; align-items: center; gap: .65rem;
            padding: .6rem .75rem; border-radius: 9px;
            color: rgba(255,255,255,.65); text-decoration: none;
            font-size: .87rem; font-weight: 600; transition: all .18s;
            position: relative;
        }
        .sb-link i { font-size: 1rem; flex-shrink: 0; }
        .sb-link:hover { background: rgba(255,255,255,.08); color: #fff; }
        .sb-link.active { background: rgba(255,255,255,.1); color: #fff; }
        .sb-link.active::before {
            content: ''; display: block;
            width: 3px; height: 100%;
            background: var(--accent);
            position: absolute; left: 0; top: 0; border-radius: 0 3px 3px 0;
        }
        .sb-menu { display: block; }
        .sb-menu > summary { list-style: none; }
        .sb-menu > summary::-webkit-details-marker { display: none; }
        .sb-menu-summary {
            display: flex; align-items: center; justify-content: space-between; gap: .65rem;
            padding: .6rem .75rem; border-radius: 9px;
            color: rgba(255,255,255,.65); text-decoration: none;
            font-size: .87rem; font-weight: 600; transition: all .18s;
            cursor: pointer; position: relative;
        }
        .sb-menu-summary-main { display: inline-flex; align-items: center; gap: .65rem; min-width: 0; }
        .sb-menu-summary-main i { font-size: 1rem; flex-shrink: 0; }
        .sb-menu-summary:hover { background: rgba(255,255,255,.08); color: #fff; }
        .sb-menu-summary.active { background: rgba(255,255,255,.1); color: #fff; }
        .sb-menu-summary.active::before {
            content: ''; display: block;
            width: 3px; height: 100%;
            background: var(--accent);
            position: absolute; left: 0; top: 0; border-radius: 0 3px 3px 0;
        }
        .sb-menu-chevron { font-size: .8rem; transition: transform .18s; opacity: .7; }
        .sb-menu[open] .sb-menu-chevron { transform: rotate(180deg); }
        .sb-child-nav { display: flex; flex-direction: column; gap: .14rem; margin: .18rem 0 .35rem 2.05rem; }
        .sb-child-link {
            display: flex; align-items: center; gap: .45rem;
            color: rgba(255,255,255,.48); text-decoration: none;
            font-size: .76rem; font-weight: 700;
            padding: .34rem .55rem; border-radius: 8px;
            transition: all .18s;
        }
        .sb-child-link i { font-size: .82rem; }
        .sb-child-link:hover, .sb-child-link.active { background: rgba(255,255,255,.07); color: #fff; }

        .sb-sep { height: 1px; background: rgba(255,255,255,.07); margin: .75rem 0; }

        .sb-foot {
            margin-top: auto; padding-top: .85rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .sb-user { display: flex; align-items: center; gap: .6rem; }
        .sb-avatar {
            width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, var(--accent), var(--pl));
            display: grid; place-items: center; font-size: .85rem; color: var(--dark); font-weight: 800;
        }
        .sb-user-name { font-size: .82rem; font-weight: 700; color: rgba(255,255,255,.88); line-height: 1.2; }
        .sb-user-handle { font-size: .72rem; color: rgba(255,255,255,.38); }

        /* ── MAIN ── */
        .tier-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

        .tier-topbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(241,247,243,.92); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--line);
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; padding: .75rem 1.5rem;
        }
        .topbar-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.2rem; color: var(--primary); }
        .topbar-right { display: flex; align-items: center; gap: .65rem; }
        .topbar-chip {
            display: inline-flex; align-items: center; gap: .4rem;
            background: #fff; border: 1px solid var(--line);
            border-radius: 999px; padding: .3rem .75rem;
            font-size: .8rem; font-weight: 700; color: var(--primary);
        }
        .topbar-icon-btn {
            width: 36px; height: 36px; border-radius: 9px;
            background: #fff; border: 1px solid var(--line);
            display: grid; place-items: center;
            color: var(--muted); font-size: 1rem;
            cursor: pointer; text-decoration: none;
            transition: color .18s, background .18s, border-color .18s;
        }
        .topbar-icon-btn:hover { color: var(--primary); background: var(--soft); border-color: rgba(26,92,42,.22); }

        .tier-content { padding: 1.5rem; flex: 1; }

        /* ── HERO BANNER ── */
        .tier-hero {
            border-radius: 18px; padding: 1.5rem 1.75rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: #fff; margin-bottom: 1.25rem;
            display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem;
            position: relative; overflow: hidden;
        }
        .tier-hero::before {
            content: ''; position: absolute; width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(255,255,255,.08), transparent 65%);
            top: -100px; right: -80px; border-radius: 50%; pointer-events: none;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
            border-radius: 999px; padding: .28rem .75rem;
            font-size: .72rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: .4px; margin-bottom: .65rem;
        }
        .hero-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: clamp(1.2rem, 2.5vw, 1.65rem); line-height: 1.2; margin-bottom: .35rem; }
        .hero-sub { font-size: .88rem; color: rgba(255,255,255,.78); line-height: 1.65; max-width: 580px; }

        /* ── PANELS ── */
        .panel {
            background: #fff; border: 1px solid var(--line); border-radius: 14px;
            padding: 1.25rem; box-shadow: 0 1px 8px rgba(26,92,42,.04); margin-bottom: 1rem;
        }
        .panel-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .panel-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .92rem; color: var(--primary); }
        .panel-action { font-size: .78rem; color: var(--accent); text-decoration: none; font-weight: 700; transition: color .18s; }
        .panel-action:hover { color: var(--primary); text-decoration: underline; }

        /* ── SHARED ACTION STYLES ── */
        .action-link {
            display: inline-flex; align-items: center; gap: .4rem;
            border-radius: 9px; background: var(--soft);
            color: var(--primary); border: 1px solid var(--line);
            text-decoration: none; font-size: .8rem; font-weight: 800;
            padding: .55rem .78rem; cursor: pointer;
            transition: background .18s, box-shadow .18s, transform .14s;
        }
        .action-link:hover { background: #fff; box-shadow: 0 3px 12px rgba(26,92,42,.1); transform: translateY(-1px); color: var(--primary); }
        .action-link:active { transform: translateY(0); }
        .action-link.primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .action-link.primary:hover { background: var(--primary); border-color: var(--primary); box-shadow: 0 4px 14px rgba(26,92,42,.22); color: #fff; }
        .account-doc-close { cursor: pointer; transition: background .18s, color .18s; display: grid; place-items: center; }
        .account-doc-close:hover { background: var(--soft); color: var(--primary); }

        /* ── FORM STYLES ── */
        .field-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--primary); display: block; margin-bottom: .35rem; }
        .field-input {
            border: 1.5px solid rgba(26,92,42,.18); border-radius: 9px;
            height: 42px; padding: 0 .85rem; width: 100%; font-size: .88rem;
            color: #1a2e1e; outline: none; transition: border .2s; background: #fff;
        }
        .field-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(76,199,106,.12); }
        .field-input[readonly] { background: var(--soft); color: var(--muted); cursor: default; }
        .field-select {
            border: 1.5px solid rgba(26,92,42,.18); border-radius: 9px;
            height: 42px; padding: 0 .85rem; width: 100%; font-size: .88rem;
            color: #1a2e1e; outline: none; transition: border .2s; background: #fff;
            appearance: auto;
        }
        .field-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(76,199,106,.12); }
        .field-error { font-size: .75rem; color: #c0392b; margin-top: .25rem; }
        .btn-save {
            display: inline-flex; align-items: center; gap: .4rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff; border: none; border-radius: 9px;
            padding: .65rem 1.5rem; font-size: .88rem; font-weight: 700;
            cursor: pointer; transition: box-shadow .2s, transform .2s;
        }
        .btn-save:hover { box-shadow: 0 4px 16px rgba(26,92,42,.3); transform: translateY(-1px); }

        /* ── PROFILE CARD ── */
        .profile-stat {
            background: var(--soft); border-radius: 12px; padding: .85rem 1rem;
            display: flex; align-items: center; gap: .75rem;
        }
        .profile-stat-icon {
            width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
            background: linear-gradient(135deg, var(--accent), var(--pl));
            display: grid; place-items: center; font-size: .95rem; color: #fff;
        }
        .profile-stat-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); }
        .profile-stat-val   { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .92rem; color: #17251b; line-height: 1.2; }

        /* ── MOBILE ── */
        @media (max-width: 1024px) {
            .two-col { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 820px) {
            body { flex-direction: column; }
            .tier-sidebar {
                width: 100%; height: auto; position: static;
                flex-direction: row; align-items: center; flex-wrap: wrap;
                padding: .85rem 1rem; gap: .5rem;
                border-right: none; border-bottom: 1px solid rgba(255,255,255,.08);
            }
            .sb-brand { margin-bottom: 0; }
            .sb-tier-pill { margin-bottom: 0; }
            .sb-section-label, .sb-sep, .sb-foot { display: none; }
            .sb-nav { flex-direction: row; flex-wrap: wrap; gap: .3rem; }
            .sb-link { padding: .4rem .65rem; font-size: .78rem; }
            .sb-menu-summary { padding: .4rem .65rem; font-size: .78rem; }
            .sb-child-nav { flex-direction: row; flex-wrap: wrap; margin: 0; gap: .25rem; }
            .sb-child-link { padding: .32rem .55rem; font-size: .72rem; }
            .tier-content { padding: 1rem; }
        }
        @media (max-width: 560px) {
            .tier-hero { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="tier-sidebar">
        <a class="sb-brand" href="{{ route('dashboard.entry') }}">
            <div class="sb-brand-leaf">🌿</div>
            <div class="sb-brand-word">Olives <em>BI</em></div>
        </a>

        <div class="sb-tier-pill"><i class="bi bi-patch-check-fill"></i> {{ $tierName }}</div>

        <div class="sb-section-label">Navigation</div>
        <nav class="sb-nav">
            @foreach ($sidebarNav as $item)
                @php
                    $isActive = $item['route'] && ($currentRoute === $item['route'] || str_starts_with($currentRoute, $item['route'].'.'));
                    $navUrl   = $item['route'] ? route($item['route']).(!empty($item['fragment']) ? '#'.$item['fragment'] : '') : '#';
                @endphp
                @if (!empty($item['children']))
                    <details class="sb-menu" @if($isActive) open @endif>
                        <summary class="sb-menu-summary {{ $isActive ? 'active' : '' }}">
                            <span class="sb-menu-summary-main"><i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}</span>
                            <i class="bi bi-chevron-down sb-menu-chevron"></i>
                        </summary>
                        <div class="sb-child-nav">
                            @foreach ($item['children'] as $child)
                                @php
                                    $childActive = $child['route'] && ($currentRoute === $child['route']);
                                    $childUrl = $child['route'] ? route($child['route']).(!empty($child['fragment']) ? '#'.$child['fragment'] : '') : '#';
                                @endphp
                                <a class="sb-child-link {{ $childActive ? 'active' : '' }}" href="{{ $childUrl }}">
                                    <i class="bi {{ $child['icon'] ?? 'bi-dot' }}"></i> {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </details>
                @else
                    <a class="sb-link {{ $isActive ? 'active' : '' }}" href="{{ $navUrl }}">
                        <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="sb-foot">
            <div class="sb-user">
                <div class="sb-avatar">{{ strtoupper(substr($userName, 0, 1)) }}</div>
                <div>
                    <div class="sb-user-name">{{ $userName }}</div>
                    <div class="sb-user-handle">{{ $userHandle }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="tier-main">

        <header class="tier-topbar">
            <div class="topbar-title">@yield('page-scope', $tierName)</div>
            <div class="topbar-right">
                <span class="topbar-chip"><i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}</span>
                <a class="topbar-icon-btn" href="{{ $profileRoute }}" title="Profile"><i class="bi bi-person"></i></a>
                <a class="topbar-icon-btn" href="{{ route('auth.logout') }}" title="Sign Out"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </header>

        <div class="tier-content">
            @yield('page-content')
        </div>

    </div><!-- /.tier-main -->

    @stack('page-scripts')
</body>
</html>

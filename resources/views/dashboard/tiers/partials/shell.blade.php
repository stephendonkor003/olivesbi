@php
    $accent      = $accent      ?? '#2d9e4b';
    $tier        = $tier        ?? 'Dashboard';
    $tierSlug    = $tierSlug    ?? 'account';
    $scope       = $scope       ?? 'My Account';
    $subtitle    = $subtitle    ?? 'Welcome to your Olives BI dashboard.';
    $focus       = $focus       ?? [];
    $cards       = $cards       ?? [];
    $metricKeys  = $metricKeys  ?? [];
    $stats       = $stats       ?? [];
    $sidebarNav  = $sidebarNav  ?? [];
    $user        = $user        ?? auth()->user();
    $resolvedTier = $resolvedTier ?? $tierSlug;

    $userName    = optional($user)->name ?? 'User';
    $userHandle  = optional($user)->email ?: (optional($user)->phone ?? '—');
    $userHandle  = optional($user)->email ?: (optional($user)->phone ?? 'Olives BI user');
    $cardNumber  = \App\Support\OlivesCardNumber::format(optional($user)->olives_card_number);
    $profileRoute = $profileRoute ?? route('dashboard.myprofile');
    $contentView = $contentView ?? null;
    $contentData = $contentData ?? [];
    $contentWide = $contentWide ?? false;
    $quickLinks = $quickLinks ?? [
        ['label' => 'Check Locality', 'url' => route('locality.check'), 'hint' => 'Environmental risk, AQI, water, waste'],
        ['label' => 'My Profile', 'url' => $profileRoute, 'hint' => 'Account settings and personal data'],
    ];

    $quickLinks[] = ['label' => 'Sign Out', 'url' => route('auth.logout'), 'hint' => 'End your session securely', 'danger' => true];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tier }} — Olives BI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@700;800;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            content: '';
            display: block; width: 3px; height: 100%;
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
            content: '';
            display: block; width: 3px; height: 100%;
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

        /* Topbar */
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

        /* Content */
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
        .hero-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: clamp(1.35rem, 2.5vw, 1.85rem); line-height: 1.2; margin-bottom: .4rem; }
        .hero-sub { font-size: .88rem; color: rgba(255,255,255,.78); line-height: 1.65; max-width: 580px; }
        .hero-date { font-size: .78rem; color: rgba(255,255,255,.55); white-space: nowrap; text-align: right; }

        /* ── OLIVES CARD ── */
        .card-section { margin-bottom: 1.25rem; }
        .card-section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: .85rem; }
        .section-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .92rem; color: var(--primary); }
        .section-sub { font-size: .78rem; color: var(--muted); }

        .olives-card-wrap {
            perspective: 900px;
        }
        .olives-card {
            width: 100%; max-width: 380px;
            height: 220px; border-radius: 20px;
            position: relative; cursor: default;
            transform-style: preserve-3d;
            transition: transform .45s cubic-bezier(.23,1,.32,1), box-shadow .45s ease;
            box-shadow: 0 20px 60px rgba(26,92,42,.3), 0 6px 20px rgba(0,0,0,.2);
        }
        .olives-card:hover {
            transform: rotateX(6deg) rotateY(-8deg) scale(1.02);
            box-shadow: 0 30px 80px rgba(26,92,42,.4), 0 10px 30px rgba(0,0,0,.25);
        }
        .card-bg {
            position: absolute; inset: 0; border-radius: 20px; overflow: hidden;
            background: linear-gradient(130deg, #071a0d 0%, #0e3318 40%, {{ $accent }} 130%);
        }
        .card-shimmer {
            position: absolute; inset: 0; border-radius: 20px;
            background: linear-gradient(105deg, transparent 30%, rgba(255,255,255,.07) 50%, transparent 70%);
            background-size: 200% 100%;
            animation: shimmer 3.5s linear infinite;
            pointer-events: none;
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .card-dots {
            position: absolute; inset: 0; border-radius: 20px; overflow: hidden;
            background-image: radial-gradient(circle, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
        }
        .card-inner { position: relative; z-index: 2; padding: 1.3rem 1.5rem; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }
        .card-top { display: flex; align-items: center; justify-content: space-between; }
        .card-logo { display: flex; align-items: center; gap: .4rem; }
        .card-logo-leaf {
            width: 30px; height: 30px; border-radius: 50% 8px 50% 8px;
            background: linear-gradient(135deg, rgba(76,199,106,.9), rgba(45,158,75,.8));
            display: grid; place-items: center; font-size: .8rem;
            box-shadow: 0 0 12px rgba(76,199,106,.4);
        }
        .card-logo-word { font-family: 'Poppins', sans-serif; font-weight: 900; font-size: 1rem; color: #fff; }
        .card-logo-word em { color: #4cc76a; font-style: normal; }
        .card-tier-badge {
            font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .65rem;
            text-transform: uppercase; letter-spacing: .5px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
            border-radius: 999px; padding: .18rem .55rem;
            color: rgba(255,255,255,.88);
        }
        .card-chip {
            width: 38px; height: 28px; border-radius: 5px; margin: .65rem 0;
            background: linear-gradient(135deg, #d4af70, #f0d080, #c8a04a, #e8c96a);
            position: relative; overflow: hidden;
        }
        .card-chip::before {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(90deg, rgba(0,0,0,.1) 0, rgba(0,0,0,.1) 1px, transparent 1px, transparent 8px),
                        repeating-linear-gradient(0deg, rgba(0,0,0,.1) 0, rgba(0,0,0,.1) 1px, transparent 1px, transparent 8px);
        }
        .card-number {
            font-family: 'Space Mono', monospace; font-size: .88rem; letter-spacing: .2em;
            color: rgba(255,255,255,.85); margin-bottom: .6rem;
        }
        .card-bottom { display: flex; justify-content: space-between; align-items: flex-end; }
        .card-holder-label { font-size: .58rem; text-transform: uppercase; letter-spacing: .5px; color: rgba(255,255,255,.4); margin-bottom: .15rem; }
        .card-holder-name { font-size: .85rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: .04em; }
        .card-contactless {
            display: flex; flex-direction: column; gap: 2px; align-items: flex-end;
        }
        .card-contactless-arc {
            width: 18px; height: 9px; border-top: 2px solid rgba(255,255,255,.45);
            border-radius: 50% 50% 0 0;
        }
        .card-valid-label { font-size: .58rem; text-transform: uppercase; letter-spacing: .5px; color: rgba(255,255,255,.4); margin-bottom: .12rem; }
        .card-valid-val { font-family: 'Space Mono', monospace; font-size: .78rem; color: rgba(255,255,255,.8); }

        /* ── STATS GRID ── */
        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: .75rem; margin-bottom: 1.25rem; }
        .stat-card {
            background: #fff; border: 1px solid var(--line); border-radius: 14px;
            padding: 1rem 1.1rem; box-shadow: 0 1px 8px rgba(26,92,42,.04);
            transition: box-shadow .2s, transform .2s;
        }
        .stat-card:hover { box-shadow: 0 4px 18px rgba(26,92,42,.1); transform: translateY(-1px); }
        .stat-label { font-size: .67rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); margin-bottom: .4rem; }
        .stat-value { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.55rem; color: #17251b; line-height: 1; }
        .stat-sub { font-size: .72rem; color: var(--muted); margin-top: .3rem; }

        /* ── CONTENT GRID ── */
        .content-grid { display: grid; grid-template-columns: minmax(0,1.1fr) minmax(300px,.9fr); gap: 1rem; align-items: start; }
        .content-grid.content-grid-wide { grid-template-columns: minmax(0,1fr); }
        .content-grid.content-grid-wide .side-panel-stack { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); align-items: start; }

        /* Panels */
        .panel {
            background: #fff; border: 1px solid var(--line); border-radius: 14px;
            padding: 1.2rem; box-shadow: 0 1px 8px rgba(26,92,42,.04);
        }
        .panel-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .panel-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .92rem; color: var(--primary); }
        .panel-action { font-size: .78rem; color: var(--accent); text-decoration: none; font-weight: 700; transition: color .18s; }
        .panel-action:hover { color: var(--primary); text-decoration: underline; }

        /* Focus list */
        .focus-list { list-style: none; display: flex; flex-direction: column; gap: .5rem; }
        .focus-item {
            display: flex; align-items: flex-start; gap: .65rem;
            padding: .65rem .85rem; background: var(--soft); border-radius: 10px;
            font-size: .87rem; color: #17251b;
        }
        .focus-item i { color: var(--accent); flex-shrink: 0; margin-top: .05rem; font-size: 1rem; }

        /* Tool cards */
        .tool-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: .65rem; }
        .tool-card {
            background: var(--soft); border: 1px solid var(--line); border-radius: 12px;
            padding: 1rem; text-decoration: none; color: #17251b;
            transition: background .18s, box-shadow .18s, transform .14s, border-color .18s;
            display: block;
        }
        a.tool-card:hover {
            background: #fff; box-shadow: 0 4px 18px rgba(26,92,42,.1);
            transform: translateY(-2px); color: #17251b; border-color: rgba(45,158,75,.3);
        }
        a.tool-card:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .tool-card-icon { font-size: 1.4rem; color: var(--accent); margin-bottom: .5rem; }
        .tool-card-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: .85rem; margin-bottom: .25rem; }
        .tool-card-desc { font-size: .77rem; color: var(--muted); line-height: 1.45; }

        /* Card + Quick-access side panel */
        .side-panel-stack { display: flex; flex-direction: column; gap: 1rem; }

        /* Activity / info list */
        .info-list { display: flex; flex-direction: column; gap: 0; }
        .info-item {
            display: flex; gap: .7rem; align-items: flex-start;
            padding: .65rem 0; border-bottom: 1px solid var(--line);
        }
        .info-item:last-child { border-bottom: 0; }
        .info-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
            background: var(--accent); margin-top: .35rem;
        }
        .info-text { font-size: .83rem; color: #374151; line-height: 1.5; }
        .info-time { font-size: .73rem; color: var(--muted); margin-top: .1rem; }

        /* ── MOBILE ── */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
            .content-grid { grid-template-columns: 1fr; }
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
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .tool-grid { grid-template-columns: 1fr; }
            .tier-hero { flex-direction: column; align-items: flex-start; }
            .hero-date { text-align: left; }
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

        <div class="sb-tier-pill"><i class="bi bi-patch-check-fill"></i> {{ $tier }}</div>

        @if (!empty($sidebarNav))
            <div class="sb-section-label">Navigation</div>
            <nav class="sb-nav">
                @foreach ($sidebarNav as $item)
                    @if (!empty($item['children']))
                        <details class="sb-menu" @if($item['active'] ?? false) open @endif>
                            <summary class="sb-menu-summary {{ ($item['active'] ?? false) ? 'active' : '' }}">
                                <span class="sb-menu-summary-main"><i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i> {{ $item['label'] }}</span>
                                <i class="bi bi-chevron-down sb-menu-chevron"></i>
                            </summary>
                            <div class="sb-child-nav">
                                @foreach ($item['children'] as $child)
                                    <a class="sb-child-link {{ ($child['active'] ?? false) ? 'active' : '' }}" href="{{ $child['url'] ?? '#' }}">
                                        <i class="bi {{ $child['icon'] ?? 'bi-dot' }}"></i> {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        <a class="sb-link {{ ($item['active'] ?? false) ? 'active' : '' }}" href="{{ $item['url'] ?? '#' }}">
                            <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i> {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        @else
            <div class="sb-section-label">Navigation</div>
            <nav class="sb-nav">
                <a class="sb-link active" href="{{ route('dashboard.entry') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a class="sb-link" href="{{ route('locality.check') }}"><i class="bi bi-geo-alt"></i> Check Locality</a>
                <a class="sb-link" href="{{ route('dashboard.myprofile') }}"><i class="bi bi-person-circle"></i> My Profile</a>
            </nav>
            <div class="sb-sep"></div>
            <nav class="sb-nav">
                <a class="sb-link" href="{{ route('dashboard.operational') }}"><i class="bi bi-grid-1x2"></i> Classic System</a>
                <a class="sb-link" href="{{ route('auth.logout') }}"><i class="bi bi-box-arrow-left"></i> Sign Out</a>
            </nav>
        @endif

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

    <!-- MAIN CONTENT -->
    <div class="tier-main">

        <!-- Topbar -->
        <header class="tier-topbar">
            <div class="topbar-title">{{ $scope }}</div>
            <div class="topbar-right">
                <span class="topbar-chip"><i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}</span>
                <a class="topbar-icon-btn" href="{{ $profileRoute }}" title="Profile"><i class="bi bi-person"></i></a>
                <a class="topbar-icon-btn" href="{{ route('auth.logout') }}" title="Sign Out"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </header>

        <div class="tier-content">

            <!-- Hero Banner -->
            <div class="tier-hero">
                <div>
                    <div class="hero-badge"><i class="bi bi-layers-fill"></i> {{ $tier }}</div>
                    <div class="hero-title">{{ $subtitle }}</div>
                    <div class="hero-sub">{{ $scope }} — Olives BI environmental and revenue intelligence platform.</div>
                </div>
                <div class="hero-date">
                    <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:.2rem;">Session</div>
                    {{ now()->format('H:i') }} · {{ now()->format('l') }}
                </div>
            </div>

            @if (session('status'))
                <div class="panel" style="border-color:rgba(45,158,75,.25);background:#f0fdf4;color:var(--primary);margin-bottom:1rem;font-weight:700;">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Stat Cards -->
            <div class="stats-grid">
                @forelse ($metricKeys as $label => $key)
                    <div class="stat-card">
                        <div class="stat-label">{{ $label }}</div>
                        <div class="stat-value">{{ number_format($stats[$key] ?? 0) }}</div>
                        <div class="stat-sub">Live data</div>
                    </div>
                @empty
                    <div class="stat-card"><div class="stat-label">Account</div><div class="stat-value">—</div><div class="stat-sub">No metrics configured</div></div>
                    <div class="stat-card"><div class="stat-label">Status</div><div class="stat-value">Active</div><div class="stat-sub">Good standing</div></div>
                    <div class="stat-card"><div class="stat-label">Tier</div><div class="stat-value">{{ $resolvedTier }}</div><div class="stat-sub">Access level</div></div>
                    <div class="stat-card"><div class="stat-label">Portal</div><div class="stat-value">Live</div><div class="stat-sub">All systems up</div></div>
                @endforelse
            </div>

            <!-- Main Grid: Modules + Side Panel -->
            <div class="content-grid {{ $contentWide ? 'content-grid-wide' : '' }}">

                <!-- Left: Capabilities + Modules -->
                <div style="display:flex;flex-direction:column;gap:1rem;">

                    @if ($contentView)
                        @include($contentView, $contentData)
                    @else
                        @if (!empty($focus))
                        <div class="panel">
                            <div class="panel-head">
                                <div class="panel-title"><i class="bi bi-check2-all" style="color:var(--accent);"></i> Primary Capabilities</div>
                            </div>
                            <ul class="focus-list">
                                @foreach ($focus as $item)
                                    <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if (!empty($cards))
                        <div class="panel">
                            <div class="panel-head">
                                <div class="panel-title"><i class="bi bi-grid-3x3-gap-fill" style="color:var(--accent);"></i> Workspace Modules</div>
                            </div>
                            <div class="tool-grid">
                                @foreach ($cards as $card)
                                    <a class="tool-card" href="{{ $card['url'] ?? '#' }}">
                                        <div class="tool-card-icon"><i class="bi {{ $card['icon'] ?? 'bi-box' }}"></i></div>
                                        <div class="tool-card-title">{{ $card['title'] }}</div>
                                        <div class="tool-card-desc">{{ $card['text'] }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endif
                </div>

                <!-- Right: Olives Card + Quick Links -->
                <div class="side-panel-stack">

                    <!-- Olives Card -->
                    <div class="panel">
                        <div class="panel-head">
                            <div class="panel-title"><i class="bi bi-credit-card-2-front-fill" style="color:var(--accent);"></i> Olives Account Card</div>
                        </div>
                        <div class="olives-card-wrap">
                            <div class="olives-card">
                                <div class="card-bg"></div>
                                <div class="card-shimmer"></div>
                                <div class="card-dots"></div>
                                <div class="card-inner">
                                    <div class="card-top">
                                        <div class="card-logo">
                                            <div class="card-logo-leaf">🌿</div>
                                            <div class="card-logo-word">Olives <em>BI</em></div>
                                        </div>
                                        <div class="card-tier-badge">{{ $tier }}</div>
                                    </div>
                                    <div>
                                        <div class="card-chip"></div>
                                        <div class="card-number">{{ $cardNumber }}</div>
                                    </div>
                                    <div class="card-bottom">
                                        <div>
                                            <div class="card-holder-label">Account Holder</div>
                                            <div class="card-holder-name">{{ strtoupper(substr($userName, 0, 20)) }}</div>
                                            <div style="margin-top:.5rem;">
                                                <div class="card-valid-label">Valid Thru</div>
                                                <div class="card-valid-val">{{ now()->format('m/y') }} — {{ now()->addYears(3)->format('m/y') }}</div>
                                            </div>
                                        </div>
                                        <div class="card-contactless">
                                            <div class="card-contactless-arc" style="width:22px;height:11px;opacity:.7;"></div>
                                            <div class="card-contactless-arc" style="width:16px;height:8px;opacity:.5;"></div>
                                            <div class="card-contactless-arc" style="width:10px;height:5px;opacity:.35;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top:.85rem;font-size:.78rem;color:var(--muted);line-height:1.55;">
                            This is your Olives BI digital account card for the <strong>{{ $tier }}</strong> tier.
                            Use this card number with your password to sign in by card, email, or phone.
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="panel">
                        <div class="panel-head">
                            <div class="panel-title"><i class="bi bi-lightning-charge-fill" style="color:var(--accent);"></i> Quick Access</div>
                        </div>
                        <div class="info-list">
                            @foreach ($quickLinks as $link)
                            <div class="info-item" @if($loop->last) style="border:0;" @endif>
                                <div class="info-dot" @if($link['danger'] ?? false) style="background:#e74c3c;" @endif></div>
                                <div>
                                    <a href="{{ $link['url'] ?? '#' }}" style="color:{{ ($link['danger'] ?? false) ? '#e74c3c' : 'var(--primary)' }};font-weight:700;text-decoration:none;font-size:.85rem;">{{ $link['label'] }}</a>
                                    <div class="info-time">{{ $link['hint'] ?? '' }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        </div><!-- /.tier-content -->
    </div><!-- /.tier-main -->

</body>
</html>

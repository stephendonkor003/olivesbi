@extends('dashboard.tiers.partials.tier-page-layout')

@section('page-title', 'Check Locality')
@section('page-scope', 'Check Locality')

@section('page-head-extra')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

    <style>
        /* locality-specific variables (do not redefine --accent / --primary — inherited from tier layout) */
        :root {
            --primary-light: #2d9e4b;
            --accent-soft:   #a8e6bc;
            --light-green:   #e8f5e9;
            --dark-bg:       #071a0d;
            --risk-low:      #1a5c2a;
            --risk-mod:      #b97c05;
            --risk-high:     #c0392b;
        }

        /* ── NAVBAR ── */
        .cl-nav {
            position: sticky; top: 0; z-index: 1000;
            background: rgba(7,26,13,.97);
            backdrop-filter: blur(10px);
            padding: .65rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,.25);
        }
        .cl-nav .brand {
            font-family: 'Poppins', sans-serif; font-weight: 800;
            font-size: 1.3rem; color: #fff; text-decoration: none;
            display: flex; align-items: center; gap: .5rem;
        }
        .cl-nav .brand .leaf {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            border-radius: 50% 10px 50% 10px;
            display: flex; align-items: center; justify-content: center; font-size: 1rem;
        }
        .cl-nav .brand em { color: var(--accent); font-style: normal; }
        .cl-nav-links { display: flex; align-items: center; gap: .25rem; }
        .cl-nav-links a {
            color: rgba(255,255,255,.75); font-size: .87rem; font-weight: 500;
            padding: .35rem .85rem; border-radius: 6px; text-decoration: none;
            transition: all .2s;
        }
        .cl-nav-links a:hover { color: var(--accent); background: rgba(255,255,255,.07); }
        .cl-nav-links a.active { color: var(--accent); }
        .btn-nav-back {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.8); border-radius: 8px;
            padding: .35rem 1rem; font-size: .83rem; font-weight: 500;
            text-decoration: none; transition: all .2s;
        }
        .btn-nav-back:hover { background: rgba(255,255,255,.18); color: #fff; }

        /* ── HERO STRIP ── */
        .cl-hero {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary) 100%);
            position: relative; overflow: hidden; padding: 2.5rem 0 2rem;
        }
        .cl-hero::before {
            content: ''; position: absolute; inset: 0;
            background: url('https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?auto=format&fit=crop&w=1400&q=30') center/cover;
            opacity: .12;
        }
        .cl-hero-inner { position: relative; z-index: 2; }
        .cl-hero h1 {
            font-family: 'Poppins', sans-serif; font-weight: 800;
            font-size: clamp(1.5rem, 3.5vw, 2.3rem); color: #fff; margin-bottom: .4rem;
        }
        .cl-hero h1 span { color: var(--accent); }
        .cl-hero p { color: rgba(255,255,255,.65); font-size: .92rem; max-width: 560px; line-height: 1.6; margin: 0; }
        .cl-badge {
            display: inline-flex; align-items: center; gap: .35rem;
            background: rgba(76,199,106,.15); border: 1px solid rgba(76,199,106,.35);
            color: var(--accent-soft); border-radius: 50px;
            padding: .25rem .85rem; font-size: .75rem; font-weight: 700;
            letter-spacing: .5px; text-transform: uppercase; margin-bottom: .75rem;
        }

        /* ── SEARCH STRIP ── */
        .search-strip {
            background: #fff;
            border-radius: 14px;
            padding: 1.1rem 1.25rem;
            box-shadow: 0 2px 16px rgba(26,92,42,.09);
            border: 1.5px solid var(--line);
            display: flex; flex-wrap: wrap; gap: .75rem; align-items: flex-end;
            margin-top: -1.5rem; position: relative; z-index: 1050;
        }
        .ss-field { flex: 1 1 140px; }
        .ss-label {
            font-size: .72rem; font-weight: 700; color: var(--primary);
            text-transform: uppercase; letter-spacing: .5px;
            display: block; margin-bottom: .35rem;
        }
        .ss-input {
            border: 1.5px solid rgba(26,92,42,.18); border-radius: 9px;
            height: 42px; padding: 0 .75rem; width: 100%; font-size: .88rem;
            color: #1a2e1e; outline: none; transition: border .2s;
        }
        .ss-input:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(45,158,75,.12); }
        .ss-btn {
            height: 42px; border: none; border-radius: 9px;
            font-weight: 700; font-size: .85rem; cursor: pointer;
            display: inline-flex; align-items: center; gap: .4rem;
            padding: 0 1.1rem; transition: all .2s; white-space: nowrap;
        }
        .ss-btn-primary  { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; }
        .ss-btn-primary:hover  { box-shadow: 0 3px 12px rgba(26,92,42,.35); transform: translateY(-1px); }
        .ss-btn-gps      { background: var(--accent); color: var(--dark-bg); }
        .ss-btn-gps:hover      { background: #3db85d; }
        .ss-btn-reset    { background: var(--light-green); color: var(--primary); }
        .ss-btn-reset:hover    { background: #c8e6c9; }

        /* ── AUTOCOMPLETE DROPDOWN ── */
        .ac-wrap { position: relative; }
        .ac-dropdown {
            position: absolute; top: calc(100% + 5px); left: 0;
            min-width: 320px; max-width: 460px;
            background: #fff; border-radius: 12px;
            border: 1.5px solid rgba(26,92,42,.22);
            box-shadow: 0 10px 36px rgba(26,92,42,.2);
            z-index: 9999; max-height: 360px; overflow-y: auto;
            display: none;
        }
        .ac-dropdown.open { display: block; }
        .ac-group-header {
            padding: .4rem .8rem .25rem;
            font-size: .67rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: .65px;
            color: var(--primary); background: var(--light-green);
            border-top: 1px solid rgba(26,92,42,.1);
            position: sticky; top: 0;
        }
        .ac-group-header:first-child { border-top: 0; border-radius: 10px 10px 0 0; }
        .ac-item {
            padding: .55rem .85rem;
            font-size: .83rem; color: #1a2e1e;
            cursor: pointer; display: flex; align-items: center; gap: .55rem;
            border-top: 1px solid rgba(26,92,42,.05);
            transition: background .12s;
        }
        .ac-item:hover, .ac-item.ac-focused {
            background: var(--light-green); color: var(--primary);
        }
        .ac-item .ac-icon { font-size: .9rem; flex-shrink: 0; }
        .ac-item .ac-body { flex: 1; min-width: 0; }
        .ac-item .ac-main { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ac-item .ac-sub  { font-size: .72rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ac-item:hover .ac-sub, .ac-item.ac-focused .ac-sub { color: var(--primary-light); }
        .ac-type-badge {
            font-size: .65rem; font-weight: 700;
            background: rgba(26,92,42,.1); color: var(--primary);
            border-radius: 4px; padding: .1rem .4rem;
            text-transform: capitalize; flex-shrink: 0;
        }
        .ac-msg { padding: .8rem; text-align: center; color: var(--muted); font-size: .82rem; }
        @keyframes ac-spin { to { transform: rotate(360deg); } }
        .ac-spinner-icon { display: inline-block; animation: ac-spin .7s linear infinite; }

        /* ── STATUS BAR ── */
        .status-bar {
            display: flex; align-items: center; gap: .5rem;
            font-size: .8rem; color: var(--muted); padding: .4rem 0;
        }
        .status-dot {
            width: 9px; height: 9px; border-radius: 50%;
            background: var(--risk-mod); flex-shrink: 0;
            transition: background .5s;
        }
        .status-dot.low  { background: var(--risk-low); }
        .status-dot.high { background: var(--risk-high); }

        /* ── LAYOUT ── */
        .cl-grid {
            display: grid;
            grid-template-columns: minmax(0,1.5fr) 368px;
            gap: 1.25rem;
            align-items: start;
        }
        .map-col  { min-width: 0; }
        .data-col { display: flex; flex-direction: column; gap: .9rem; }

        /* ── LOCATION BAR ── */
        .loc-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: .6rem 1rem; background: #fff;
            border: 1.5px solid var(--line); border-radius: 12px 12px 0 0;
            border-bottom: none; gap: .75rem;
        }
        .loc-place {
            font-weight: 700; font-size: .88rem; color: #1a2e1e;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 320px;
        }
        .risk-pill {
            flex-shrink: 0; font-size: .7rem; font-weight: 800;
            padding: .22rem .75rem; border-radius: 50px;
            background: var(--light-green); color: var(--primary);
            text-transform: uppercase; letter-spacing: .4px;
            border: 1px solid rgba(26,92,42,.18);
        }
        .risk-pill.moderate { background: #fff8e1; color: #7d4e00; border-color: rgba(197,130,0,.3); }
        .risk-pill.high     { background: #fce4ec; color: #880e4f; border-color: rgba(136,14,79,.2); }

        /* ── MAP ── */
        #locality-map {
            width: 100%; height: 420px;
            border-left: 1.5px solid var(--line);
            border-right: 1.5px solid var(--line);
        }

        /* ── MAP FOOTER STRIP ── */
        .map-footer {
            display: grid; grid-template-columns: 1fr 1fr 1fr;
            background: #fff;
            border: 1.5px solid var(--line); border-top: 1px solid var(--line);
            border-radius: 0 0 12px 12px; overflow: hidden;
        }
        .mf-cell { padding: .8rem .9rem; border-right: 1px solid var(--line); }
        .mf-cell:last-child { border-right: 0; }
        .mf-title {
            font-size: .68rem; font-weight: 800; color: var(--primary);
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: .5rem; display: flex; align-items: center; gap: .3rem;
        }

        /* ── BOUNCING PIN ── */
        .bounce-wrapper {
            animation: pinBounce 1.3s ease-in-out infinite;
            transform-origin: bottom center;
        }
        .bounce-pin {
            width: 32px; height: 44px; position: relative;
        }
        .pin-body {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            border: 3px solid #fff;
            box-shadow: 0 3px 12px rgba(26,92,42,.45);
            position: absolute; top: 0; left: 1px;
        }
        .pin-body::after {
            content: '🌿';
            font-size: 12px;
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%,-50%) rotate(45deg);
        }
        .pin-tip {
            position: absolute; bottom: 6px; left: 12px;
            width: 0; height: 0;
            border-left: 3px solid transparent;
            border-right: 3px solid transparent;
            border-top: 8px solid var(--primary);
        }
        .pin-shadow {
            width: 16px; height: 5px;
            background: rgba(0,0,0,.25);
            border-radius: 50%;
            position: absolute; bottom: 0; left: 8px;
            animation: shadowPulse 1.3s ease-in-out infinite;
        }
        @keyframes pinBounce {
            0%, 100% { transform: translateY(0); }
            40%       { transform: translateY(-14px); }
            60%       { transform: translateY(-10px); }
        }
        @keyframes shadowPulse {
            0%, 100% { transform: scaleX(1);   opacity: .4; }
            40%       { transform: scaleX(.6); opacity: .15; }
        }

        /* ── RISK SCORE CARD ── */
        .risk-band {
            border-radius: 14px; padding: 1.35rem 1.5rem 1.2rem;
            color: #fff; position: relative; overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,.2);
        }
        .risk-band::before {
            content: ''; position: absolute; right: -28px; top: -28px;
            width: 120px; height: 120px; background: rgba(255,255,255,.07); border-radius: 50%;
        }
        .risk-band::after {
            content: ''; position: absolute; right: 18px; bottom: -22px;
            width: 75px; height: 75px; background: rgba(255,255,255,.05); border-radius: 50%;
        }
        .risk-band.low      { background: linear-gradient(135deg, #145a2e, var(--primary-light)); }
        .risk-band.moderate { background: linear-gradient(135deg, #7d4e00, #d4a017); }
        .risk-band.high     { background: linear-gradient(135deg, #7f1d1d, var(--risk-high)); }
        .risk-band .label   { font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .65px; opacity: .78; margin-bottom: .3rem; display: block; }
        .risk-band .score   { font-family:'Poppins',sans-serif; font-size: 3.2rem; font-weight: 800; line-height: 1; position: relative; z-index: 1; }
        .risk-band .summary { font-size: .78rem; opacity: .9; margin-top: .45rem; line-height: 1.55; position: relative; z-index: 1; }

        /* ── KPI TILES ── */
        .kpi-section { background: #fff; border-radius: 14px; border: 1.5px solid var(--line); overflow: hidden; }
        .kpi-hdr {
            padding: .58rem 1rem; font-size: .69rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: .55px; color: var(--primary);
            background: var(--light-green); border-bottom: 1px solid var(--line);
            display: flex; align-items: center; gap: .4rem;
        }
        .kpi-grid { display: grid; grid-template-columns: repeat(4,1fr); }
        .kpi-tile {
            padding: .7rem .45rem; text-align: center;
            border-right: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            border-top: 3px solid transparent;
            transition: background .15s;
        }
        .kpi-tile:hover { background: var(--light-green); }
        .kpi-tile:nth-child(4n)        { border-right: 0; }
        .kpi-tile:nth-last-child(-n+4) { border-bottom: 0; }
        .kpi-icon { font-size: .95rem; display: block; margin-bottom: .18rem; }
        .kpi-val  { font-family:'Poppins',sans-serif; font-weight: 800; font-size: 1.05rem; color: #1a2e1e; line-height: 1; }
        .kpi-lbl  { font-size: .61rem; color: var(--muted); margin-top: .18rem; line-height: 1.2; }
        .m-unit   { font-size: .6rem; color: var(--muted); }
        .kpi-tile.kpi-air    { border-top-color: #1565c0; }
        .kpi-tile.kpi-pm     { border-top-color: #5c6bc0; }
        .kpi-tile.kpi-env    { border-top-color: #f59e0b; }
        .kpi-tile.kpi-waste  { border-top-color: #f57c00; }
        .kpi-tile.kpi-hazard { border-top-color: #c0392b; }
        .kpi-tile.kpi-natural{ border-top-color: var(--primary-light); }
        .kpi-tile.kpi-water  { border-top-color: #0288d1; }

        /* ── TABS ── */
        .tab-section { background: #fff; border-radius: 14px; border: 1.5px solid var(--line); overflow: hidden; }
        .tab-nav { display: flex; border-bottom: 1.5px solid var(--line); background: #fafafa; }
        .tab-nav-btn {
            flex: 1; padding: .58rem .3rem; font-size: .72rem; font-weight: 700;
            color: var(--muted); background: none; border: none; cursor: pointer;
            border-bottom: 2.5px solid transparent; margin-bottom: -1.5px;
            transition: all .15s; display: flex; align-items: center; justify-content: center; gap: .28rem;
        }
        .tab-nav-btn:hover { color: var(--primary); background: var(--light-green); }
        .tab-nav-btn.active { color: var(--primary); border-bottom-color: var(--primary-light); background: #fff; }
        .tab-pane { display: none; padding: .9rem 1rem; }
        .tab-pane.active { display: block; }

        /* Mini-KPI row inside tabs */
        .mini-kpi-row {
            display: grid; grid-template-columns: repeat(3,1fr); gap: .45rem;
            margin-top: .75rem; padding-top: .75rem; border-top: 1px solid var(--line);
        }
        .mini-kpi {
            display: flex; align-items: center; gap: .4rem;
            background: var(--light-green); border-radius: 8px; padding: .45rem .55rem;
        }
        .mini-kpi > span { font-size: 1.1rem; flex-shrink: 0; }
        .mk-val { font-family:'Poppins',sans-serif; font-weight: 700; font-size: .88rem; color: #1a2e1e; line-height: 1; }
        .mk-lbl { font-size: .63rem; color: var(--muted); }

        /* ── FEATURE LIST ── */
        .feature-list { list-style: none; margin: 0; padding: 0; }
        .feature-list li {
            display: flex; justify-content: space-between; align-items: center;
            padding: .5rem 0; border-top: 1px solid var(--line); font-size: .82rem;
        }
        .feature-list li:first-child { border-top: 0; }
        .feature-list .fl-icon { margin-right: .35rem; }
        .feature-list .fl-count { font-family:'Poppins',sans-serif; font-weight: 700; font-size: .9rem; color: var(--primary); }

        /* ── AIR QUALITY BARS ── */
        .aq-bar { display: flex; align-items: center; gap: .55rem; margin-bottom: .48rem; font-size: .81rem; }
        .aq-label { width: 80px; color: var(--muted); flex-shrink: 0; }
        .aq-track { flex: 1; height: 7px; background: var(--light-green); border-radius: 4px; overflow: hidden; }
        .aq-fill  { height: 100%; border-radius: 4px; background: linear-gradient(90deg,var(--primary-light),var(--accent)); transition: width .8s ease; }
        .aq-val   { min-width: 40px; text-align: right; font-weight: 600; color: #1a2e1e; }

        /* ── SOURCE LIST ── */
        .source-list { list-style: none; margin: 0; padding: 0; }
        .source-list li {
            display: flex; justify-content: space-between; align-items: center;
            padding: .44rem 0; border-top: 1px solid var(--line); font-size: .78rem; color: var(--muted);
        }
        .source-list li:first-child { border-top: 0; }
        .source-list .s-badge { background: var(--light-green); color: var(--primary); border-radius: 5px; padding: .1rem .45rem; font-size: .68rem; font-weight: 700; }

        /* ── WEATHER ── */
        .wx-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: .4rem; }
        .wx-item { background: var(--light-green); border-radius: 8px; padding: .45rem .4rem; text-align: center; }
        .wx-icon { font-size: 1.2rem; }
        .wx-val  { font-family:'Poppins',sans-serif; font-weight: 700; font-size: .88rem; color: #1a2e1e; }
        .wx-lbl  { font-size: .64rem; color: var(--muted); display: block; }

        /* ── TAG PILLS ── */
        .tag-pill { display: inline-block; background: var(--light-green); color: var(--primary); border-radius: 50px; padding: .18rem .6rem; font-size: .72rem; font-weight: 600; margin: .12rem; }
        .tag-pill.natural  { background: #e0f2f1; color: #004d40; }
        .tag-pill.water    { background: #e3f2fd; color: #0d47a1; }
        .tag-pill.hazard   { background: #fce4ec; color: #880e4f; }
        .tag-pill.industry { background: #fff3e0; color: #e65100; }

        /* ── ALERT CHIPS ── */
        .alert-chip {
            display: flex; align-items: center; gap: .45rem;
            padding: .48rem .75rem; border-radius: 8px; font-size: .79rem;
            font-weight: 600; margin-bottom: .38rem;
        }
        .alert-chip:last-child { margin-bottom: 0; }
        .alert-chip.ok   { background: #e8f5e9; color: var(--primary); }
        .alert-chip.warn { background: #fff8e1; color: #7d4e00; }
        .alert-chip.bad  { background: #fce4ec; color: #880e4f; }

        /* ── FOOTER ── */
        .cl-footer {
            background: var(--dark-bg);
            padding: 1.25rem 0; text-align: center;
            color: rgba(255,255,255,.35); font-size: .78rem;
            margin-top: 3rem;
        }
        .cl-footer span { color: var(--accent); }

        /* ── HEALTH RISK CARDS ── */
        .health-risk-card {
            background: var(--light-green); border-radius: 10px;
            padding: .65rem .6rem; text-align: center;
            border: 1.5px solid rgba(26,92,42,.08);
            transition: transform .2s;
        }
        .health-risk-card:hover { transform: translateY(-2px); }
        .hr-icon { font-size: 1.3rem; margin-bottom: .2rem; }
        .hr-label { font-size: .63rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: .4px; margin-bottom: .3rem; line-height: 1.2; }
        .hr-val { font-family:'Poppins',sans-serif; font-weight: 800; font-size: .78rem; padding: .15rem .45rem; border-radius: 50px; display: inline-block; }
        .hr-val.hr-low  { background: #e8f5e9; color: #1a5c2a; }
        .hr-val.hr-mod  { background: #fff8e1; color: #7d4e00; }
        .hr-val.hr-high { background: #fce4ec; color: #880e4f; }

        @media (max-width: 960px) {
            .cl-grid { grid-template-columns: 1fr; }
            #locality-map { height: 340px; }
            .map-footer { grid-template-columns: 1fr; }
            .mf-cell { border-right: 0; border-top: 1px solid var(--line); }
            .mf-cell:first-child { border-top: 0; }
            .loc-place { max-width: 200px; }
            .kpi-grid { grid-template-columns: repeat(4,1fr); }
        }
        @media (max-width: 640px) {
            .search-strip { flex-direction: column; align-items: stretch; }
            .ss-field { flex-basis: auto !important; }
            .kpi-grid { grid-template-columns: repeat(4,1fr); }
            .mini-kpi-row { grid-template-columns: 1fr 1fr 1fr; }
            .cl-nav-links { display: none !important; }
            /* mobile hamburger */
            .nav-mobile-toggle { display: flex !important; }
            .nav-mobile-menu {
                position: fixed; inset: 0; z-index: 2000;
                background: rgba(7,26,13,.98); flex-direction: column;
                align-items: flex-start; padding: 1.5rem;
                gap: .25rem;
            }
            .nav-mobile-menu.open { display: flex !important; }
        }
    </style>
@endsection

@section('page-content')

{{-- Page hero --}}
<div class="tier-hero" style="margin-bottom:1.25rem;">
    <div>
        <div class="hero-badge"><i class="bi bi-geo-alt-fill"></i> Environmental Intelligence</div>
        <div class="hero-title">Check Locality</div>
        <div class="hero-sub">Enter an area name or coordinates to check air quality, waste sites, hazards, land use, and a composite environmental risk score.</div>
    </div>
    <div style="font-size:2rem; opacity:.35; display:flex; gap:.65rem; align-items:center; flex-shrink:0;">
        <span>💨</span><span>💧</span><span>🌲</span><span>♻️</span><span>⚠️</span>
    </div>
</div>

<!-- ── MAIN CONTENT ── -->
<div class="container" style="padding-top:1.75rem; padding-bottom:2.5rem;">

    <!-- Search strip -->
    <div class="search-strip" id="searchStrip">
        <div class="ss-field ac-wrap" style="flex-basis:260px;">
            <label class="ss-label" for="area-search">Area / Place Name</label>
            <input class="ss-input" id="area-search" placeholder="e.g. Osu, Accra or Kumasi Central" autocomplete="off">
            <div class="ac-dropdown" id="ac-dropdown"></div>
        </div>
        <div class="ss-field" style="flex-basis:120px;">
            <label class="ss-label" for="latitude">Latitude</label>
            <input class="ss-input" id="latitude" inputmode="decimal" placeholder="5.6037" value="5.6037">
        </div>
        <div class="ss-field" style="flex-basis:120px;">
            <label class="ss-label" for="longitude">Longitude</label>
            <input class="ss-input" id="longitude" inputmode="decimal" placeholder="-0.1870" value="-0.1870">
        </div>
        <div style="display:flex; gap:.5rem; align-items:flex-end; flex-wrap:wrap;">
            <button class="ss-btn ss-btn-gps" id="locate-button" type="button">
                <i class="bi bi-crosshair2"></i> My Location
            </button>
            <button class="ss-btn ss-btn-primary" id="check-button" type="button">
                <i class="bi bi-search"></i> Check Area
            </button>
            <button class="ss-btn ss-btn-reset" id="clear-button" type="button" title="Reset">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
        </div>
    </div>

    <!-- Status bar -->
    <div class="status-bar my-2">
        <span class="status-dot" id="status-dot"></span>
        <span id="status-text">Ready — showing Accra, Ghana.</span>
        <span class="ms-auto" id="coords-display" style="font-size:.75rem; color:var(--primary); font-weight:600;"></span>
    </div>

    <!-- ── MAIN GRID ── -->
    <div class="cl-grid mt-1">

        <!-- LEFT: Map column -->
        <div class="map-col">

            <!-- Location bar above the map -->
            <div class="loc-bar">
                <div class="d-flex align-items-center gap-2" style="min-width:0;">
                    <span class="status-dot" id="status-dot2" style="flex-shrink:0;"></span>
                    <span class="loc-place" id="place-name">Accra, Ghana</span>
                </div>
                <span class="risk-pill moderate" id="risk-pill">–</span>
            </div>

            <!-- Map -->
            <div id="locality-map"></div>

            <!-- Map footer: 3-cell inline strip -->
            <div class="map-footer">
                <div class="mf-cell">
                    <div class="mf-title"><i class="bi bi-thermometer-half"></i> Weather</div>
                    <div class="wx-grid" id="wx-grid">
                        <div class="wx-item"><div class="wx-icon">🌡️</div><div class="wx-val" id="wx-temp">--</div><span class="wx-lbl">Temp °C</span></div>
                        <div class="wx-item"><div class="wx-icon">💨</div><div class="wx-val" id="wx-wind">--</div><span class="wx-lbl">Wind km/h</span></div>
                        <div class="wx-item"><div class="wx-icon">💧</div><div class="wx-val" id="wx-hum">--</div><span class="wx-lbl">Humidity %</span></div>
                    </div>
                </div>
                <div class="mf-cell">
                    <div class="mf-title"><i class="bi bi-exclamation-triangle"></i> Environmental Alerts</div>
                    <div id="alert-chips">
                        <div class="alert-chip warn"><i class="bi bi-hourglass-split"></i> Waiting for data…</div>
                    </div>
                </div>
                <div class="mf-cell">
                    <div class="mf-title"><i class="bi bi-tree"></i> Natural Features Nearby</div>
                    <div id="natural-tags"><span class="tag-pill natural">Loading…</span></div>
                </div>
            </div>

            <!-- ── AREA SUMMARY ── -->
            <div style="background:#fff; border:1.5px solid var(--line); border-radius:14px; padding:1.25rem 1.4rem; margin-top:1rem;">
                <div style="display:flex; align-items:center; gap:.5rem; margin-bottom:.7rem;">
                    <span style="font-size:1.2rem;">📋</span>
                    <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:.92rem; color:var(--primary);">Area Environmental Summary</span>
                </div>
                <p id="area-summary-text" style="font-size:.84rem; color:#374151; line-height:1.7; margin:0;">
                    Run a locality check to generate an AI-assisted summary of the environmental conditions, land use, air quality, waste management and hazard profile for this area.
                </p>
            </div>

            <!-- ── HEALTH RISKS ── -->
            <div style="background:#fff; border:1.5px solid var(--line); border-radius:14px; padding:1.25rem 1.4rem; margin-top:1rem;">
                <div style="display:flex; align-items:center; gap:.5rem; margin-bottom:.9rem;">
                    <span style="font-size:1.2rem;">🏥</span>
                    <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:.92rem; color:var(--primary);">Possible Health &amp; Ecological Risks</span>
                </div>
                <div id="health-risks-grid" style="display:grid; grid-template-columns:repeat(3,1fr); gap:.6rem;">
                    <div class="health-risk-card" id="hr-human">
                        <div class="hr-icon">👤</div>
                        <div class="hr-label">Human Health</div>
                        <div class="hr-val" id="hr-human-val">–</div>
                    </div>
                    <div class="health-risk-card" id="hr-animals">
                        <div class="hr-icon">🐾</div>
                        <div class="hr-label">Animals &amp; Wildlife</div>
                        <div class="hr-val" id="hr-animals-val">–</div>
                    </div>
                    <div class="health-risk-card" id="hr-water">
                        <div class="hr-icon">💧</div>
                        <div class="hr-label">Water Bodies</div>
                        <div class="hr-val" id="hr-water-val">–</div>
                    </div>
                    <div class="health-risk-card" id="hr-flood">
                        <div class="hr-icon">🌊</div>
                        <div class="hr-label">Flood Risk</div>
                        <div class="hr-val" id="hr-flood-val">–</div>
                    </div>
                    <div class="health-risk-card" id="hr-waste">
                        <div class="hr-icon">♻️</div>
                        <div class="hr-label">Waste &amp; Pollution</div>
                        <div class="hr-val" id="hr-waste-val">–</div>
                    </div>
                    <div class="health-risk-card" id="hr-plants">
                        <div class="hr-icon">🌿</div>
                        <div class="hr-label">Plant &amp; Forest</div>
                        <div class="hr-val" id="hr-plants-val">–</div>
                    </div>
                </div>
            </div>

        </div><!-- /map-col -->

        <!-- RIGHT: Data column -->
        <aside class="data-col">

            <!-- Risk score hero card -->
            <div class="risk-band moderate" id="risk-band">
                <span class="label">Environmental Risk Score</span>
                <div class="score" id="risk-score">–</div>
                <div class="summary" id="risk-summary">Run a locality check to calculate the composite score.</div>
            </div>

            <!-- KPI tiles (8 indicators) -->
            <div class="kpi-section">
                <div class="kpi-hdr"><i class="bi bi-bar-chart-fill"></i> Key Indicators</div>
                <div class="kpi-grid">
                    <div class="kpi-tile kpi-air">
                        <span class="kpi-icon">💨</span>
                        <div class="kpi-val" id="metric-aqi">--</div>
                        <div class="kpi-lbl">US AQI</div>
                    </div>
                    <div class="kpi-tile kpi-pm">
                        <span class="kpi-icon">🔬</span>
                        <div class="kpi-val" id="metric-pm25">--</div>
                        <div class="kpi-lbl">PM2.5</div>
                    </div>
                    <div class="kpi-tile kpi-pm">
                        <span class="kpi-icon">🌫️</span>
                        <div class="kpi-val" id="metric-pm10">--</div>
                        <div class="kpi-lbl">PM10</div>
                    </div>
                    <div class="kpi-tile kpi-env">
                        <span class="kpi-icon">☀️</span>
                        <div class="kpi-val" id="metric-uv">--</div>
                        <div class="kpi-lbl">UV Index</div>
                    </div>
                    <div class="kpi-tile kpi-waste">
                        <span class="kpi-icon">♻️</span>
                        <div class="kpi-val" id="metric-waste">--</div>
                        <div class="kpi-lbl">Waste Sites</div>
                    </div>
                    <div class="kpi-tile kpi-hazard">
                        <span class="kpi-icon">⚠️</span>
                        <div class="kpi-val" id="metric-hazards">--</div>
                        <div class="kpi-lbl">Hazards</div>
                    </div>
                    <div class="kpi-tile kpi-natural">
                        <span class="kpi-icon">🌲</span>
                        <div class="kpi-val" id="metric-natural">--</div>
                        <div class="kpi-lbl">Natural Sites</div>
                    </div>
                    <div class="kpi-tile kpi-water">
                        <span class="kpi-icon">🌊</span>
                        <div class="kpi-val" id="metric-waterways">--</div>
                        <div class="kpi-lbl">Waterways</div>
                    </div>
                </div>
            </div>

            <!-- Tabbed detail panels -->
            <div class="tab-section">
                <div class="tab-nav">
                    <button class="tab-nav-btn active" data-tab="tab-air" type="button"><i class="bi bi-wind"></i> Air</button>
                    <button class="tab-nav-btn" data-tab="tab-env" type="button"><i class="bi bi-tree"></i> Env</button>
                    <button class="tab-nav-btn" data-tab="tab-biz" type="button"><i class="bi bi-buildings"></i> Biz</button>
                    <button class="tab-nav-btn" data-tab="tab-src" type="button"><i class="bi bi-database"></i> Sources</button>
                </div>

                <!-- Air Quality tab -->
                <div class="tab-pane active" id="tab-air">
                    <div id="aq-bars">
                        <div class="aq-bar"><span class="aq-label">NO₂</span><div class="aq-track"><div class="aq-fill" id="bar-no2" style="width:0%"></div></div><span class="aq-val" id="val-no2">--</span></div>
                        <div class="aq-bar"><span class="aq-label">O₃ Ozone</span><div class="aq-track"><div class="aq-fill" id="bar-o3" style="width:0%"></div></div><span class="aq-val" id="val-o3">--</span></div>
                        <div class="aq-bar"><span class="aq-label">SO₂</span><div class="aq-track"><div class="aq-fill" id="bar-so2" style="width:0%"></div></div><span class="aq-val" id="val-so2">--</span></div>
                        <div class="aq-bar"><span class="aq-label">CO</span><div class="aq-track"><div class="aq-fill" id="bar-co" style="width:0%"></div></div><span class="aq-val" id="val-co">--</span></div>
                        <div class="aq-bar"><span class="aq-label">Dust</span><div class="aq-track"><div class="aq-fill" id="bar-dust" style="width:0%"></div></div><span class="aq-val" id="val-dust">--</span></div>
                    </div>
                    <div class="mini-kpi-row">
                        <div class="mini-kpi"><span>🌧️</span><div><div class="mk-val" id="metric-rain">--</div><div class="mk-lbl">Rain mm</div></div></div>
                        <div class="mini-kpi"><span>📡</span><div><div class="mk-val" id="metric-elevation">--</div><div class="mk-lbl">Elevation m</div></div></div>
                        <div class="mini-kpi"><span>🦋</span><div><div class="mk-val" id="metric-biodiversity">--</div><div class="mk-lbl">Species</div></div></div>
                    </div>
                </div>

                <!-- Environment tab -->
                <div class="tab-pane" id="tab-env">
                    <ul class="feature-list" id="hazard-list">
                        <li><span>No data loaded yet.</span><strong class="fl-count">--</strong></li>
                    </ul>
                    <div style="margin-top:.8rem; border-top:1px solid var(--line); padding-top:.75rem;">
                        <div style="font-size:.68rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:.5px; margin-bottom:.5rem;">🪱 Soil Data</div>
                        <div class="mini-kpi-row">
                            <div class="mini-kpi"><span>🧪</span><div><div class="mk-val" id="metric-soil-ph">--</div><div class="mk-lbl">pH</div></div></div>
                            <div class="mini-kpi"><span>🟫</span><div><div class="mk-val" id="metric-soil-carbon">--</div><div class="mk-lbl">Carbon</div></div></div>
                            <div class="mini-kpi"><span>🏺</span><div><div class="mk-val" id="metric-soil-clay">--</div><div class="mk-lbl">Clay %</div></div></div>
                        </div>
                    </div>
                    <!-- compat IDs (used by JS renderAir) -->
                    <span id="metric-osm-businesses" style="display:none;"></span>
                    <span id="metric-waste-companies" style="display:none;"></span>
                    <span id="metric-pollution" style="display:none;"></span>
                    <span id="metric-data-points" style="display:none;"></span>
                </div>

                <!-- Business tab -->
                <div class="tab-pane" id="tab-biz">
                    <ul class="feature-list">
                        <li><span><i class="bi bi-shop me-1" style="color:var(--primary-light);"></i>OSM mapped businesses</span><strong class="fl-count" id="osm-business-count">--</strong></li>
                        <li><span><i class="bi bi-recycle me-1" style="color:var(--primary-light);"></i>OSM waste companies</span><strong class="fl-count" id="osm-waste-company-count">--</strong></li>
                        <li><span><i class="bi bi-google me-1" style="color:#4285f4;"></i>Google businesses</span><strong class="fl-count" id="google-business-count">--</strong></li>
                        <li><span><i class="bi bi-google me-1" style="color:#4285f4;"></i>Google waste companies</span><strong class="fl-count" id="google-waste-company-count">--</strong></li>
                    </ul>
                    <div id="google-business-note" style="font-size:.74rem; color:var(--muted); margin-top:.65rem; background:var(--light-green); border-radius:8px; padding:.5rem .75rem; line-height:1.5;">
                        Google Places results require a configured API key.
                    </div>
                </div>

                <!-- Sources tab -->
                <div class="tab-pane" id="tab-src">
                    <ul class="source-list">
                        <li><span>Air quality &amp; AQI</span><span class="s-badge">Open-Meteo</span></li>
                        <li><span>Weather &amp; wind</span><span class="s-badge">Open-Meteo</span></li>
                        <li><span>Elevation model</span><span class="s-badge">OpenTopodata</span></li>
                        <li><span>Soil pH, carbon &amp; clay</span><span class="s-badge">SoilGrids</span></li>
                        <li><span>Biodiversity records</span><span class="s-badge">GBIF</span></li>
                        <li><span>Place name lookup</span><span class="s-badge">Nominatim</span></li>
                        <li><span>Waste, hazard, natural POIs</span><span class="s-badge">OSM / Overpass</span></li>
                        <li><span>Business &amp; waste companies</span><span class="s-badge">Google Places</span></li>
                        <li><span>Base map tiles</span><span class="s-badge">OpenStreetMap</span></li>
                    </ul>
                </div>
            </div>

        </aside>
    </div>
</div><!-- /container -->

@endsection

@push('page-scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    const DEFAULT  = { lat: 5.6037, lon: -0.1870 };
    const RADIUS   = 3000;
    const TIMEOUT  = 15000;
    const GOOGLE_BUSINESS_URL = @json(route('locality.business-data'));

    // ── DOM refs
    const statusText  = document.getElementById('status-text');
    const statusDot   = document.getElementById('status-dot');
    const statusDot2  = document.getElementById('status-dot2');
    const latInput    = document.getElementById('latitude');
    const lonInput    = document.getElementById('longitude');
    const areaInput   = document.getElementById('area-search');
    const placeName   = document.getElementById('place-name');
    const coordsDisp  = document.getElementById('coords-display');
    const riskBand    = document.getElementById('risk-band');
    const riskScore   = document.getElementById('risk-score');
    const riskSummary = document.getElementById('risk-summary');
    const hazardList  = document.getElementById('hazard-list');
    const naturalTags = document.getElementById('natural-tags');
    const alertChips  = document.getElementById('alert-chips');
    let selectedAreaName = '';

    // ── Map setup
    const map = L.map('locality-map', { zoomControl: true }).setView([DEFAULT.lat, DEFAULT.lon], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const markerLayer  = L.layerGroup().addTo(map);
    const featureLayer = L.layerGroup().addTo(map);
    let locationMarker = null;

    // ── Bouncing custom icon
    const bouncingIcon = L.divIcon({
        html: `<div class="bounce-wrapper">
                   <div class="bounce-pin">
                       <div class="pin-body"></div>
                       <div class="pin-shadow"></div>
                   </div>
               </div>`,
        className: '',
        iconSize:   [32, 50],
        iconAnchor: [16, 50],
        popupAnchor: [0, -50]
    });

    // ── AUTOCOMPLETE ─────────────────────────────────────────────────────────
    const acDrop = document.getElementById('ac-dropdown');
    let acResults = [], acFocusIdx = -1, acTimer = null;

    areaInput.addEventListener('input', () => {
        clearTimeout(acTimer);
        const q = areaInput.value.trim();
        if (q.length < 2) { closeAc(); return; }
        acTimer = setTimeout(() => fetchAcSuggestions(q), 310);
    });

    areaInput.addEventListener('keydown', e => {
        if (!acDrop.classList.contains('open')) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); acMoveFocus(1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); acMoveFocus(-1); }
        else if (e.key === 'Enter' && acFocusIdx >= 0) {
            e.preventDefault();
            if (acResults[acFocusIdx]) acSelect(acResults[acFocusIdx]);
        } else if (e.key === 'Escape') closeAc();
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.ac-wrap')) closeAc();
    });

    async function fetchAcSuggestions(query) {
        acDrop.innerHTML = '<div class="ac-msg"><span class="ac-spinner-icon">↻</span> Searching…</div>';
        openAc();
        try {
            const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=14&addressdetails=1&q=${encodeURIComponent(query)}`;
            const data = await fetchJson(url);
            acResults = Array.isArray(data) ? data : [];
            if (!acResults.length) {
                acDrop.innerHTML = '<div class="ac-msg">No results found — try a different name</div>';
                return;
            }
            renderAcDropdown(acResults);
        } catch (_) {
            acDrop.innerHTML = '<div class="ac-msg">Search failed — check your connection</div>';
        }
    }

    function renderAcDropdown(results) {
        const groups = {};
        results.forEach((r, i) => {
            const a = r.address || {};
            const key = a.municipality || a.county || a.state_district || a.region || a.state || (a.country_code || 'other').toUpperCase();
            if (!groups[key]) groups[key] = [];
            groups[key].push({ ...r, _i: i });
        });

        const groupIcon = k => {
            const l = k.toLowerCase();
            if (l.includes('metropolitan') || l.includes('city')) return '🏙️';
            if (l.includes('municipal'))  return '🏛️';
            if (l.includes('district'))   return '🗺️';
            if (l.includes('region') || l.includes('province')) return '🌍';
            return '📍';
        };

        const typeLabel = r => {
            const t = r.type || r.class || '';
            if (t === 'administrative') return 'Admin';
            if (t === 'suburb' || t === 'neighbourhood') return 'Zone';
            if (t === 'city' || t === 'town') return 'City';
            if (t === 'village') return 'Village';
            if (t === 'forest') return 'Forest';
            if (t === 'water') return 'Water';
            return t ? t.charAt(0).toUpperCase() + t.slice(1) : '';
        };

        let html = '';
        for (const [groupKey, items] of Object.entries(groups)) {
            html += `<div class="ac-group-header">${groupIcon(groupKey)} ${esc(groupKey)}</div>`;
            items.forEach(r => {
                const a = r.address || {};
                const main = r.name
                    || a.road || a.suburb || a.neighbourhood
                    || a.village || a.town || a.city
                    || r.display_name.split(',')[0];
                const sub = [
                    a.suburb || a.neighbourhood || a.village || a.town,
                    a.county || a.municipality,
                    a.country
                ].filter(Boolean).join(', ');
                const lbl = typeLabel(r);
                html += `<div class="ac-item" data-i="${r._i}">
                    <span class="ac-icon">📍</span>
                    <span class="ac-body">
                        <div class="ac-main">${esc(main)}</div>
                        <div class="ac-sub">${esc(sub.substring(0, 55))}</div>
                    </span>
                    ${lbl ? `<span class="ac-type-badge">${esc(lbl)}</span>` : ''}
                </div>`;
            });
        }

        acDrop.innerHTML = html;
        acFocusIdx = -1;

        acDrop.querySelectorAll('.ac-item').forEach(el => {
            el.addEventListener('click', () => {
                const r = acResults[+el.dataset.i];
                if (r) acSelect(r);
            });
        });
        openAc();
    }

    function acSelect(r) {
        const a = r.address || {};
        const short = r.display_name.split(',').slice(0, 2).join(',').trim();
        areaInput.value = short;
        selectedAreaName = r.display_name;
        latInput.value = parseFloat(r.lat).toFixed(6);
        lonInput.value = parseFloat(r.lon).toFixed(6);
        closeAc();
        runCheck({ lat: parseFloat(r.lat), lon: parseFloat(r.lon) });
    }

    function openAc()  { acDrop.classList.add('open'); }
    function closeAc() { acDrop.classList.remove('open'); acFocusIdx = -1; }

    function acMoveFocus(dir) {
        const items = acDrop.querySelectorAll('.ac-item');
        if (!items.length) return;
        items[acFocusIdx]?.classList.remove('ac-focused');
        acFocusIdx = Math.max(0, Math.min(items.length - 1, acFocusIdx + dir));
        items[acFocusIdx].classList.add('ac-focused');
        items[acFocusIdx].scrollIntoView({ block: 'nearest' });
    }
    // ─────────────────────────────────────────────────────────────────────────

    // ── Event listeners
    areaInput.addEventListener('keydown', e => {
        if (acDrop.classList.contains('open')) return;
        if (e.key === 'Enter') {
            e.preventDefault();
            searchAreaName();
        }
    });
    document.getElementById('locate-button').addEventListener('click', locateUser);
    document.getElementById('check-button').addEventListener('click', () => runCheck(readInputs()));
    document.getElementById('clear-button').addEventListener('click', () => {
        areaInput.value = '';
        selectedAreaName = '';
        latInput.value = DEFAULT.lat;
        lonInput.value = DEFAULT.lon;
        runCheck(DEFAULT);
    });

    // Initial load
    runCheck(DEFAULT);

    // ─────────────────────────────────────────────────────────────────────────
    function locateUser() {
        if (!navigator.geolocation) { setStatus('Geolocation not available.', 'high'); return; }
        setStatus('Locating your position…', 'moderate');
        navigator.geolocation.getCurrentPosition(pos => {
            const c = {
                lat: +pos.coords.latitude.toFixed(6),
                lon: +pos.coords.longitude.toFixed(6)
            };
            latInput.value = c.lat;
            lonInput.value = c.lon;
            selectedAreaName = '';
            runCheck(c);
        }, () => setStatus('Location permission denied or unavailable.', 'high'), {
            enableHighAccuracy: true, timeout: 12000, maximumAge: 60000
        });
    }

    function readInputs() {
        return { lat: parseFloat(latInput.value), lon: parseFloat(lonInput.value) };
    }

    async function searchAreaName() {
        const query = areaInput.value.trim();
        if (!query) { setStatus('Enter an area name to search.', 'high'); return; }
        setStatus(`Searching for "${query}"…`, 'moderate');
        try {
            const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&addressdetails=1&q=${encodeURIComponent(query)}`;
            const results = await fetchJson(url);
            if (!Array.isArray(results) || !results.length) {
                setStatus(`No area found for "${query}". Try a more specific name.`, 'high');
                return;
            }
            const m = results[0];
            const c = { lat: parseFloat(m.lat), lon: parseFloat(m.lon) };
            selectedAreaName = m.display_name || query;
            latInput.value = c.lat.toFixed(6);
            lonInput.value = c.lon.toFixed(6);
            runCheck(c);
        } catch (_) {
            setStatus('Area search failed. Please try again.', 'high');
        }
    }

    async function runCheck(c) {
        if (!isFinite(c.lat) || !isFinite(c.lon)) { setStatus('Enter valid latitude and longitude.', 'high'); return; }

        setStatus('Fetching air quality, weather, hazards and natural features…', 'moderate');
        setStatus('Fetching air quality, weather, hazards, soil, elevation and biodiversity data...', 'moderate');
        setMapPin(c);
        coordsDisp.textContent = `${c.lat.toFixed(5)}, ${c.lon.toFixed(5)}`;

        const [place, air, weather, overpass, businessData, elevation, soil, biodiversity] = await Promise.allSettled([
            fetchJson(nominatimUrl(c)),
            fetchJson(airUrl(c)),
            fetchJson(weatherUrl(c)),
            fetchOverpass(c),
            fetchJson(businessDataUrl(c)),
            fetchJson(elevationUrl(c)),
            fetchJson(soilUrl(c)),
            fetchJson(biodiversityUrl(c))
        ]);

        const pd = val(place);
        const ad = val(air);
        const wd = val(weather);
        const feats = normalise(val(overpass));

        renderPlace(pd, c);
        renderAir(ad, wd, feats);
        renderFeatures(feats);
        renderFeatureMarkers(feats);
        renderNaturalTags(feats);
        renderAlerts(ad, feats, wd);
        renderWeather(wd);
        renderBusinessData(feats, val(businessData));
        renderEnvironmentalData(ad, wd, feats, val(elevation), val(soil), val(biodiversity));
        renderSummaryAndHealth(pd, ad, feats, wd);
        setStatus('Locality check complete — ' + (pd ? pd.display_name.substring(0, 60) + '…' : `${c.lat}, ${c.lon}`), 'low');
    }

    // ── URLs
    const nominatimUrl = ({lat,lon}) =>
        `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}&zoom=16&addressdetails=1`;

    const businessDataUrl = ({lat,lon}) => {
        const params = new URLSearchParams({
            lat,
            lon,
            place: selectedAreaName || placeName.textContent || ''
        });

        return `${GOOGLE_BUSINESS_URL}?${params.toString()}`;
    };

    const airUrl = ({lat,lon}) =>
        `https://air-quality-api.open-meteo.com/v1/air-quality?latitude=${lat}&longitude=${lon}`
        + `&hourly=us_aqi,european_aqi,pm10,pm2_5,carbon_monoxide,nitrogen_dioxide,sulphur_dioxide,ozone,dust,uv_index`
        + `&forecast_days=1&timezone=auto`;

    const weatherUrl = ({lat,lon}) =>
        `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}`
        + `&current=temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m,wind_direction_10m`
        + `&timezone=auto`;

    const elevationUrl = ({lat,lon}) =>
        `https://api.opentopodata.org/v1/srtm90m?locations=${lat},${lon}`;

    const soilUrl = ({lat,lon}) =>
        `https://rest.isric.org/soilgrids/v2.0/properties/query?lon=${lon}&lat=${lat}`
        + `&property=phh2o&property=soc&property=clay&depth=0-5cm&value=mean`;

    const biodiversityUrl = ({lat,lon}) => {
        const delta = 0.025;
        const west = lon - delta;
        const east = lon + delta;
        const south = lat - delta;
        const north = lat + delta;
        const polygon = `POLYGON((${west} ${south},${east} ${south},${east} ${north},${west} ${north},${west} ${south}))`;

        return `https://api.gbif.org/v1/occurrence/search?limit=0&geometry=${encodeURIComponent(polygon)}`;
    };

    async function fetchOverpass({lat,lon}) {
        const q = `[out:json][timeout:25];
        (
          node(around:${RADIUS},${lat},${lon})["amenity"~"waste_disposal|recycling|waste_transfer_station|fuel"];
          way(around:${RADIUS},${lat},${lon}) ["amenity"~"waste_disposal|recycling|waste_transfer_station|fuel"];
          node(around:${RADIUS},${lat},${lon})["landuse"~"landfill|industrial|quarry|brownfield|forest|meadow|farmland|wetland"];
          way(around:${RADIUS},${lat},${lon}) ["landuse"~"landfill|industrial|quarry|brownfield|forest|meadow|farmland|wetland"];
          node(around:${RADIUS},${lat},${lon})["man_made"~"wastewater_plant|works"];
          way(around:${RADIUS},${lat},${lon}) ["man_made"~"wastewater_plant|works"];
          node(around:${RADIUS},${lat},${lon})["natural"~"wood|water|wetland|scrub|heath|grassland"];
          way(around:${RADIUS},${lat},${lon}) ["natural"~"wood|water|wetland|scrub|heath|grassland"];
          node(around:${RADIUS},${lat},${lon})["power"~"plant|generator|substation"];
          way(around:${RADIUS},${lat},${lon}) ["power"~"plant|generator|substation"];
          node(around:${RADIUS},${lat},${lon})["hazard"];
          way(around:${RADIUS},${lat},${lon}) ["hazard"];
          node(around:${RADIUS},${lat},${lon})["waterway"~"river|stream|canal|drain"];
          way(around:${RADIUS},${lat},${lon}) ["waterway"~"river|stream|canal|drain"];
          node(around:${RADIUS},${lat},${lon})["shop"];
          way(around:${RADIUS},${lat},${lon}) ["shop"];
          node(around:${RADIUS},${lat},${lon})["office"];
          way(around:${RADIUS},${lat},${lon}) ["office"];
          node(around:${RADIUS},${lat},${lon})["craft"];
          way(around:${RADIUS},${lat},${lon}) ["craft"];
          node(around:${RADIUS},${lat},${lon})["tourism"];
          way(around:${RADIUS},${lat},${lon}) ["tourism"];
          node(around:${RADIUS},${lat},${lon})["amenity"];
          way(around:${RADIUS},${lat},${lon}) ["amenity"];
        );
        out center tags;`;
        return fetchJson(`https://overpass-api.de/api/interpreter?data=${encodeURIComponent(q)}`);
    }

    async function fetchJson(url) {
        const ctrl = new AbortController();
        const t    = setTimeout(() => ctrl.abort(), TIMEOUT);
        try {
            const r = await fetch(url, { signal: ctrl.signal });
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            return r.json();
        } finally { clearTimeout(t); }
    }

    const val = r => r.status === 'fulfilled' ? r.value : null;

    // ── Map pin
    function setMapPin({lat,lon}) {
        map.setView([lat,lon], 14);
        if (!locationMarker) {
            locationMarker = L.marker([lat,lon], { icon: bouncingIcon }).addTo(markerLayer);
        } else {
            locationMarker.setLatLng([lat,lon]);
        }
        locationMarker.bindPopup(`<strong>Selected locality</strong><br>${lat.toFixed(5)}, ${lon.toFixed(5)}`).openPopup();
    }

    // ── Render helpers
    function renderPlace(d, c) {
        const txt = d && d.display_name ? d.display_name : `${c.lat.toFixed(5)}, ${c.lon.toFixed(5)}`;
        placeName.textContent = txt;
    }

    function latestAir(d) {
        const h = d && d.hourly ? d.hourly : {};
        const now = Date.now();
        const idx = Array.isArray(h.time)
            ? h.time.reduce((b,t,i) => Math.abs(new Date(t)-now) < Math.abs(new Date(h.time[b])-now) ? i : b, 0)
            : -1;
        const at = k => idx >= 0 && Array.isArray(h[k]) ? h[k][idx] : null;
        return { usAqi:at('us_aqi'), pm25:at('pm2_5'), pm10:at('pm10'),
                 no2:at('nitrogen_dioxide'), o3:at('ozone'), so2:at('sulphur_dioxide'),
                 co:at('carbon_monoxide'), dust:at('dust'), uv:at('uv_index') };
    }

    function renderAir(ad, wd, feats) {
        const a = latestAir(ad);
        const wx = wd && wd.current ? wd.current : null;
        const waste   = feats.filter(f=>f.group==='waste').length;
        const wasteCompanies = feats.filter(f=>f.group==='waste_company').length;
        const businesses = feats.filter(f=>f.group==='business').length;
        const hazards = feats.filter(f=>f.group==='hazard').length + feats.filter(f=>f.group==='pollution').length;
        const natural = feats.filter(f=>f.group==='natural' || f.group==='water').length;
        const wind    = wx ? Number(wx.wind_speed_10m||0) : 0;
        const lowWind = wind < 7 ? 6 : 0;
        const score   = Math.min(100, Math.round((Number(a.usAqi||0)*0.45)+(Number(a.pm25||0)*1.4)+(waste*7)+(hazards*5)+(lowWind)));

        document.getElementById('metric-aqi').innerHTML     = a.usAqi !== null ? a.usAqi : '--';
        document.getElementById('metric-pm25').innerHTML    = a.pm25  !== null ? `${(+a.pm25).toFixed(1)}<span class="m-unit">µg</span>` : '--';
        document.getElementById('metric-pm10').innerHTML    = a.pm10  !== null ? `${(+a.pm10).toFixed(1)}<span class="m-unit">µg</span>` : '--';
        document.getElementById('metric-waste').textContent = waste;
        document.getElementById('metric-hazards').textContent = hazards;
        document.getElementById('metric-natural').textContent = natural;
        document.getElementById('metric-osm-businesses').textContent = businesses;
        document.getElementById('metric-waste-companies').textContent = wasteCompanies;

        riskScore.textContent = score + '/100';
        const level = score >= 65 ? 'high' : score >= 35 ? 'moderate' : 'low';
        riskBand.className = 'risk-band ' + level;
        const riskPillEl = document.getElementById('risk-pill');
        if (riskPillEl) {
            riskPillEl.textContent = level === 'high' ? '🔴 High Risk' : level === 'moderate' ? '🟡 Moderate' : '🟢 Low Risk';
            riskPillEl.className = 'risk-pill ' + level;
        }
        riskSummary.textContent = level === 'high'
            ? 'Higher environmental pressure — inspect air quality, waste and hazard markers.'
            : level === 'moderate'
            ? 'Moderate pressure detected. Review pollutant readings and mapped features.'
            : 'Lower locality pressure based on current open data.';

        // AQ bars (normalise relative to rough safe limits)
        const bars = [
            ['no2',  a.no2,  100, 'val-no2',  'bar-no2'],
            ['o3',   a.o3,   120, 'val-o3',   'bar-o3'],
            ['so2',  a.so2,   20, 'val-so2',  'bar-so2'],
            ['co',   a.co, 10000, 'val-co',   'bar-co'],
            ['dust', a.dust, 500, 'val-dust', 'bar-dust'],
        ];
        bars.forEach(([,val,max,vid,bid]) => {
            document.getElementById(vid).textContent = val !== null ? (+val).toFixed(1) : '--';
            document.getElementById(bid).style.width = val !== null ? Math.min(100,(+val/max)*100)+'%' : '0%';
        });

        // sync both status dots
        [statusDot, statusDot2].forEach(d => { d.className = 'status-dot ' + level; });
    }

    function renderFeatures(feats) {
        const groups = {
            'Waste management sites': feats.filter(f=>f.group==='waste').length,
            'Waste collection companies': feats.filter(f=>f.group==='waste_company').length,
            'Mapped businesses': feats.filter(f=>f.group==='business').length,
            'Pollution / industry points': feats.filter(f=>f.group==='pollution').length,
            'Natural features (forest / wetland / water)': feats.filter(f=>f.group==='natural'||f.group==='water').length,
            'Waterways (rivers / streams)': feats.filter(f=>f.group==='waterway').length,
            'Tagged hazards': feats.filter(f=>f.group==='hazard').length,
            'Other mapped features': feats.filter(f=>f.group==='other').length,
        };
        const icons = {'Waste management sites':'♻️','Waste collection companies':'♻️','Mapped businesses':'🏢','Pollution / industry points':'🏭',
            'Natural features (forest / wetland / water)':'🌲','Waterways (rivers / streams)':'🌊',
            'Tagged hazards':'⚠️','Other mapped features':'📍'};
        hazardList.innerHTML = Object.entries(groups)
            .map(([l,v]) => `<li><span><span class="fl-icon">${icons[l]||'•'}</span>${l}</span><strong class="fl-count">${v}</strong></li>`)
            .join('');
    }

    function renderNaturalTags(feats) {
        const tags = [...new Set(feats.map(f => {
            const t = f.tags;
            return t.natural || t.waterway || t.landuse || t.amenity || null;
        }).filter(Boolean))].slice(0, 12);

        const cls = v => v.match(/water|river|stream|canal/) ? 'water'
                       : v.match(/wood|forest|wetland|scrub|meadow/) ? 'natural'
                       : v.match(/landfill|quarry|industrial|brownfield/) ? 'hazard'
                       : v.match(/fuel|works|plant|substation/) ? 'industry'
                       : '';

        naturalTags.innerHTML = tags.length
            ? tags.map(t => `<span class="tag-pill ${cls(t)}">${t}</span>`).join('')
            : '<span class="tag-pill">No natural features found in 3 km radius</span>';
    }

    function renderAlerts(ad, feats, wd) {
        const a   = latestAir(ad);
        const aqi = Number(a.usAqi || 0);
        const wx  = wd && wd.current ? wd.current : null;

        const alerts = [];
        if (aqi >= 101)  alerts.push({type:'bad',  icon:'💨', msg:`AQI ${aqi} — Unhealthy for sensitive groups`});
        else if (aqi > 0) alerts.push({type:'ok', icon:'✅', msg:`AQI ${aqi} — Air quality acceptable`});
        if (feats.filter(f=>f.group==='hazard').length > 0)
            alerts.push({type:'warn', icon:'⚠️', msg:`${feats.filter(f=>f.group==='hazard').length} hazard(s) mapped within 3 km`});
        if (feats.filter(f=>f.group==='waste').length > 5)
            alerts.push({type:'warn', icon:'♻️', msg:`High waste site density (${feats.filter(f=>f.group==='waste').length} sites)`});
        if (wx && Number(wx.wind_speed_10m||0) < 5)
            alerts.push({type:'warn', icon:'🌬️', msg:'Low wind speed — pollutants may accumulate'});
        if (!alerts.length)
            alerts.push({type:'ok', icon:'🌿', msg:'No critical environmental alerts for this locality'});

        alertChips.innerHTML = alerts
            .map(a => `<div class="alert-chip ${a.type}">${a.icon} ${a.msg}</div>`)
            .join('');
    }

    function renderWeather(wd) {
        const w = wd && wd.current ? wd.current : null;
        document.getElementById('wx-temp').textContent = w ? Math.round(w.temperature_2m||0)+'°' : '--';
        document.getElementById('wx-wind').textContent = w ? Math.round(w.wind_speed_10m||0) : '--';
        document.getElementById('wx-hum').textContent  = w ? Math.round(w.relative_humidity_2m||0)+'%' : '--';
    }

    function renderBusinessData(feats, googleData) {
        const osmBusinesses = feats.filter(f=>f.group==='business').length;
        const osmWasteCompanies = feats.filter(f=>f.group==='waste_company').length;

        document.getElementById('osm-business-count').textContent = osmBusinesses;
        document.getElementById('osm-waste-company-count').textContent = osmWasteCompanies;

        if (!googleData || googleData.available === false) {
            document.getElementById('google-business-count').textContent = 'Key needed';
            document.getElementById('google-waste-company-count').textContent = 'Key needed';
            document.getElementById('google-business-note').textContent =
                'Google Places search is wired in, but GOOGLE_PLACES_API_KEY is empty. OSM/Overpass counts are shown above.';
            return;
        }

        document.getElementById('google-business-count').textContent = googleData.business_count ?? 0;
        document.getElementById('google-waste-company-count').textContent = googleData.waste_collection_company_count ?? 0;
        document.getElementById('google-business-note').textContent = googleData.message || 'Google Places search complete.';
    }

    function renderEnvironmentalData(ad, wd, feats, elevationData, soilData, biodiversityData) {
        const a = latestAir(ad);
        const w = wd && wd.current ? wd.current : {};
        const elevation = elevationData && elevationData.results && elevationData.results[0]
            ? elevationData.results[0].elevation
            : null;
        const ph = soilMetric(soilData, 'phh2o');
        const soc = soilMetric(soilData, 'soc');
        const clay = soilMetric(soilData, 'clay');
        const waterways = feats.filter(f=>f.group==='waterway').length;
        const pollution = feats.filter(f=>f.group==='pollution').length;
        const visiblePoints = feats.length
            + (a.usAqi !== null ? 1 : 0)
            + (a.pm25 !== null ? 1 : 0)
            + (elevation !== null ? 1 : 0)
            + (ph !== null ? 1 : 0)
            + (soc !== null ? 1 : 0)
            + (clay !== null ? 1 : 0)
            + (biodiversityData && typeof biodiversityData.count === 'number' ? 1 : 0);

        document.getElementById('metric-elevation').innerHTML = elevation !== null
            ? `${Math.round(elevation)}<span class="m-unit">m</span>`
            : '--';
        document.getElementById('metric-uv').textContent = a.uv !== null ? (+a.uv).toFixed(1) : '--';
        document.getElementById('metric-rain').innerHTML = w && w.precipitation !== undefined
            ? `${(+w.precipitation).toFixed(1)}<span class="m-unit">mm</span>`
            : '--';
        document.getElementById('metric-biodiversity').textContent =
            biodiversityData && typeof biodiversityData.count === 'number' ? biodiversityData.count.toLocaleString() : '--';
        document.getElementById('metric-soil-ph').textContent = ph !== null ? (ph / 10).toFixed(1) : '--';
        document.getElementById('metric-soil-carbon').innerHTML = soc !== null
            ? `${(soc / 10).toFixed(1)}<span class="m-unit">g/kg</span>`
            : '--';
        document.getElementById('metric-soil-clay').innerHTML = clay !== null
            ? `${(clay / 10).toFixed(1)}<span class="m-unit">%</span>`
            : '--';
        document.getElementById('metric-waterways').textContent = waterways;
        document.getElementById('metric-pollution').textContent = pollution;
        document.getElementById('metric-data-points').textContent = visiblePoints.toLocaleString();
    }

    function soilMetric(data, name) {
        const layers = data && data.properties && Array.isArray(data.properties.layers)
            ? data.properties.layers
            : [];
        const layer = layers.find(l => l.name === name);
        const depth = layer && Array.isArray(layer.depths)
            ? (layer.depths.find(d => d.label === '0-5cm') || layer.depths[0])
            : null;

        return depth && depth.values && depth.values.mean !== undefined ? depth.values.mean : null;
    }

    function renderFeatureMarkers(feats) {
        featureLayer.clearLayers();
        const colMap = { waste:'#1a5c2a', waste_company:'#0f7c44', business:'#5b6ee1', pollution:'#b97c05', hazard:'#c0392b', natural:'#0288d1', water:'#0277bd', waterway:'#1565c0' };
        feats.slice(0, 80).forEach(f => {
            if (!f.lat || !f.lon) return;
            const col = colMap[f.group] || '#888';
            L.circleMarker([f.lat,f.lon], { radius:7, color:col, weight:2, fillColor:col, fillOpacity:.35 })
             .bindPopup(`<strong>${esc(f.label)}</strong><br><em>${esc(f.group)}</em>`)
             .addTo(featureLayer);
        });
    }

    function normalise(d) {
        if (!d || !Array.isArray(d.elements)) return [];
        return d.elements.map(el => {
            const t = el.tags || {};
            const lat = el.lat || (el.center && el.center.lat);
            const lon = el.lon || (el.center && el.center.lon);
            const label = t.name || t.shop || t.office || t.craft || t.tourism || t.amenity || t.landuse || t.natural || t.waterway || t.man_made || t.power || t.hazard || 'Feature';
            const group = classify(t);
            return {...el, tags:t, lat, lon, label, group};
        }).filter(f => isFinite(f.lat) && isFinite(f.lon));
    }

    function classify(t) {
        const s = `${t.name||''} ${t.amenity||''} ${t.landuse||''} ${t.man_made||''} ${t.power||''} ${t.hazard||''} ${t.natural||''} ${t.waterway||''} ${t.shop||''} ${t.office||''} ${t.craft||''} ${t.tourism||''}`.toLowerCase();
        if (s.match(/waste collection|waste management|sanitation|refuse|recycling company|cleaning service/)) return 'waste_company';
        if (s.match(/waste|recycling|landfill|wastewater/)) return 'waste';
        if (s.match(/industrial|quarry|brownfield|works|fuel|plant|generator|substation/)) return 'pollution';
        if (s.match(/hazard/)) return 'hazard';
        if (s.match(/waterway|river|stream|canal|drain/)) return 'waterway';
        if (s.match(/water|lake|pond/)) return 'water';
        if (s.match(/wood|forest|wetland|scrub|heath|grass|meadow|natural/)) return 'natural';
        if (t.shop || t.office || t.craft || t.tourism || t.amenity) return 'business';
        return 'other';
    }

    function setStatus(msg, level) {
        statusText.textContent = msg;
        [statusDot, statusDot2].forEach(d => {
            d.style.background = level==='low' ? 'var(--risk-low)' : level==='high' ? 'var(--risk-high)' : 'var(--risk-mod)';
        });
    }

    const esc = v => String(v).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));

    // ── TAB SWITCHING
    document.querySelectorAll('.tab-nav-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-nav-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        });
    });

    // ── MOBILE NAV TOGGLE
    const navToggle = document.getElementById('navToggle');
    const navMobile = document.getElementById('navMobileMenu');
    if (navToggle && navMobile) {
        if (window.innerWidth <= 640) navToggle.style.display = 'flex';
        window.addEventListener('resize', () => {
            navToggle.style.display = window.innerWidth <= 640 ? 'flex' : 'none';
            if (window.innerWidth > 640) navMobile.style.display = 'none';
        });
        navToggle.addEventListener('click', () => {
            const open = navMobile.style.display === 'block';
            navMobile.style.display = open ? 'none' : 'block';
            navToggle.innerHTML = open ? '<i class="bi bi-list"></i>' : '<i class="bi bi-x-lg"></i>';
        });
    }

    // ── AREA SUMMARY + HEALTH RISKS (generated from fetched data)
    function renderSummaryAndHealth(pd, ad, feats, wd) {
        const a   = latestAir(ad);
        const wx  = wd && wd.current ? wd.current : null;
        const aqi = Number(a.usAqi || 0);
        const pm25 = Number(a.pm25 || 0);
        const wasteCount   = feats.filter(f => f.group === 'waste').length;
        const hazardCount  = feats.filter(f => f.group === 'hazard').length;
        const naturalCount = feats.filter(f => f.group === 'natural' || f.group === 'water').length;
        const waterwayCount= feats.filter(f => f.group === 'waterway').length;
        const wind = wx ? Number(wx.wind_speed_10m || 0) : 0;
        const placeTxt = pd && pd.display_name ? pd.display_name.split(',').slice(0,3).join(', ') : 'this area';

        // ── Summary text
        const aqDesc = aqi === 0 ? 'Air quality data unavailable'
            : aqi <= 50  ? `good air quality (AQI ${aqi})`
            : aqi <= 100 ? `moderate air quality (AQI ${aqi})`
            : aqi <= 150 ? `unhealthy air for sensitive groups (AQI ${aqi})`
            : `unhealthy air quality (AQI ${aqi})`;
        const windDesc = wind < 5 ? 'low wind conditions that may trap pollutants' : `wind at ${Math.round(wind)} km/h aiding dispersion`;
        const wasteDesc = wasteCount > 0 ? `${wasteCount} waste site${wasteCount > 1 ? 's' : ''} mapped within 3 km` : 'no significant waste sites detected';
        const natDesc = naturalCount > 0 ? `${naturalCount} natural feature${naturalCount > 1 ? 's' : ''} (forests, wetlands, water bodies)` : 'limited natural features';

        document.getElementById('area-summary-text').textContent =
            `${placeTxt} currently shows ${aqDesc}. There are ${windDesc}. ${wasteDesc.charAt(0).toUpperCase() + wasteDesc.slice(1)}. The area contains ${natDesc}` +
            (waterwayCount > 0 ? ` and ${waterwayCount} mapped waterway${waterwayCount > 1 ? 's' : ''}` : '') +
            (hazardCount > 0 ? `. ${hazardCount} environmental hazard${hazardCount > 1 ? 's' : ''} noted within proximity.` : '.');

        // ── Health risk scoring helpers
        const risk = score => score >= 70 ? { cls: 'hr-high', lbl: 'High Risk' }
                            : score >= 40 ? { cls: 'hr-mod',  lbl: 'Moderate' }
                            :               { cls: 'hr-low',  lbl: 'Low Risk' };
        const setRisk = (id, score) => {
            const el = document.getElementById(id);
            if (!el) return;
            const r = risk(score);
            el.textContent = r.lbl;
            el.className = 'hr-val ' + r.cls;
        };

        const aqScore   = Math.min(100, aqi * 0.7);
        const wasteScore= Math.min(100, wasteCount * 12 + hazardCount * 10);
        const waterScore= Math.min(100, (wasteCount * 8) + (hazardCount * 8) + (aqi > 100 ? 20 : 0));
        const floodScore= Math.min(100, (waterwayCount * 15) + (wasteCount * 5) + (pm25 > 50 ? 20 : 0));
        const animalScore = Math.min(100, aqScore * 0.6 + wasteScore * 0.4);
        const plantScore  = Math.min(100, (aqi > 100 ? 50 : aqi * 0.3) + (hazardCount * 10));

        setRisk('hr-human-val',  Math.round(aqScore * 0.5 + wasteScore * 0.5));
        setRisk('hr-animals-val', Math.round(animalScore));
        setRisk('hr-water-val',  Math.round(waterScore));
        setRisk('hr-flood-val',  Math.round(floodScore));
        setRisk('hr-waste-val',  Math.round(wasteScore));
        setRisk('hr-plants-val', Math.round(plantScore));
    }
</script>
@endpush

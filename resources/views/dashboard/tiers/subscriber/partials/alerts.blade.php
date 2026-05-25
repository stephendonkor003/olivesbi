<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-bell-fill" style="color:var(--accent);"></i> Subscriber Alert Center</div>
        <a class="panel-action" href="{{ route('dashboard.subscriber.newsletter') }}">Manage topics</a>
    </div>

    <div class="tool-grid">
        @foreach ($alertRules ?? [] as $rule)
            <div class="tool-card">
                <div class="tool-card-icon"><i class="bi bi-broadcast-pin"></i></div>
                <div class="tool-card-title">{{ $rule['name'] }}</div>
                <div class="tool-card-desc">{{ $rule['area'] }}</div>
                <div style="margin-top:.65rem;display:inline-flex;align-items:center;gap:.35rem;border-radius:999px;background:#fff;border:1px solid var(--line);padding:.24rem .55rem;font-size:.68rem;font-weight:800;color:var(--primary);">
                    <i class="bi bi-check-circle-fill" style="color:var(--accent);"></i> {{ $rule['status'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-info-circle" style="color:var(--accent);"></i> How Alerts Work</div>
    </div>
    <ul class="focus-list">
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>Alerts are based on your newsletter topics, locality checks, and public environmental signals.</span></li>
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>As the overhaul continues, this page will store saved areas, thresholds, and notification preferences.</span></li>
        <li class="focus-item"><i class="bi bi-check-circle-fill"></i><span>For now, use Check Locality to search a place and Newsletter to select the topics you care about.</span></li>
    </ul>
</div>

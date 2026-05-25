<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-geo-alt-fill" style="color:var(--accent);"></i> Locality Research</div>
        <a class="panel-action" href="{{ route('locality.check') }}">Open checker</a>
    </div>

    <div style="background:var(--soft);border:1px solid var(--line);border-radius:14px;padding:1rem;margin-bottom:1rem;">
        <div style="font-family:'Poppins',sans-serif;font-weight:800;color:var(--primary);margin-bottom:.35rem;">Search any area, town, coordinate, or property location.</div>
        <div class="info-text">The public checker gives subscribers a map-led view of air quality, waste pressure, business density, environmental hazards, and nearby public datasets.</div>
        <a href="{{ route('locality.check') }}" style="display:inline-flex;align-items:center;gap:.45rem;margin-top:.85rem;background:var(--accent);color:#fff;text-decoration:none;border-radius:9px;padding:.62rem .95rem;font-weight:800;font-size:.84rem;">
            <i class="bi bi-search"></i> Launch locality checker
        </a>
    </div>

    <div class="tool-grid">
        @foreach ($sourceGroups ?? [] as $group => $sources)
            <div class="tool-card">
                <div class="tool-card-icon"><i class="bi bi-database-check"></i></div>
                <div class="tool-card-title">{{ $group }}</div>
                <div class="tool-card-desc">{{ implode(', ', $sources) }}</div>
            </div>
        @endforeach
    </div>
</div>

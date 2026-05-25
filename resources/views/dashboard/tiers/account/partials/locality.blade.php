<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-geo-alt-fill" style="color:var(--accent);"></i> Locality Intelligence</div>
        <a class="panel-action" href="{{ route('locality.check') }}">Open public checker</a>
    </div>

    <div style="background:var(--soft);border:1px solid var(--line);border-radius:14px;padding:1rem;margin-bottom:1rem;">
        <div style="font-family:'Poppins',sans-serif;font-weight:800;color:var(--primary);margin-bottom:.35rem;">Research environmental conditions near your assets.</div>
        <div class="info-text">Use the locality checker to search towns, coordinates, property locations, business areas, and surrounding environmental risk signals.</div>
        <a href="{{ route('locality.check') }}" style="display:inline-flex;align-items:center;gap:.45rem;margin-top:.85rem;background:var(--accent);color:#fff;text-decoration:none;border-radius:9px;padding:.62rem .95rem;font-weight:800;font-size:.84rem;">
            <i class="bi bi-search"></i> Launch locality checker
        </a>
    </div>

    <div class="tool-grid">
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-wind"></i></div>
            <div class="tool-card-title">Air quality</div>
            <div class="tool-card-desc">Check public air-quality and health-risk indicators near your areas.</div>
        </div>
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-recycle"></i></div>
            <div class="tool-card-title">Waste pressure</div>
            <div class="tool-card-desc">Review nearby waste collection, waste company, and landfill signals.</div>
        </div>
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-water"></i></div>
            <div class="tool-card-title">Water and wetlands</div>
            <div class="tool-card-desc">Research rivers, wetlands, Ramsar areas, and biodiversity constraints.</div>
        </div>
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-shop"></i></div>
            <div class="tool-card-title">Business density</div>
            <div class="tool-card-desc">Understand commercial activity around properties and proposed business areas.</div>
        </div>
    </div>
</div>

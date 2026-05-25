@php
    $businesses = $accountData['businesses'] ?? collect();
@endphp

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-shop-window" style="color:var(--accent);"></i> My Businesses</div>
        <a class="panel-action" href="{{ route('dashboard.olives-account.bills') }}">View bills</a>
    </div>

    <div class="tool-grid">
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-briefcase-fill"></i></div>
            <div class="tool-card-title">{{ number_format($businesses->count()) }} linked businesses</div>
            <div class="tool-card-desc">Business records connected to your personal Olives Account.</div>
        </div>
        <div class="tool-card">
            <div class="tool-card-icon"><i class="bi bi-file-earmark-check"></i></div>
            <div class="tool-card-title">Permits and compliance</div>
            <div class="tool-card-desc">Review permit numbers, locations, and assembly records.</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div class="panel-title"><i class="bi bi-list-check" style="color:var(--accent);"></i> Business Records</div>
    </div>

    <div class="info-list">
        @forelse ($businesses as $business)
            <div class="info-item">
                <div class="info-dot"></div>
                <div style="width:100%;">
                    <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                        <div>
                            <div style="font-family:'Poppins',sans-serif;font-weight:800;color:#17251b;">{{ $business->business_name }}</div>
                            <div class="info-text">{{ \App\Support\GlobalLocation::addressFor($business) }}</div>
                            <div class="info-time">{{ \App\Support\GlobalLocation::regionFor($business) }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800;color:var(--primary);">{{ $business->permit_no ?: 'No permit number' }}</div>
                            <div class="info-time">{{ $business->assembly->name ?? 'Assembly not recorded' }}</div>
                        </div>
                    </div>
                    <div style="margin-top:.55rem;display:flex;gap:.75rem;flex-wrap:wrap;">
                        <span class="info-time">Account: {{ $business->account_no ?? 'N/A' }}</span>
                        <span class="info-time">Status: {{ $business->status_of_business ?? 'N/A' }}</span>
                        <a class="panel-action" href="{{ route('dashboard.olives-account.businesses.show', $business) }}"><i class="bi bi-eye"></i> View business</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:2.4rem 1rem;color:var(--muted);">
                <i class="bi bi-shop" style="font-size:2rem;display:block;margin-bottom:.6rem;opacity:.45;"></i>
                No business records are linked to your account yet.
            </div>
        @endforelse
    </div>
</div>

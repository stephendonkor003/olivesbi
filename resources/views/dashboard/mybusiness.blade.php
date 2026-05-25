@extends('dashboard.tiers.partials.tier-page-layout')

@section('page-title', 'My Business')
@section('page-scope', 'My Business')

@section('page-head-extra')
<style>
    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead th {
        font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px;
        color:var(--primary); background:#f8fdf9; padding:.65rem .9rem;
        border-bottom:2px solid var(--line); white-space:nowrap;
    }
    .data-table tbody td {
        padding:.65rem .9rem; font-size:.84rem; color:#374151;
        border-bottom:1px solid var(--line); vertical-align:middle;
    }
    .data-table tbody tr:hover { background:#f8fdf9; }
    .data-table tbody tr:last-child td { border-bottom:0; }
    .tbl-search {
        border:1.5px solid rgba(26,92,42,.18); border-radius:9px;
        height:38px; padding:0 .85rem; font-size:.84rem; color:#1a2e1e;
        outline:none; width:260px; transition:border .2s;
    }
    .tbl-search:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(76,199,106,.1); }
    .tbl-badge {
        display:inline-flex; align-items:center; gap:.3rem;
        font-size:.7rem; font-weight:700; padding:.18rem .55rem; border-radius:6px;
        background:var(--soft); color:var(--primary);
    }
    .tbl-link {
        color:var(--primary); font-weight:700; font-size:.8rem;
        text-decoration:none; display:inline-flex; align-items:center; gap:.25rem;
        padding:.22rem .6rem; border-radius:6px; background:var(--soft);
        transition:background .15s;
    }
    .tbl-link:hover { background:#d4edda; color:var(--primary); }
    .summary-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.85rem; margin-bottom:1.25rem; }
    .sum-card {
        background:#fff; border:1px solid var(--line); border-radius:14px;
        padding:1rem 1.1rem; box-shadow:0 1px 8px rgba(26,92,42,.04);
    }
    .sum-label { font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:var(--muted); margin-bottom:.35rem; }
    .sum-value { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.45rem; color:#17251b; line-height:1; }
    .sum-sub   { font-size:.72rem; color:var(--muted); margin-top:.25rem; }
    .empty-state { text-align:center; padding:3rem 1rem; color:var(--muted); }
    .empty-state i { font-size:2.5rem; margin-bottom:.75rem; display:block; opacity:.35; }
    .table-wrap { overflow-x:auto; }
</style>
@endsection

@section('page-content')

{{-- Hero --}}
<div class="tier-hero">
    <div>
        <div class="hero-badge"><i class="bi bi-briefcase"></i> My Account</div>
        <div class="hero-title">My Business</div>
        <div class="hero-sub">All businesses registered under your Olives BI account.</div>
    </div>
    <div style="font-size:.78rem;color:rgba(255,255,255,.55);text-align:right;white-space:nowrap;">
        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:.2rem;">Total</div>
        {{ count($customerData['businesses'] ?? []) }} Records
    </div>
</div>

@if (auth()->check())

{{-- Summary cards --}}
<div class="summary-grid">
    <div class="sum-card">
        <div class="sum-label">Total Businesses</div>
        <div class="sum-value">{{ count($customerData['businesses'] ?? []) }}</div>
        <div class="sum-sub">Registered businesses</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">Account Status</div>
        <div class="sum-value" style="font-size:1rem; color:#1a5c2a;">Active</div>
        <div class="sum-sub">Good standing</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">Check Locality</div>
        <div class="sum-value" style="font-size:.88rem;">
            <a href="{{ route('locality.check') }}" style="color:var(--accent); text-decoration:none;">Environmental Scores →</a>
        </div>
        <div class="sum-sub">View area risk data</div>
    </div>
</div>

{{-- Table panel --}}
<div class="panel" style="padding:0; overflow:hidden;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--line);">
        <div class="panel-title" style="margin:0;"><i class="bi bi-briefcase-fill" style="color:var(--accent);"></i> Business Records</div>
        <input class="tbl-search" type="text" id="biz-search" placeholder="Search businesses…">
    </div>
    <div class="table-wrap">
        <table class="data-table" id="biz-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Business Name</th>
                    <th>Business Type</th>
                    <th>Owner</th>
                    <th>Assembly</th>
                    <th>Created By</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($customerData['businesses'] ?? [] as $index => $business)
                @php
                    $fullname = trim(($business->customer->first_name ?? '') . ' ' . ($business->customer->last_name ?? ''));
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span class="tbl-badge"><i class="bi bi-briefcase-fill"></i> {{ $business->business_name }}</span></td>
                    <td>{{ $business->businessType->name ?? 'N/A' }}</td>
                    <td>{{ $fullname ?: 'N/A' }}</td>
                    <td>{{ $business->assembly->name ?? 'N/A' }}</td>
                    <td style="color:var(--muted);">{{ $business->createdBy->name ?? 'N/A' }}</td>
                    <td style="color:var(--muted); font-size:.78rem;">{{ optional($business->created_at)->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-briefcase"></i><p>No businesses found on your account.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@elseif (false)
    <div class="panel" style="text-align:center; padding:3rem;">
        <i class="bi bi-lock-fill" style="font-size:2rem; color:var(--muted); display:block; margin-bottom:.75rem;"></i>
        <p style="color:var(--muted); font-size:.88rem;">You do not have permission to view this section.</p>
        <a href="{{ route('dashboard.entry') }}" style="color:var(--accent); font-weight:700; font-size:.85rem;">← Back to Dashboard</a>
    </div>
@endif

@endsection

@push('page-scripts')
<script>
    document.getElementById('biz-search').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#biz-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush

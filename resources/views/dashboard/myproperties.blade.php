@extends('dashboard.tiers.partials.tier-page-layout')

@section('page-title', 'My Properties')
@section('page-scope', 'My Properties')

@php
    $customerData = array_merge([
        'properties' => collect(),
        'total' => '0.00',
    ], $customerData ?? []);
@endphp

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
    .data-table tfoot th {
        font-size:.75rem; font-weight:700; color:var(--primary);
        background:#f8fdf9; padding:.65rem .9rem; border-top:2px solid var(--line);
    }
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
    .empty-state p { font-size:.88rem; }
    .table-wrap { overflow-x:auto; }
</style>
@endsection

@section('page-content')

{{-- Hero --}}
<div class="tier-hero">
    <div>
        <div class="hero-badge"><i class="bi bi-buildings"></i> My Account</div>
        <div class="hero-title">My Properties</div>
        <div class="hero-sub">All properties registered under your Olives BI account.</div>
    </div>
    <div style="font-size:.78rem;color:rgba(255,255,255,.55);text-align:right;white-space:nowrap;">
        <div style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.4);margin-bottom:.2rem;">Total Value</div>
        GHS {{ $customerData['total'] ?? '0.00' }}
    </div>
</div>

@if (auth()->check())

{{-- Summary cards --}}
<div class="summary-grid">
    <div class="sum-card">
        <div class="sum-label">Total Properties</div>
        <div class="sum-value">{{ count($customerData['properties']) }}</div>
        <div class="sum-sub">Registered records</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">Total Ratable Value</div>
        <div class="sum-value" style="font-size:1.1rem;">GHS {{ $customerData['total'] }}</div>
        <div class="sum-sub">Combined value</div>
    </div>
    <div class="sum-card">
        <div class="sum-label">Account Status</div>
        <div class="sum-value" style="font-size:1rem; color:#1a5c2a;">Active</div>
        <div class="sum-sub">Good standing</div>
    </div>
</div>

{{-- Table panel --}}
<div class="panel" style="padding:0; overflow:hidden;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--line);">
        <div class="panel-title" style="margin:0;"><i class="bi bi-buildings-fill" style="color:var(--accent);"></i> Property Records</div>
        <input class="tbl-search" type="text" id="prop-search" placeholder="Search properties…">
    </div>
    <div class="table-wrap">
        <table class="data-table" id="prop-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Property No.</th>
                    <th>Owner Account</th>
                    <th>Owner Name</th>
                    <th>Ratable Value (GHS)</th>
                    <th>Assembly</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($customerData['properties'] as $index => $property)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span class="tbl-badge"><i class="bi bi-house-fill"></i> {{ $property->property_number }}</span></td>
                    <td style="font-family:'Space Mono',monospace; font-size:.78rem;">{{ $property->customer->account_number ?? 'N/A' }}</td>
                    <td>{{ trim(($property->customer->first_name ?? '') . ' ' . ($property->customer->last_name ?? '')) ?: 'N/A' }}</td>
                    <td style="font-weight:700; color:var(--primary);">{{ number_format($property->ratable_value, 2) }}</td>
                    <td>{{ $property->assembly->name ?? 'N/A' }}</td>
                    <td style="color:var(--muted); font-size:.78rem;">{{ optional($property->created_at)->format('d M Y') }}</td>
                    <td>
                        <a class="tbl-link" href="{{ route('citizens.viewProperty', $property) }}">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty-state"><i class="bi bi-buildings"></i><p>No properties found on your account.</p></div></td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align:right; color:var(--muted);">Total Ratable Value</th>
                    <th>GHS {{ $customerData['total'] }}</th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
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
    document.getElementById('prop-search').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#prop-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush
